<?php

/**
 * @author Mygento Team
 * @copyright 2017-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Plugin;

class WebapiCatcher
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
     * @param mixed $result
     */
    public function afterApiShutdownFunction(
        \Magento\Framework\Webapi\ErrorProcessor $subject,
        $result
    ) {
        $fatalErrorFlag = E_ERROR |
            E_USER_ERROR |
            E_PARSE |
            E_CORE_ERROR |
            E_COMPILE_ERROR |
            E_RECOVERABLE_ERROR;
        $error = error_get_last();
        if ($error && $error['type'] & $fatalErrorFlag) {
            $exception = new \ErrorException(
                @$error['message'],
                0,
                @$error['type'],
                @$error['file'],
                @$error['line']
            );
            $this->logger->critical($exception);
        }
        return $result;
    }
}
