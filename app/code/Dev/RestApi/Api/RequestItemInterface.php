<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Dev\RestApi\Api;

interface RequestItemInterface
{
    public const DATA_ID = 'id';
    public const DATA_DESCRIPTION = 'description';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Get Description
     *
     * @return string
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
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description);
}
