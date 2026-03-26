<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Setup\Patch\Data;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\RuleRepositoryInterface;
use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Model\RuleFactory;
use NamCong\Loyalty\Model\RewardFactory;
use NamCong\Loyalty\Api\Data\RewardInterface;
use NamCong\Loyalty\Model\ResourceModel\Reward\CollectionFactory as RewardCollectionFactory;
use NamCong\Loyalty\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

/**
 * Seeds default loyalty rules and rewards for demonstration purposes.
 */
class InstallDefaultData implements DataPatchInterface
{
    public function __construct(
        private readonly RuleRepositoryInterface  $ruleRepository,
        private readonly RewardRepositoryInterface $rewardRepository,
        private readonly RuleFactory $ruleFactory,
        private readonly RewardFactory $rewardFactory,
        private readonly RuleCollectionFactory $ruleCollectionFactory,
        private readonly RewardCollectionFactory $rewardCollectionFactory,
        private readonly Json $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function apply(): self
    {
        $this->installDefaultRules();
        $this->installDefaultRewards();
        return $this;
    }

    private function installDefaultRules(): void
    {
        $rules = [
            ['name' => 'Registration Bonus',    'points' => 100, 'condition' => ['action_type' => HistoryInterface::ACTION_REGISTRATION]],
            ['name' => 'Product Review Reward', 'points' => 50,  'condition' => ['action_type' => HistoryInterface::ACTION_REVIEW]],
            ['name' => 'Purchase Points',       'points' => 0,   'condition' => ['action_type' => HistoryInterface::ACTION_ORDER]],
        ];

        foreach ($rules as $ruleData) {
            try {
                if ($this->defaultRuleExists($ruleData)) {
                    continue;
                }

                /** @var \NamCong\Loyalty\Model\Rule $rule */
                $rule = $this->ruleFactory->create();
                $rule->setName($ruleData['name']);
                $rule->setPoints($ruleData['points']);
                $rule->setIsActive(true);
                $rule->setCustomerGroupIds(null);
                $rule->setConditionSerialized($this->serializer->serialize($ruleData['condition']));
                $this->ruleRepository->save($rule);
            } catch (\Exception $e) {
                $this->logger->warning('[NamCong_Loyalty] Could not install default rule: ' . $e->getMessage());
            }
        }
    }

    private function installDefaultRewards(): void
    {
        $rewards = [
            ['name' => '$5 Discount Voucher',      'points' => 500,  'type' => RewardInterface::TYPE_DISCOUNT,      'value' => 5.0],
            ['name' => '$10 Discount Voucher',     'points' => 1000, 'type' => RewardInterface::TYPE_DISCOUNT,      'value' => 10.0],
            ['name' => 'Free Shipping',            'points' => 200,  'type' => RewardInterface::TYPE_FREE_SHIPPING, 'value' => 0.0],
            ['name' => 'Premium Gift Product',     'points' => 2000, 'type' => RewardInterface::TYPE_GIFT_PRODUCT,  'value' => 1.0],
        ];

        foreach ($rewards as $rewardData) {
            try {
                if ($this->defaultRewardExists($rewardData)) {
                    continue;
                }

                /** @var \NamCong\Loyalty\Model\Reward $reward */
                $reward = $this->rewardFactory->create();
                $reward->setName($rewardData['name']);
                $reward->setRequiredPoints($rewardData['points']);
                $reward->setRewardType($rewardData['type']);
                $reward->setRewardValue($rewardData['value']);
                $reward->setIsActive(true);
                $this->rewardRepository->save($reward);
            } catch (\Exception $e) {
                $this->logger->warning('[NamCong_Loyalty] Could not install default reward: ' . $e->getMessage());
            }
        }
    }

    private function defaultRuleExists(array $ruleData): bool
    {
        $collection = $this->ruleCollectionFactory->create();
        $collection->addFieldToFilter('name', $ruleData['name']);
        $collection->setPageSize(1);

        return (bool) $collection->getSize();
    }

    private function defaultRewardExists(array $rewardData): bool
    {
        $collection = $this->rewardCollectionFactory->create();
        $collection->addFieldToFilter('name', $rewardData['name']);
        $collection->setPageSize(1);

        return (bool) $collection->getSize();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
