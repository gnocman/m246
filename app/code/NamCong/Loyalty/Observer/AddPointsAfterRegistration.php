<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Observer;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\PointManagerInterface;
use NamCong\Loyalty\Service\RuleEngine;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddPointsAfterRegistration implements ObserverInterface
{
    public function __construct(
        private readonly PointManagerInterface $pointManager,
        private readonly RuleEngine $ruleEngine,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getEvent()->getCustomer();

        if (!$customer) {
            return;
        }

        $customerId = (int) $customer->getId();
        if (!$customerId) {
            return;
        }

        try {
            $points = $this->ruleEngine->getPointsForAction(HistoryInterface::ACTION_REGISTRATION, $customerId);
            if ($points <= 0) {
                return;
            }

            $this->pointManager->addPoints(
                $customerId,
                $points,
                HistoryInterface::ACTION_REGISTRATION,
                __('Welcome! Earned %1 points for creating an account', $points)->render()
            );
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] AddPointsAfterRegistration error: ' . $e->getMessage());
        }
    }
}
