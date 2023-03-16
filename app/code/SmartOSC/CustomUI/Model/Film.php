<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CustomUI\Model;

use Magento\Framework\Model\AbstractModel;
use SmartOSC\CustomUI\Model\ResourceModel\Film as ResourceModel;

/**
 * Init ResourceModel
 */
class Film extends AbstractModel
{
    /**
     * Init ResourceModel
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
