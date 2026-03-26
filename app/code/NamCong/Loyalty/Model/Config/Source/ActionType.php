<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Config\Source;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use Magento\Framework\Data\OptionSourceInterface;

class ActionType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => HistoryInterface::ACTION_ORDER, 'label' => __('Order')],
            ['value' => HistoryInterface::ACTION_REGISTRATION, 'label' => __('Registration')],
            ['value' => HistoryInterface::ACTION_REVIEW, 'label' => __('Review')],
        ];
    }
}
