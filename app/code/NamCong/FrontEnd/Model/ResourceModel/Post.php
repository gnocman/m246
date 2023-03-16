<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NamCong\FrontEnd\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Init model
 */
class Post extends AbstractDb
{
    /**
     * Init table & id table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('namcong_movie', 'movie_id');
    }
}
