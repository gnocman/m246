<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Observer;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\PointManagerInterface;
use NamCong\Loyalty\Service\RuleEngine;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddPointsAfterReview implements ObserverInterface
{
    public function __construct(
        private readonly PointManagerInterface $pointManager,
        private readonly RuleEngine $ruleEngine,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getObject();
        if (!$review) {
            return;
        }

        $statusId = (int) $review->getStatusId();
        $origStatusId = (int) $review->getOrigData('status_id');
        $customerId = (int) $review->getCustomerId();

        if (!$customerId) {
            return;
        }

        // Only award points for approved reviews (status_id = 1)
        if ($statusId !== \Magento\Review\Model\Review::STATUS_APPROVED) {
            return;
        }

        // Prevent awarding points again if review was already approved previously
        if ($origStatusId === \Magento\Review\Model\Review::STATUS_APPROVED) {
            return;
        }

        try {
            $points = $this->ruleEngine->getPointsForAction(HistoryInterface::ACTION_REVIEW, $customerId);
            if ($points <= 0) {
                return;
            }

            $this->pointManager->addPoints(
                $customerId,
                $points,
                HistoryInterface::ACTION_REVIEW,
                __('Earned %1 points for approved product review', $points)->render()
            );
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] AddPointsAfterReview error: ' . $e->getMessage());
        }
    }
}
