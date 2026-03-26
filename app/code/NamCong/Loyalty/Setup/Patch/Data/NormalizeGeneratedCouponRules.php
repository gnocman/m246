<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Setup\Patch\Data;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use NamCong\Loyalty\Api\Data\RewardInterface;
use Psr\Log\LoggerInterface;

class NormalizeGeneratedCouponRules implements DataPatchInterface
{
    private const LOYALTY_RULE_NAME_PATTERN = 'Loyalty Reward:%';
    private const LOYALTY_COUPON_PATTERN = 'LOYALTY-%';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly LoggerInterface $logger
    ) {
    }

    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        try {
            $convertedRuleCount = $this->convertDiscountRulesToCartFixed($connection);
            $deactivatedRuleCount = $this->deactivateUsedCouponRules($connection);

            $this->logger->info(sprintf(
                '[NamCong_Loyalty] Normalized generated coupon rules: converted=%d, deactivated=%d',
                $convertedRuleCount,
                $deactivatedRuleCount
            ));
        } finally {
            $connection->endSetup();
        }

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    private function convertDiscountRulesToCartFixed(AdapterInterface $connection): int
    {
        $salesRuleTable = $this->moduleDataSetup->getTable('salesrule');
        $rewardTable = $this->moduleDataSetup->getTable('loyalty_reward');

        $ruleIds = $this->getUniqueIds($connection->fetchCol(
            $connection->select()
                ->from(['salesrule' => $salesRuleTable], ['rule_id'])
                ->joinInner(['reward' => $rewardTable], 'reward.name = salesrule.description', [])
                ->where('salesrule.name LIKE ?', self::LOYALTY_RULE_NAME_PATTERN)
                ->where('salesrule.simple_action = ?', \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION)
                ->where('reward.reward_type = ?', RewardInterface::TYPE_DISCOUNT)
        ));

        if ($ruleIds === []) {
            return 0;
        }

        return $connection->update(
            $salesRuleTable,
            ['simple_action' => \Magento\SalesRule\Model\Rule::CART_FIXED_ACTION],
            ['rule_id IN (?)' => $ruleIds]
        );
    }

    private function deactivateUsedCouponRules(AdapterInterface $connection): int
    {
        $salesRuleTable = $this->moduleDataSetup->getTable('salesrule');
        $couponTable = $this->moduleDataSetup->getTable('salesrule_coupon');
        $orderTable = $this->moduleDataSetup->getTable('sales_order');

        $ruleIds = $this->getUniqueIds($connection->fetchCol(
            $connection->select()
                ->from(['coupon' => $couponTable], ['rule_id'])
                ->joinInner(['salesrule' => $salesRuleTable], 'salesrule.rule_id = coupon.rule_id', [])
                ->joinInner(['sales_order' => $orderTable], 'sales_order.coupon_code = coupon.code', [])
                ->where('coupon.code LIKE ?', self::LOYALTY_COUPON_PATTERN)
                ->where('salesrule.name LIKE ?', self::LOYALTY_RULE_NAME_PATTERN)
        ));

        if ($ruleIds === []) {
            return 0;
        }

        return $connection->update(
            $salesRuleTable,
            ['is_active' => 0],
            [
                'rule_id IN (?)' => $ruleIds,
                'is_active = ?' => 1,
            ]
        );
    }

    /**
     * @param array<int|string> $ids
     * @return int[]
     */
    private function getUniqueIds(array $ids): array
    {
        return array_values(array_unique(array_map('intval', $ids)));
    }
}
