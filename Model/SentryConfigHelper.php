<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

class SentryConfigHelper
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Gets sentry config values from scope config and saves to config object's properties
     */
    public function prepareSentryConfig()
    {
        $this->config->getConnection();
        $this->config->getLogLevel();
        $this->config->isEnabled();
        $this->config->getEnvironment();
        $this->config->getHub();
        $this->config->getRelease();
    }
}
