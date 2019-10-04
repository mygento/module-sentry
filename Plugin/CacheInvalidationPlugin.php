<?php

/**
 * @author Mygento Team
 * @copyright 2017-2019 Mygento (https://www.mygento.ru)
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
     */
    public function beforeClean()
    {
        $this->sentryConfigHelper->prepareSentryConfig();
    }
}