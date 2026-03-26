<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Service;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Sales\Model\Order;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\StoreManagerInterface;
use NamCong\Loyalty\Api\Data\RewardInterface;
use Psr\Log\LoggerInterface;

class RewardCouponManager
{
    private const LOYALTY_COUPON_PREFIX = 'LOYALTY-';

    public function __construct(
        private readonly RuleFactory $ruleFactory,
        private readonly CouponFactory $couponFactory,
        private readonly ResourceConnection $resourceConnection,
        private readonly Random $random,
        private readonly StoreManagerInterface $storeManager,
        private readonly \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Create a Magento Sales Rule (cart price rule) for the redeemed reward.
     *
     * @return string The generated coupon code.
     */
    public function fulfillReward(RewardInterface $reward, int $customerId): string
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $couponCode = self::LOYALTY_COUPON_PREFIX . strtoupper($this->random->getRandomString(6));

            /** @var Rule $rule */
            $rule = $this->ruleFactory->create();

            $ruleName = sprintf('Loyalty Reward: %s (Customer #%d)', $reward->getName(), $customerId);

            $rule->setName($ruleName)
                ->setDescription($reward->getName())
                ->setFromDate(date('Y-m-d', strtotime('-1 day')))
                ->setIsActive(1)
                ->setWebsiteIds($this->getAllWebsiteIds())
                ->setCustomerGroupIds([$customer->getGroupId()])
                ->setCouponType(Rule::COUPON_TYPE_SPECIFIC)
                ->setCouponCode($couponCode)
                ->setUseAutoGeneration(0)
                ->setUsesPerCoupon(1)
                ->setUsesPerCustomer(1)
                ->setSimpleAction($this->resolveSimpleAction($reward))
                ->setDiscountAmount($reward->getRewardValue())
                ->setStopRulesProcessing(0)
                ->setIsAdvanced(1);

            if ($reward->getRewardType() === RewardInterface::TYPE_FREE_SHIPPING) {
                $rule->setSimpleFreeShipping(\Magento\SalesRule\Api\Data\RuleInterface::FREE_SHIPPING_MATCHING_ITEMS_ONLY);
            }

            $conditions = [
                'type' => \Magento\SalesRule\Model\Rule\Condition\Combine::class,
                'aggregator' => 'all',
                'value' => '1',
                'conditions' => [
                    [
                        'type' => \Magento\SalesRule\Model\Rule\Condition\Address::class,
                        'attribute' => 'email',
                        'operator' => '==',
                        'value' => $customer->getEmail(),
                    ],
                ],
            ];
            $rule->getConditions()->loadArray($conditions);
            $rule->save();

            $this->logger->info(sprintf(
                '[NamCong_Loyalty] Generated reward coupon %s for customer %s (Rule ID: %d)',
                $couponCode,
                $customer->getEmail(),
                (int) $rule->getId()
            ));

            return $couponCode;
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] Reward fulfillment error: ' . $e->getMessage());
            throw new LocalizedException(__('Could not generate the reward coupon. Please try again later.'), $e);
        }
    }

    public function isLoyaltyCouponCode(string $couponCode): bool
    {
        return str_starts_with(trim($couponCode), self::LOYALTY_COUPON_PREFIX);
    }

    public function consumeCoupon(string $couponCode): void
    {
        $couponCode = trim($couponCode);
        if ($couponCode === '' || !$this->isLoyaltyCouponCode($couponCode)) {
            return;
        }

        $coupon = $this->couponFactory->create()->loadByCode($couponCode);
        if (!(int) $coupon->getId()) {
            $this->logger->warning(sprintf(
                '[NamCong_Loyalty] Loyalty coupon %s could not be found for consumption',
                $couponCode
            ));
            return;
        }

        /** @var Rule $rule */
        $rule = $this->ruleFactory->create()->load((int) $coupon->getRuleId());
        if (!(int) $rule->getId() || !(bool) $rule->getIsActive()) {
            return;
        }

        $rule->setIsActive(false);
        $rule->save();

        $this->logger->info(sprintf(
            '[NamCong_Loyalty] Consumed loyalty coupon %s by deactivating rule %d',
            $couponCode,
            (int) $rule->getId()
        ));
    }

    public function restoreCoupon(string $couponCode, ?int $excludedOrderId = null): void
    {
        $couponCode = trim($couponCode);
        if ($couponCode === '' || !$this->isLoyaltyCouponCode($couponCode)) {
            return;
        }

        if ($this->hasAnotherActiveOrderForCoupon($couponCode, $excludedOrderId)) {
            $this->logger->info(sprintf(
                '[NamCong_Loyalty] Skip restoring loyalty coupon %s because another non-canceled order still uses it',
                $couponCode
            ));
            return;
        }

        $coupon = $this->couponFactory->create()->loadByCode($couponCode);
        if (!(int) $coupon->getId()) {
            $this->logger->warning(sprintf(
                '[NamCong_Loyalty] Loyalty coupon %s could not be found for restoration',
                $couponCode
            ));
            return;
        }

        /** @var Rule $rule */
        $rule = $this->ruleFactory->create()->load((int) $coupon->getRuleId());
        if (!(int) $rule->getId() || (bool) $rule->getIsActive()) {
            return;
        }

        $rule->setIsActive(true);
        $rule->save();

        $this->logger->info(sprintf(
            '[NamCong_Loyalty] Restored loyalty coupon %s by reactivating rule %d',
            $couponCode,
            (int) $rule->getId()
        ));
    }

    private function resolveSimpleAction(RewardInterface $reward): string
    {
        if ($reward->getRewardType() === RewardInterface::TYPE_DISCOUNT) {
            return Rule::CART_FIXED_ACTION;
        }

        return Rule::BY_FIXED_ACTION;
    }

    private function hasAnotherActiveOrderForCoupon(string $couponCode, ?int $excludedOrderId): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $salesOrderTable = $this->resourceConnection->getTableName('sales_order');

        $select = $connection->select()
            ->from($salesOrderTable, ['entity_id'])
            ->where('coupon_code = ?', $couponCode)
            ->where('state != ?', Order::STATE_CANCELED)
            ->limit(1);

        if ($excludedOrderId !== null) {
            $select->where('entity_id != ?', $excludedOrderId);
        }

        return (bool) $connection->fetchOne($select);
    }

    private function getAllWebsiteIds(): array
    {
        $ids = [];
        foreach ($this->storeManager->getWebsites() as $website) {
            $ids[] = $website->getId();
        }

        return $ids;
    }
}
