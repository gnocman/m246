<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CustomUI\Model\ResourceModel\Film;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SmartOSC\CustomUI\Model\Film;
use SmartOSC\CustomUI\Model\ResourceModel\Film as ResourceModel;

/**
 * Collection model and resource model
 */
class Collection extends AbstractCollection
{
    /**
     * Collection model and resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Film::class, ResourceModel::class);
    }
}
