<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dev\RestApi\Model\Api;

use Dev\RestApi\Api\RequestItemInterface;

use Magento\Framework\DataObject;

class RequestItem extends DataObject implements RequestItemInterface
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
     * Get Description
     *
     * @return string
     */
    public function getDescription(): string
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
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): mixed
    {
        return $this->setData(self::DATA_DESCRIPTION, $description);
    }
}
