<?php

declare(strict_types=1);

namespace NamCong\Loyalty\ViewModel;

use NamCong\Loyalty\Api\HistoryRepositoryInterface;
use NamCong\Loyalty\Api\PointRepositoryInterface;
use NamCong\Loyalty\Service\RewardManager;
use NamCong\Loyalty\Service\LevelManager;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class LoyaltyDashboard implements ArgumentInterface
{
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly PointRepositoryInterface $pointRepository,
        private readonly HistoryRepositoryInterface $historyRepository,
        private readonly RewardManager $rewardManager,
        private readonly LevelManager $levelManager
    ) {
    }

    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getCustomerId(): int
    {
        return (int) $this->customerSession->getCustomerId();
    }

    public function getTotalPoints(): int
    {
        $customerId = $this->getCustomerId();
        if ($customerId <= 0) {
            return 0;
        }

        try {
            return $this->pointRepository->getByCustomerId($customerId)->getTotalPoints();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getCurrentLevel(): string
    {
        $customerId = $this->getCustomerId();
        if ($customerId <= 0) {
            return LevelManager::LEVEL_BRONZE;
        }

        try {
            return $this->pointRepository->getByCustomerId($customerId)->getLevel();
        } catch (\Exception $e) {
            return LevelManager::LEVEL_BRONZE;
        }
    }

    public function getLevelLabel(): string
    {
        return $this->levelManager->getLevelLabel($this->getCurrentLevel());
    }

    public function getNextLevelThreshold(): ?int
    {
        return $this->levelManager->getNextLevelThreshold($this->getCurrentLevel());
    }

    public function getProgressPercentage(): float
    {
        return $this->levelManager->getProgressPercentage($this->getTotalPoints());
    }

    public function getPointsToNextLevel(): ?int
    {
        $nextThreshold = $this->getNextLevelThreshold();
        if ($nextThreshold === null) {
            return null; // Already at Gold
        }
        return max(0, $nextThreshold - $this->getTotalPoints());
    }

    /**
     * @return \NamCong\Loyalty\Api\Data\HistoryInterface[]
     */
    public function getPointsHistory(int $limit = 10): array
    {
        $customerId = $this->getCustomerId();
        if ($customerId <= 0) {
            return [];
        }

        try {
            return $this->historyRepository->getByCustomerId($customerId, $limit);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return \NamCong\Loyalty\Api\Data\RewardInterface[]
     */
    public function getAvailableRewards(): array
    {
        try {
            return $this->rewardManager->getCachedActiveRewards();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function canAffordReward(\NamCong\Loyalty\Api\Data\RewardInterface $reward): bool
    {
        return $this->getTotalPoints() >= $reward->getRequiredPoints();
    }

    public function getLevelBadgeColor(string $level): string
    {
        return match ($level) {
            LevelManager::LEVEL_GOLD   => '#FFD700',
            LevelManager::LEVEL_SILVER => '#C0C0C0',
            default                    => '#CD7F32', // Bronze
        };
    }

    public function getActionTypeLabel(string $actionType): string
    {
        return match ($actionType) {
            'order'        => __('Purchase')->render(),
            'registration' => __('Registration Bonus')->render(),
            'review'       => __('Product Review')->render(),
            'redemption'   => __('Reward Redemption')->render(),
            'expiration'   => __('Points Expired')->render(),
            'deduction'    => __('Points Deducted')->render(),
            default        => __('Manual Adjustment')->render(),
        };
    }
}
