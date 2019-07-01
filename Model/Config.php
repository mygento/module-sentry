<?php

/**
 * @author Mygento Team
 * @copyright 2017-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

class Config
{
    private $connection;

    private $loglevel;

    private $environment;

    private $enabled;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface\Proxy
     */
    private $scopeConfig;

    private $hub;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

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

    public function getEnvironment()
    {
        if ($this->loglevel !== null) {
            $this->environment = $this->scopeConfig->getValue(
                'sentry/general/environment',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $this->environment;
    }

    public function isEnabled(): bool
    {
        if ($this->enabled === null) {
            $this->enabled = $this->scopeConfig->getValue(
                'sentry/general/enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return (bool) $this->enabled;
    }

    public function getHub()
    {
        if ($this->hub === null) {
            \Sentry\init(['dsn' => $this->getConnection()]);
            $this->hub = \Sentry\State\Hub::getCurrent();
        }

        return $this->hub;
    }
}
