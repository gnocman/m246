<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use NamCong\Loyalty\Service\RewardCouponManager;
use Psr\Log\LoggerInterface;

class ConsumeCouponAfterOrderPlace implements ObserverInterface
{
    public function __construct(
        private readonly RewardCouponManager $rewardCouponManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Sales\Model\Order|null $order */
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return;
        }

        try {
            $this->rewardCouponManager->consumeCoupon((string) $order->getCouponCode());
        } catch (\Throwable $exception) {
            $this->logger->error('[NamCong_Loyalty] ConsumeCouponAfterOrderPlace error: ' . $exception->getMessage());
        }
    }
}
