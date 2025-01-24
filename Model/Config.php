<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var string
     */
    private $connection;

    /**
     * @var int
     */
    private $loglevel;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $errorMessageFilterPattern;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var \Sentry\State\HubInterface
     */
    private $hub;

    /**
     * @var bool
     */
    private $isExceptionsExcludeActive;

    /**
     * @var string|null
     */
    private $release = null;

    private ScopeConfigInterface $scopeConfig;
    private ReleaseIdentifier $releaseIdentifier;

    /**
     * @var float|null
     */
    private $profilesRate = null;

    /**
     * @var float|null
     */
    private $tracesRate = null;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ReleaseIdentifier $releaseIdentifier
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->releaseIdentifier = $releaseIdentifier;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->scopeConfig->getValue(
                'sentry/general/connection',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            );
        }

        return $this->connection;
    }

    /**
     * @return int
     */
    public function getLogLevel(): int
    {
        if ($this->loglevel === null) {
            $this->loglevel = (int) $this->scopeConfig->getValue(
                'sentry/general/loglevel',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            );
        }

        return $this->loglevel;
    }

    /**
     * @return float|null
     */
    public function getProfilesSampleRate()
    {
        if ($this->profilesRate === null) {
            $this->profilesRate = $this->scopeConfig->isSetFlag(
                'sentry/general/profile_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            ) ? (float) $this->scopeConfig->getValue(
                'sentry/general/profiles_sample_rate',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            ) : null;
        }

        return $this->profilesRate;
    }

    /**
     * @return float|null
     */
    public function getTracesSampleRate()
    {
        if ($this->tracesRate === null) {
            $this->tracesRate = $this->scopeConfig->isSetFlag(
                'sentry/general/traces_enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            ) ? (float) $this->scopeConfig->getValue(
                'sentry/general/traces_sample_rate',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            ) : null;
        }

        return $this->tracesRate;
    }

    public function getEnvironment()
    {
        if ($this->environment === null) {
            $this->environment = $this->scopeConfig->getValue(
                'sentry/general/environment',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            );
        }

        return $this->environment;
    }

    public function getErrorMessageFilterPattern(): ?string
    {
        if ($this->errorMessageFilterPattern === null) {
            $this->errorMessageFilterPattern = $this->scopeConfig->getValue(
                'sentry/general/error_message_filter_pattern',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            );
        }

        return $this->errorMessageFilterPattern;
    }

    public function isEnabled(): bool
    {
        if ($this->enabled === null) {
            try {
                $this->enabled = $this->scopeConfig->getValue(
                    'sentry/general/enabled',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                );
            } catch (\DomainException $e) {
                unset($e);

                return false;
            }
        }

        return (bool) $this->enabled;
    }

    /**
     * @return \Sentry\State\HubInterface
     */
    public function getHub()
    {
        if ($this->hub === null) {
            \Sentry\init([
                'dsn' => $this->getConnection(),
                'traces_sample_rate' => $this->getTracesSampleRate(),
                'profiles_sample_rate' => $this->getProfilesSampleRate(),
                'environment' => $this->getEnvironment() ?? null,
                'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
                    $pattern = $this->getErrorMessageFilterPattern();
                    $message = $event->getMessage();

                    try {
                        if ($pattern && $message && preg_match($pattern, $message)) {
                            return null;
                        }
                    } catch (\Throwable $th) {
                        // In case $pattern is invalid, preg_match will throw an exception
                        // Let's silently ignore that then, and pass through the event.
                        return $event;
                    }

                    return $event;
                },
            ]);
            $this->hub = \Sentry\SentrySdk::getCurrentHub();
        }

        return $this->hub;
    }

    /**
     * @return bool
     */
    public function isExceptionsExcludeActive(): bool
    {
        if ($this->isExceptionsExcludeActive === null) {
            $this->isExceptionsExcludeActive = $this->scopeConfig->isSetFlag(
                'sentry/general/exclude_exceptions',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            );
        }

        return $this->isExceptionsExcludeActive;
    }

    public function getRelease(): string
    {
        if ($this->release == null) {
            $this->release = $this->releaseIdentifier->getValue();
        }

        return $this->release;
    }
}
