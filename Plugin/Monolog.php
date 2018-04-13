<?php
/**
 * @author Mygento
 * @package Mygento_Sentry
 */
namespace Mygento\Sentry\Plugin;

class Monolog
{
    /* @var \Mygento\Sentry\Model\SentryHandler */
    private $factory;

    /* @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        \Mygento\Sentry\Model\SentryHandler $factory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->factory = $factory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     */
    public function beforeAddRecord(
        \Magento\Framework\Logger\Monolog $subject
    ) {
        if (!$this->scopeConfig->getValue(
            'sentry/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return;
        }
        foreach ($subject->getHandlers() as $handler) {
            if ($handler instanceof \Monolog\Handler\RavenHandler) {
                return;
            }
        }
        $handler = $this->factory->create();
        $subject->pushHandler($handler);
    }
}
