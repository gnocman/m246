<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Observer;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\PointManagerInterface;
use NamCong\Loyalty\Service\RuleEngine;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddPointsAfterOrder implements ObserverInterface
{
    public function __construct(
        private readonly PointManagerInterface $pointManager,
        private readonly RuleEngine $ruleEngine,
        private readonly \NamCong\Loyalty\Api\HistoryRepositoryInterface $historyRepository,
        private readonly \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$order || $order->getState() !== \Magento\Sales\Model\Order::STATE_COMPLETE) {
            return;
        }

        $customerId = (int) $order->getCustomerId();
        if (!$customerId) {
            return;
        }

        try {
            // Check if points were already awarded for this order to prevent duplicates
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('order_id', $order->getId())
                ->addFilter('action_type', HistoryInterface::ACTION_ORDER)
                ->create();
            
            $historyList = $this->historyRepository->getList($searchCriteria);
            if ($historyList->getTotalCount() > 0) {
                // Points already awarded for this order
                return;
            }

            $orderTotal = (float) $order->getBaseGrandTotal();
            $points = $this->ruleEngine->calculateOrderPoints($orderTotal, $customerId);

            if ($points <= 0) {
                return;
            }

            $this->pointManager->addPoints(
                $customerId,
                $points,
                HistoryInterface::ACTION_ORDER,
                __('Earned %1 points for order #%2', $points, $order->getIncrementId())->render(),
                (int) $order->getId()
            );
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] AddPointsAfterOrder error: ' . $e->getMessage());
        }
    }
}
