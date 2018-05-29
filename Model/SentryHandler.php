<?php

/**
 * @author Mygento Team
 * @copyright 2017-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

class SentryHandler extends \Monolog\Handler\RavenHandler
{
    /* @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface\Proxy $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $connection = $this->scopeConfig->getValue(
            'sentry/general/connection',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $level = $this->scopeConfig->getValue(
            'sentry/general/loglevel',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $client = new \Raven_Client($connection);
        parent::__construct($client, $level);
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        if (!$this->scopeConfig->getValue(
            'sentry/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return false;
        }
        return parent::isHandling($record);
    }
}
