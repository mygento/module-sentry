<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Plugin;

class HttpCatcher
{
    /**
     * @var \Magento\Framework\Logger\Monolog
     */
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
     * @param \Magento\Framework\App\Http $subject
     * @param \Magento\Framework\App\Bootstrap $bootstrap
     * @param \Exception $exception
     */
    public function beforeCatchException(
        \Magento\Framework\App\Http $subject,
        \Magento\Framework\App\Bootstrap $bootstrap,
        \Exception $exception
    ) {
        $this->logger->critical($exception);
    }
}
