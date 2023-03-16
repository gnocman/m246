<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\Test2\Model;

use Magento\Framework\Model\AbstractModel;

/**
 *
 */
class Director extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Test2\Model\ResourceModel\Director');
    }

}
