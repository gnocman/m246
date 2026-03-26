<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\ResourceModel\History;

use NamCong\Loyalty\Model\History;
use NamCong\Loyalty\Model\ResourceModel\History as HistoryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'history_id';
    protected $_eventPrefix = 'namcong_loyalty_history_collection';

    protected function _construct(): void
    {
        $this->_init(History::class, HistoryResource::class);
    }
}
