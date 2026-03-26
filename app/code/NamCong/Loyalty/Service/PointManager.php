<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Service;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\HistoryRepositoryInterface;
use NamCong\Loyalty\Api\PointManagerInterface;
use NamCong\Loyalty\Api\PointRepositoryInterface;
use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Model\HistoryFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class PointManager implements PointManagerInterface
{
    public function __construct(
        private readonly PointRepositoryInterface $pointRepository,
        private readonly HistoryRepositoryInterface $historyRepository,
        private readonly RewardRepositoryInterface $rewardRepository,
        private readonly HistoryFactory $historyFactory,
        private readonly CustomerSession $customerSession,
        private readonly LevelManager $levelManager,
        private readonly RewardCouponManager $rewardCouponManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function addPoints(
        int $customerId,
        int $points,
        string $actionType,
        string $comment = '',
        ?int $orderId = null
    ): int {
        if ($points <= 0) {
            return $this->getCustomerPoints($customerId);
        }

        try {
            $newTotal = $this->changeBalance($customerId, $points);
            $this->createHistoryEntry(
                $customerId,
                $points,
                $actionType,
                $comment ?: $this->getDefaultComment($actionType, $points),
                $orderId
            );

            $this->logger->info(sprintf(
                '[NamCong_Loyalty] Added %d points to customer %d. New balance: %d',
                $points,
                $customerId,
                $newTotal
            ));

            return $newTotal;
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] addPoints error: ' . $e->getMessage());
            throw new LocalizedException(__('Could not add loyalty points: %1', $e->getMessage()), $e);
        }
    }

    public function deductPoints(int $customerId, int $points, string $comment = ''): int
    {
        if ($points <= 0) {
            return $this->getCustomerPoints($customerId);
        }

        try {
            $newTotal = $this->changeBalance($customerId, -$points);
            $this->createHistoryEntry(
                $customerId,
                -$points,
                HistoryInterface::ACTION_DEDUCTION,
                $comment ?: __('Points deducted')->render()
            );
            return $newTotal;
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] deductPoints error: ' . $e->getMessage());
            throw new LocalizedException(__('Could not deduct loyalty points.'), $e);
        }
    }

    public function getCustomerPoints(?int $customerId = null): int
    {
        $customerId = $customerId ?? (int) $this->customerSession->getCustomerId();
        if (!$customerId) {
            return 0;
        }
        try {
            return $this->pointRepository->getByCustomerId($customerId)->getTotalPoints();
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

    public function redeemReward(int $rewardId, ?int $customerId = null): bool
    {
        $customerId = $customerId ?? (int) $this->customerSession->getCustomerId();
        if (!$customerId) {
            throw new LocalizedException(__('Customer must be logged in to redeem rewards.'));
        }

        try {
            $reward = $this->rewardRepository->getById($rewardId);
            if (!$reward->getIsActive()) {
                throw new LocalizedException(__('This reward is no longer available.'));
            }

            $requiredPoints = $reward->getRequiredPoints();
            $currentPoints = $this->getCustomerPoints($customerId);

            if ($currentPoints < $requiredPoints) {
                throw new LocalizedException(
                    __('You need %1 points to redeem this reward, but you only have %2.', $requiredPoints, $currentPoints)
                );
            }

            $couponCode = $this->rewardCouponManager->fulfillReward($reward, $customerId);
            $this->changeBalance($customerId, -$requiredPoints);
            $this->createHistoryEntry(
                $customerId,
                -$requiredPoints,
                HistoryInterface::ACTION_REDEMPTION,
                __('Redeemed: %1. Your Code: %2', $reward->getName(), $couponCode)->render()
            );

            $this->logger->info(sprintf(
                '[NamCong_Loyalty] Customer %d redeemed reward %d for %d points.',
                $customerId,
                $rewardId,
                $requiredPoints
            ));

            return true;
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] redeemReward error: ' . $e->getMessage());
            throw new LocalizedException(__('Could not redeem loyalty reward.'), $e);
        }
    }

    private function getDefaultComment(string $actionType, int $points): string
    {
        return match ($actionType) {
            HistoryInterface::ACTION_ORDER        => __('Earned %1 points for purchase', $points)->render(),
            HistoryInterface::ACTION_REGISTRATION => __('Welcome bonus: %1 points for registering', $points)->render(),
            HistoryInterface::ACTION_REVIEW       => __('Earned %1 points for product review', $points)->render(),
            HistoryInterface::ACTION_MANUAL       => __('Manual points adjustment: %1 points', $points)->render(),
            default                               => __('Loyalty points: %1', $points)->render(),
        };
    }

    private function changeBalance(int $customerId, int $pointsDelta): int
    {
        $pointsModel = $this->pointRepository->getByCustomerId($customerId);
        $newTotal = $pointsModel->getTotalPoints() + $pointsDelta;

        if ($newTotal < 0) {
            throw new LocalizedException(
                __('Insufficient points balance. You have %1 points but need %2.', $pointsModel->getTotalPoints(), abs($pointsDelta))
            );
        }

        $pointsModel->setTotalPoints($newTotal);
        $pointsModel->setLevel($this->levelManager->calculateLevel($newTotal));
        $this->pointRepository->save($pointsModel);

        return $newTotal;
    }

    private function createHistoryEntry(
        int $customerId,
        int $points,
        string $actionType,
        string $comment,
        ?int $orderId = null
    ): void {
        $history = $this->historyFactory->create();
        $history->setCustomerId($customerId)
            ->setPoints($points)
            ->setActionType($actionType)
            ->setOrderId($orderId)
            ->setComment($comment);
        $this->historyRepository->save($history);
    }
}
