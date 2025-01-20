<?php

/**
 * @author Mygento Team
 * @copyright 2017-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Sentry
 */

namespace Mygento\Sentry\Model\Source;

class Loglevel implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        $levels = [];
        foreach (\Monolog\Logger::getLevels() as $level => $value) {
            $levels[$value] = $level;
        }

        return $levels;
    }
}
