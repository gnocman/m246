<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ValidInvalid implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('Invalid')
            ],
            [
                'value' => '1',
                'label' => __('Valid')
            ],
        ];
    }
}
