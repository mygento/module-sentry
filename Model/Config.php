<?php

/**
 * @author Mygento Team
 * @copyright 2017-2021 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Sentry\State\HubInterface
     */
    private $hub;

    /**
     * @var bool
     */
    private $isGraphQLExceptionsExcluded;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->scopeConfig->getValue(
                'sentry/general/connection',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->loglevel;
    }

    public function getEnvironment(): ?string
    {
        if ($this->environment === null) {
            $this->environment = $this->scopeConfig->getValue(
                'sentry/general/environment',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->environment;
    }

    public function getErrorMessageFilterPattern(): ?string
    {
        if ($this->errorMessageFilterPattern === null) {
            $this->errorMessageFilterPattern = $this->scopeConfig->getValue(
                'sentry/general/error_message_filter_pattern',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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
    public function isGraphQLExceptionsExcluded(): bool
    {
        if ($this->isGraphQLExceptionsExcluded === null) {
            $this->isGraphQLExceptionsExcluded = $this->scopeConfig->isSetFlag(
                'sentry/general/exclude_graphql_exceptions',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->isGraphQLExceptionsExcluded;
    }
}
