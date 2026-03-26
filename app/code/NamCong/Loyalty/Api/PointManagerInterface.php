<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PointManagerInterface
{
    /**
     * Add points to a customer account.
     *
     * @param int    $customerId
     * @param int    $points
     * @param string $actionType
     * @param string $comment
     * @param int|null $orderId
     * @return int New total points balance
     * @throws LocalizedException
     */
    public function addPoints(
        int $customerId,
        int $points,
        string $actionType,
        string $comment = '',
        ?int $orderId = null
    ): int;

    /**
     * Deduct points from a customer account.
     *
     * @param int    $customerId
     * @param int    $points
     * @param string $comment
     * @return int New total points balance
     * @throws LocalizedException
     */
    public function deductPoints(int $customerId, int $points, string $comment = ''): int;

    /**
     * Get customer current points balance.
     *
     * @param int $customerId
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCustomerPoints(?int $customerId = null): int;

    /**
     * Redeem a reward for a customer.
     *
     * @param int $rewardId
     * @param int|null $customerId
     * @return bool
     * @throws LocalizedException
     */
    public function redeemReward(int $rewardId, ?int $customerId = null): bool;
}
