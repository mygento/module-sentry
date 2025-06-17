<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model;

use Magento\Framework\App\View\Deployment\Version\StorageInterface;

class ReleaseIdentifier
{
    private ?string $cachedValue = null;
    private StorageInterface $versionStorage;

    public function __construct(
        StorageInterface $versionStorage
    ) {
        $this->versionStorage = $versionStorage;
    }

    public function getValue(): ?string
    {
        if ($this->cachedValue) {
            return $this->cachedValue;
        }

        try {
            $this->cachedValue = (string) $this->versionStorage->load();
        } catch (\Throwable $e) {
            unset($e);
        }

        return $this->cachedValue;
    }
}
