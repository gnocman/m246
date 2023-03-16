<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Model\ResourceModel\Blog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 *
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'blog_id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'name';
    /**
     * @var string
     */
    protected $_eventObject = 'description';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\DatabaseEAV\Model\Blog', 'Magenest\DatabaseEAV\Model\ResourceModel\Blog');
    }

}

