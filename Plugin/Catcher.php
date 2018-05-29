<?php

/**
 * @author Mygento Team
 * @copyright 2017-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Plugin;

class Catcher
{
    /* @var \Magento\Framework\Logger\Monolog */
    private $logger;

    /**
     * @param \Magento\Framework\Logger\Monolog $logger
     */
    public function __construct(
        \Magento\Framework\Logger\Monolog $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCatchException(
        \Magento\Framework\App\Http $subject,
        \Magento\Framework\App\Bootstrap $bootstrap,
        \Exception $exception
    ) {
        $this->logger->critical($exception);
    }
}
