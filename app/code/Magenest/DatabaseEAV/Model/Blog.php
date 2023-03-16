<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 *
 */
class Blog extends AbstractModel implements IdentityInterface
{
    /**
     *
     */
    const CACHE_TAG = 'magenest_blog';
    /**
     * @var string
     */
    protected $_cacheTag = 'magenest_blog';
    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_blog';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\DatabaseEAV\Model\ResourceModel\Blog');
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getSelect()
    {
        $values = [];
        return $values;
    }

}

