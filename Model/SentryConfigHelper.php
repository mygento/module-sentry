<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

class SentryConfigHelper
{
    public function __construct(private Config $config) {}

    /**
     * Gets sentry config values from scope config and saves to config object's properties
     */
    public function prepareSentryConfig(): void
    {
        if ($this->config->isEnabled()) {
            $this->config->getLogLevel();
            $this->config->getEnvironment();
            $this->config->getHub();
            $this->config->getRelease();
        }
    }
}
