<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use NamCong\Loyalty\Api\Data\RewardInterface;

class RewardType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => RewardInterface::TYPE_DISCOUNT,      'label' => __('Discount')],
            ['value' => RewardInterface::TYPE_FREE_SHIPPING, 'label' => __('Free Shipping')],
            ['value' => RewardInterface::TYPE_GIFT_PRODUCT,  'label' => __('Gift Product')],
        ];
    }

    public function toArray(): array
    {
        return [
            RewardInterface::TYPE_DISCOUNT      => __('Discount'),
            RewardInterface::TYPE_FREE_SHIPPING => __('Free Shipping'),
            RewardInterface::TYPE_GIFT_PRODUCT  => __('Gift Product'),
        ];
    }
}
