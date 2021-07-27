<?php

/**
 * @author Mygento Team
 * @copyright 2017-2019 Mygento (https://www.mygento.ru)
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
     * @var string
     */
    private $loglevel;

    /**
     * @var string
     */
    private $environment;

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
     * @return string
     */
    public function getLogLevel()
    {
        if ($this->loglevel === null) {
            $this->loglevel = $this->scopeConfig->getValue(
                'sentry/general/loglevel',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->loglevel;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        if ($this->environment === null) {
            $this->environment = $this->scopeConfig->getValue(
                'sentry/general/environment',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->environment;
    }

    /**
     * @return bool
     */
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
            ]);
            $this->hub = \Sentry\SentrySdk::getCurrentHub();
        }

        return $this->hub;
    }
}
