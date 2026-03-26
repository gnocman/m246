<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Service;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\RuleRepositoryInterface;
use NamCong\Loyalty\Api\Data\RuleInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Evaluates which loyalty rules apply to a given context.
 */
class RuleEngine
{
    public function __construct(
        private readonly RuleRepositoryInterface $ruleRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly LoggerInterface $logger,
        private readonly Json $serializer
    ) {
    }

    /**
     * Get total points to award for a given order amount.
     */
    public function calculateOrderPoints(float $orderAmount, int $customerId): int
    {
        $basePoints = max(0, (int) floor($orderAmount));

        return $basePoints + $this->getPointsForAction(HistoryInterface::ACTION_ORDER, $customerId);
    }

    /**
     * Get points to award for a specific action type.
     */
    public function getPointsForAction(string $actionType, int $customerId): int
    {
        $rules = $this->ruleRepository->getActiveRules();
        $totalPoints = 0;

        foreach ($rules as $rule) {
            if (!$this->ruleApplies($rule, $customerId)) {
                continue;
            }

            if ($this->ruleMatchesAction($rule, $actionType)) {
                $totalPoints += max(0, $rule->getPoints());
            }
        }

        return $totalPoints;
    }

    /**
     * Default fixed points for each action type.
     */
    public function getDefaultPointsForAction(string $actionType): int
    {
        return match ($actionType) {
            HistoryInterface::ACTION_REGISTRATION => 100,
            HistoryInterface::ACTION_REVIEW       => 50,
            HistoryInterface::ACTION_ORDER        => 0, // Calculated based on order total
            default                               => 0,
        };
    }

    private function ruleApplies(RuleInterface $rule, int $customerId): bool
    {
        $groupIds = $rule->getCustomerGroupIds();
        if (!$groupIds) {
            return true;
        }

        if ($customerId <= 0) {
            return false;
        }

        $allowedGroups = explode(',', $groupIds);
        $customerGroupId = $this->getCustomerGroupId($customerId);
        if ($customerGroupId === null) {
            return false;
        }

        return in_array((string) $customerGroupId, $allowedGroups, true);
    }

    private function ruleMatchesAction(RuleInterface $rule, string $actionType): bool
    {
        try {
            $conditions = $rule->getConditionSerialized()
                ? $this->serializer->unserialize($rule->getConditionSerialized())
                : [];
        } catch (\Exception $e) {
            $this->logger->warning('[NamCong_Loyalty] Rule condition parse error: ' . $e->getMessage());
            return false;
        }

        return ($conditions['action_type'] ?? null) === $actionType;
    }

    private function getCustomerGroupId(int $customerId): ?int
    {
        try {
            return (int) $this->customerRepository->getById($customerId)->getGroupId();
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(
                sprintf('[NamCong_Loyalty] Customer %d not found while evaluating loyalty rules.', $customerId)
            );
            return null;
        }
    }
}
