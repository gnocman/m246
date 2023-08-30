<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Model;

use Magento\Framework\Model\AbstractModel;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Orders as ResourceModel;

class Orders extends AbstractModel
{
    /**
     * Init ResourceModel
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
