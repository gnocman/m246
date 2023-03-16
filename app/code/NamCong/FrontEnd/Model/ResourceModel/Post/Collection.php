<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NamCong\FrontEnd\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use NamCong\FrontEnd\Model\Post;
use NamCong\FrontEnd\Model\ResourceModel\Post as RS;

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
        $this->_init(Post::class, RS::class);
    }
}
