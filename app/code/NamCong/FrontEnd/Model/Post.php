<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NamCong\FrontEnd\Model;

use Magento\Framework\Model\AbstractModel;
use NamCong\FrontEnd\Model\ResourceModel\Post as Md;

/**
 * Init ResourceModel
 */
class Post extends AbstractModel
{
    /**
     * Init ResourceModel
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Md::class);
    }
}
