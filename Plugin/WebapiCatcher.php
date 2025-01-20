<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Plugin;

class WebapiCatcher
{
    /**
     * @var \Mygento\Sentry\Model\Config
     */
    private $config;

    /**
     * @param \Mygento\Sentry\Model\Config $config
     */
    public function __construct(
        \Mygento\Sentry\Model\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\Webapi\ErrorProcessor $subject
     * @param mixed $result
     */
    public function afterApiShutdownFunction(
        \Magento\Framework\Webapi\ErrorProcessor $subject,
        $result
    ) {
        $this->config->getHub()->captureLastError();

        return $result;
    }
}
