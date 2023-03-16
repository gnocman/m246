<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Model\Config\Source;

use Magento\Framework\Exception\LocalizedException;

/**
 * Telephone number must be 10 digits and start with 0 or +84
 */
class Telephone extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @throws LocalizedException
     */
    public function beforeSave($object)
    {
        if ($object->getData($this->getAttribute()->getAttributeCode())) {
            $value = $object->getData($this->getAttribute()->getAttributeCode());
            if (str_starts_with($value, '+84')) {
                $value = '0' . substr($value, 3);
            }

            if (strlen($value) == 10 && str_starts_with($value, '0')) {
                $object->setData($this->getAttribute()->getAttributeCode(), $value);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Telephone number must be 10 digits and start with 0 or +84')
                );
            }
        }

        return $this;
    }
}
