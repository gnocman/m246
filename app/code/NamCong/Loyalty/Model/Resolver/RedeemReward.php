<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Resolver;

use NamCong\Loyalty\Api\PointManagerInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class RedeemReward implements ResolverInterface
{
    public function __construct(
        private readonly PointManagerInterface $pointManager
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('Customer must be logged in to redeem rewards.'));
        }

        $rewardId = (int) ($args['rewardId'] ?? 0);
        if (!$rewardId) {
            throw new GraphQlInputException(__('Invalid reward ID.'));
        }

        try {
            $customerId = (int) $context->getUserId();

            $this->pointManager->redeemReward($rewardId, $customerId);
            $remainingPoints = $this->pointManager->getCustomerPoints($customerId);

            return [
                'success'          => true,
                'message'          => __('Reward redeemed successfully!')->render(),
                'remaining_points' => $remainingPoints,
            ];
        } catch (LocalizedException $e) {
            $customerId = (int) $context->getUserId();

            return [
                'success'          => false,
                'message'          => $e->getMessage(),
                'remaining_points' => $this->pointManager->getCustomerPoints($customerId),
            ];
        }
    }
}
