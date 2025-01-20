<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State;
use Magento\Framework\App\View\Deployment\Version\StorageInterface;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Psr\Log\LoggerInterface;

class ReleaseIdentifier
{
    private ?string $cachedValue = null;
    private State $appState;
    private StorageInterface $versionStorage;
    private DeploymentConfig $deploymentConfig;
    private LoggerInterface $logger;

    public function __construct(
        State $appState,
        StorageInterface $versionStorage,
        DeploymentConfig $deploymentConfig,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->versionStorage = $versionStorage;
        $this->deploymentConfig = $deploymentConfig;
        $this->logger = $logger;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     * @return string
     */
    public function getValue(): string
    {
        if (!$this->cachedValue) {
            $this->cachedValue = $this->readValue($this->appState->getMode());
        }

        return $this->cachedValue;
    }

    /**
     * @param string $appMode
     *
     * @throws FileSystemException
     * @throws RuntimeException
     * @return string
     */
    private function readValue(string $appMode): string
    {
        $result = $this->versionStorage->load();
        if (!$result) {
            if (
                $appMode == State::MODE_PRODUCTION
                && !$this->deploymentConfig->getConfigData(
                    ConfigOptionsListConstants::CONFIG_PATH_SCD_ON_DEMAND_IN_PRODUCTION
                )
            ) {
                $this->logger->critical('Can not load static content version.');

                throw new \UnexpectedValueException(
                    'Unable to retrieve deployment version of static files from the file system.'
                );
            }
            $result = $this->generateVersion();
            $this->versionStorage->save($result);
        }

        return $result;
    }

    /**
     * Generate version of static content.
     *
     * @return string
     */
    private function generateVersion(): string
    {
        return (string) time();
    }
}
