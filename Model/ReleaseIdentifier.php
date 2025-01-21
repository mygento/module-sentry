<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

use Magento\Framework\App\View\Deployment\Version\StorageInterface;
use Psr\Log\LoggerInterface;

class ReleaseIdentifier
{
    private ?string $cachedValue = null;
    private StorageInterface $versionStorage;
    private LoggerInterface $logger;

    public function __construct(
        StorageInterface $versionStorage,
        LoggerInterface $logger
    ) {
        $this->versionStorage = $versionStorage;
        $this->logger = $logger;
    }

    public function getValue(): ?string
    {
        if ($this->cachedValue) {
            return $this->cachedValue;
        }

        try {
            $this->cachedValue = (string) $this->versionStorage->load();
        } catch (\Throwable $e) {
            $this->logger->critical('Can not load static content version.', ['exception' => $e]);
        }

        return $this->cachedValue;
    }
}
