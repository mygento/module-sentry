<?php

/**
 * @author Mygento Team
 * @copyright 2017-2021 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

class SentryHandler extends AbstractProcessingHandler
{
    /**
     * @var \Mygento\Sentry\Model\Config
     */
    private $config;

    /**
     * @var array
     */
    private $excludedGraphQlExceptions;

    /**
     * @param \Mygento\Sentry\Model\Config $config
     * @param bool $bubble
     * @param array $excludedGraphQlExceptions
     */
    public function __construct(
        Config $config,
        bool $bubble = true,
        array $excludedGraphQlExceptions = []
    ) {
        $this->config = $config;
        parent::__construct();
        $this->bubble = $bubble;
        $this->excludedGraphQlExceptions = $excludedGraphQlExceptions;
    }

    /**
     * Gets minimum logging level at which this handler will be triggered.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->config->getLogLevel();
    }

    /**
     * @inheritdoc
     */
    public function isHandling(array $record): bool
    {
        if (!$this->config->isEnabled() || $this->isGraphQLExceptionExcluded($record)) {
            return false;
        }

        $this->setLevel(
            Logger::getLevelName(
                /** @phpstan-ignore-next-line */
                $this->config->getLogLevel()
            )
        );

        return parent::isHandling($record);
    }

    /*
     * @inheritDoc

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter('[%channel%] %message%');
    }
    */

    /**
     * @inheritdoc
     */
    protected function write(array $record): void
    {
        $event = Event::createEvent();
        $event->setLevel($this->getLogLevel($record['level']));
        $event->setMessage($record['message']);
        $event->setLogger(sprintf('monolog.%s', $record['channel']));

        $hint = new EventHint();

        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
            $hint->exception = $record['context']['exception'];
        }

        $this->getHub()->withScope(function (Scope $scope) use ($record, $event, $hint): void {
            $scope->setExtra('monolog.channel', $record['channel']);
            $scope->setExtra('monolog.level', $record['level_name']);

            $this->getHub()->captureEvent($event, $hint);
        });
    }

    /**
     * Translates the Monolog level into the Sentry severity.
     *
     * @param int $level The Monolog log level
     */
    private function getLogLevel(int $level): Severity
    {
        switch ($level) {
            case Logger::DEBUG:
                return Severity::debug();
            case Logger::WARNING:
                return Severity::warning();
            case Logger::ERROR:
                return Severity::error();
            case Logger::CRITICAL:
            case Logger::ALERT:
            case Logger::EMERGENCY:
                return Severity::fatal();
            case Logger::INFO:
            case Logger::NOTICE:
            default:
                return Severity::info();
        }
    }

    /**
     * @return HubInterface
     */
    private function getHub()
    {
        return $this->config->getHub();
    }

    /**
     * @param array $record
     * @return bool
     */
    private function isGraphQLExceptionExcluded(array $record)
    {
        if (!$this->config->isGraphQLExceptionsExcluded()) {
            return false;
        }

        $mainException = $record['context']['exception'] ?? null;

        if (!is_object($mainException)) {
            return false;
        }
        $exception = $mainException->getPrevious();

        if (!is_object($exception)) {
            return false;
        }

        return in_array(get_class($exception), $this->excludedGraphQlExceptions);
    }
}
