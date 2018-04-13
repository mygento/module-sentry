<?php
/**
 * @author Mygento
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

class SentryHandler
{
    /* @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function create()
    {
        $connection = $this->scopeConfig->getValue(
            'sentry/general/connection',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $level = $this->scopeConfig->getValue(
            'sentry/general/loglevel',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$connection) {
            return;
        }
        $client = new \Raven_Client($connection);
        $handler = new \Monolog\Handler\RavenHandler($client, $level);
        $handler->setFormatter(
            new \Monolog\Formatter\LineFormatter("%message% %context% %extra%\n")
        );
        return $handler;
    }
}
