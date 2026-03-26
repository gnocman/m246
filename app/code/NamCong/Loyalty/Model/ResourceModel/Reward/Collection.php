<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\ResourceModel\Reward;

use NamCong\Loyalty\Model\Reward;
use NamCong\Loyalty\Model\ResourceModel\Reward as RewardResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'reward_id';
    protected $_eventPrefix = 'namcong_loyalty_reward_collection';

    protected function _construct(): void
    {
        $this->_init(Reward::class, RewardResource::class);
    }
}
