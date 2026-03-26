<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Plugin\Reward;

use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Api\Data\RewardInterface;
use NamCong\Loyalty\Service\RewardManager;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Plugin to invalidate RewardManager cache on reward saves and deletes.
 */
class InvalidateRewardCachePlugin
{
    public function __construct(
        private readonly RewardManager $rewardManager
    ) {
    }

    public function afterSave(
        RewardRepositoryInterface $subject,
        RewardInterface $result
    ): RewardInterface {
        $this->rewardManager->invalidateCache();
        return $result;
    }

    public function afterDelete(
        RewardRepositoryInterface $subject,
        bool $result
    ): bool {
        $this->rewardManager->invalidateCache();
        return $result;
    }

    public function afterDeleteById(
        RewardRepositoryInterface $subject,
        bool $result
    ): bool {
        $this->rewardManager->invalidateCache();
        return $result;
    }
}
