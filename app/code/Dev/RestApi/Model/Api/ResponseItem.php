<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dev\RestApi\Model\Api;

use Dev\RestApi\Api\ResponseItemInterface;

use Magento\Framework\DataObject;

class ResponseItem extends DataObject implements ResponseItemInterface
{
    /**
     * Get ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->_getData(self::DATA_ID);
    }

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku(): string
    {
        return $this->_getData(self::DATA_SKU);
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_getData(self::DATA_NAME);
    }

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription(): string|null
    {
        return $this->_getData(self::DATA_DESCRIPTION);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id): mixed
    {
        return $this->setData(self::DATA_ID, $id);
    }

    /**
     * Set Sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku): mixed
    {
        return $this->setData(self::DATA_SKU, $sku);
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): mixed
    {
        return $this->setData(self::DATA_NAME, $name);
    }

    /**
     * Set Description
     *
     * @param string|null $description
     * @return $this
     */
    public function setDescription(string|null $description): mixed
    {
        return $this->setData(self::DATA_DESCRIPTION, $description);
    }
}
