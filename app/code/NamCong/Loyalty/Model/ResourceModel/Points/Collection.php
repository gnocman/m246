<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\ResourceModel\Points;

use NamCong\Loyalty\Model\Points;
use NamCong\Loyalty\Model\ResourceModel\Points as PointsResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'point_id';
    protected $_eventPrefix = 'namcong_loyalty_points_collection';

    protected function _construct(): void
    {
        $this->_init(Points::class, PointsResource::class);
    }
}
