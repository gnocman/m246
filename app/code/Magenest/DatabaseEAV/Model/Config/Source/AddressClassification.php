<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 *
 */
class AddressClassification extends AbstractSource implements OptionSourceInterface, SourceInterface
{
    /**
     * @return array[]
     */
    public function getAllOptions(): array
    {
        return [
            [
                'value' => 1,
                'label' => 'Miền Bắc',
            ],
            [
                'value' => 2,
                'label' => 'Miền Trung',
            ],
            [
                'value' => 3,
                'label' => 'Miền Nam',
            ],
        ];
    }
}
