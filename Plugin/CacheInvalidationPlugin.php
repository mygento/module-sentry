<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Plugin;

use Mygento\Sentry\Model\SentryConfigHelper;

class CacheInvalidationPlugin
{
    /**
     * @var SentryConfigHelper
     */
    private $sentryConfigHelper;

    /**
     * @param SentryConfigHelper $sentryConfigHelper
     */
    public function __construct(SentryConfigHelper $sentryConfigHelper)
    {
        $this->sentryConfigHelper = $sentryConfigHelper;
    }

    /**
     * Before cache invalidation
     *
     * @param \Magento\Framework\App\Config $subject
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeClean($subject)
    {
        $this->sentryConfigHelper->prepareSentryConfig();
    }
}
