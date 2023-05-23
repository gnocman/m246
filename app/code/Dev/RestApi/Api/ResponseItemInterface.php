<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Dev\RestApi\Api;

interface ResponseItemInterface
{
    public const DATA_ID = 'id';
    public const DATA_SKU = 'sku';
    public const DATA_NAME = 'name';
    public const DATA_DESCRIPTION = 'description';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id);

    /**
     * Set Sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku);

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description);
}
