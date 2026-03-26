<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Plugin\Order;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\OrderService;
use NamCong\Loyalty\Service\RewardCouponManager;
use Psr\Log\LoggerInterface;

class RestoreCouponAfterCancelPlugin
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly RewardCouponManager $rewardCouponManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function afterCancel(OrderService $subject, bool $result, int $orderId): bool
    {
        if (!$result) {
            return $result;
        }

        try {
            $order = $this->orderRepository->get($orderId);
            $this->rewardCouponManager->restoreCoupon(
                (string) $order->getCouponCode(),
                (int) $order->getEntityId()
            );
        } catch (\Throwable $exception) {
            $this->logger->error('[NamCong_Loyalty] RestoreCouponAfterCancelPlugin error: ' . $exception->getMessage());
        }

        return $result;
    }
}
