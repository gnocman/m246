<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Model\ResourceModel\Orders;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SmartOSC\MagentoIntegration\Model\Orders;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Orders as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Collection model and resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Orders::class, ResourceModel::class);
    }
}
