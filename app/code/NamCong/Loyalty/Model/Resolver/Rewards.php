<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Resolver;

use NamCong\Loyalty\Api\RewardRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Rewards implements ResolverInterface
{
    public function __construct(
        private readonly RewardRepositoryInterface $rewardRepository
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        $rewards = $this->rewardRepository->getActiveRewards();
        $result  = [];

        foreach ($rewards as $reward) {
            $result[] = [
                'reward_id'       => $reward->getRewardId(),
                'name'            => $reward->getName(),
                'required_points' => $reward->getRequiredPoints(),
                'reward_type'     => $reward->getRewardType(),
                'reward_value'    => $reward->getRewardValue(),
                'is_active'       => $reward->getIsActive(),
            ];
        }

        return $result;
    }
}
