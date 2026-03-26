<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\ResourceModel\Rule;

use NamCong\Loyalty\Model\Rule;
use NamCong\Loyalty\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'rule_id';
    protected $_eventPrefix = 'namcong_loyalty_rule_collection';

    protected function _construct(): void
    {
        $this->_init(Rule::class, RuleResource::class);
    }
}
