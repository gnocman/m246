<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use NamCong\Loyalty\Service\LevelManager;

class Level implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => LevelManager::LEVEL_BRONZE, 'label' => __('Bronze')],
            ['value' => LevelManager::LEVEL_SILVER, 'label' => __('Silver')],
            ['value' => LevelManager::LEVEL_GOLD,   'label' => __('Gold')],
        ];
    }
}
