<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Plugin;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;
use Magento\Framework\Logger\Monolog;

class HttpCatcher
{
    public function __construct(
        private Monolog $logger,
    ) {}

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function beforeCatchException(
        Http $subject,
        Bootstrap $bootstrap,
        \Exception $exception,
    ): void {
        $this->logger->critical($exception);
    }
}
