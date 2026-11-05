<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

class SentryHandler extends AbstractProcessingHandler
{
    private const EXCEPTION_DEEPEST_LEVEL = 5;

    /**
     * @var \Mygento\Sentry\Model\Config
     */
    private $config;

    /**
     * @var array<string,string>
     */
    private $excludedExceptions;

    /**
     * @param array<string,string> $excludedExceptions
     */
    public function __construct(
        Config $config,
        bool $bubble = true,
        array $excludedExceptions = [],
    ) {
        $this->config = $config;
        parent::__construct();
        $this->bubble = $bubble;
        $this->excludedExceptions = $excludedExceptions;
    }

    /**
     * @inheritDoc
     */
    public function getLevel(): Level
    {
        return Level::fromValue(
            /** @phpstan-ignore-next-line */
            $this->config->getLogLevel(),
        );
    }

    /**
     * @inheritdoc
     */
    public function isHandling(LogRecord $record): bool
    {
        if (!$this->config->isEnabled() || $this->isRecordWithExcludedException($record)) {
            return false;
        }

        $this->setLevel(
            Level::fromValue(
                /** @phpstan-ignore-next-line */
                $this->config->getLogLevel(),
            ),
        );

        return parent::isHandling($record);
    }

    /**
     * @inheritdoc
     */
    protected function write(LogRecord $record): void
    {
        $event = Event::createEvent();
        $event->setLevel($this->getLogLevel($record->level));
        $event->setMessage($record->message);
        $event->setLogger(sprintf('monolog.%s', $record->channel));
        $release = $this->config->getRelease();
        if ($release) {
            $event->setRelease($release);
        }

        $hint = new EventHint();

        if (isset($record->context['exception']) && $record->context['exception'] instanceof \Throwable) {
            $hint->exception = $record->context['exception'];
        }

        $this->getHub()->withScope(function (Scope $scope) use ($record, $event, $hint): void {
            $scope->setExtra('monolog.channel', $record->channel);
            $scope->setExtra('monolog.level', $record->level->name);
            $this->getHub()->captureEvent($event, $hint);
        });
    }

    /**
     * Translates the Monolog level into the Sentry severity.
     */
    private function getLogLevel(Level $level): Severity
    {
        switch ($level) {
            case Level::Debug:
                return Severity::debug();
            case Level::Warning:
                return Severity::warning();
            case Level::Error:
                return Severity::error();
            case Level::Critical:
            case Level::Alert:
            case Level::Emergency:
                return Severity::fatal();
            case Level::Info:
            case Level::Notice:
            default:
                return Severity::info();
        }
    }

    private function getHub(): HubInterface
    {
        return $this->config->getHub();
    }

    private function isRecordWithExcludedException(LogRecord $record): bool
    {
        if (!$this->config->isExceptionsExcludeActive()) {
            return false;
        }

        $mainException = $record->context['exception'] ?? null;

        if (!is_object($mainException)) {
            return false;
        }

        if (in_array(get_class($mainException), $this->excludedExceptions)) {
            return true;
        }

        for ($i = 1; $i <= self::EXCEPTION_DEEPEST_LEVEL; $i++) {
            $exception = $mainException->getPrevious();

            if (!is_object($exception)) {
                return false;
            }

            if (in_array(get_class($exception), $this->excludedExceptions)) {
                return true;
            }

            $mainException = $exception;
        }

        return false;
    }
}
