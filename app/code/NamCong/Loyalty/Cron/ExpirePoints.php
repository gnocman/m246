<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Cron;

use NamCong\Loyalty\Api\Data\HistoryInterface;
use NamCong\Loyalty\Api\HistoryRepositoryInterface;
use NamCong\Loyalty\Api\PointRepositoryInterface;
use NamCong\Loyalty\Model\HistoryFactory;
use NamCong\Loyalty\Model\ResourceModel\Points\CollectionFactory as PointsCollectionFactory;
use NamCong\Loyalty\Service\LevelManager;
use Psr\Log\LoggerInterface;

/**
 * Cron job: expire points older than 365 days.
 * Runs daily at 02:00 AM (see etc/crontab.xml).
 */
class ExpirePoints
{
    private const EXPIRY_DAYS = 365;

    public function __construct(
        private readonly PointsCollectionFactory $pointsCollectionFactory,
        private readonly PointRepositoryInterface $pointRepository,
        private readonly HistoryRepositoryInterface $historyRepository,
        private readonly HistoryFactory $historyFactory,
        private readonly LevelManager $levelManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        $this->logger->info('[NamCong_Loyalty] Starting point expiration cron job.');

        try {
            $expiryDate = date('Y-m-d H:i:s', strtotime(sprintf('-%d days', self::EXPIRY_DAYS)));

            // Find customers whose last update is older than expiry threshold and has positive points
            $collection = $this->pointsCollectionFactory->create();
            $collection->addFieldToFilter('updated_at', ['lt' => $expiryDate]);
            $collection->addFieldToFilter('total_points', ['gt' => 0]);

            $expiredCount = 0;
            foreach ($collection as $pointsModel) {
                $customerId = (int) $pointsModel->getCustomerId();
                $expiredPoints = $pointsModel->getTotalPoints();

                $pointsModel->setTotalPoints(0);
                $pointsModel->setLevel($this->levelManager->calculateLevel(0));
                $this->pointRepository->save($pointsModel);

                // Record expiration in history
                $history = $this->historyFactory->create();
                $history->setCustomerId($customerId)
                    ->setPoints(-$expiredPoints)
                    ->setActionType(HistoryInterface::ACTION_EXPIRATION)
                    ->setComment(__('Points expired after %1 days of inactivity', self::EXPIRY_DAYS)->render());
                $this->historyRepository->save($history);

                $expiredCount++;
            }

            $this->logger->info(sprintf(
                '[NamCong_Loyalty] Point expiration complete. Expired points for %d customers.',
                $expiredCount
            ));
        } catch (\Exception $e) {
            $this->logger->error('[NamCong_Loyalty] ExpirePoints cron error: ' . $e->getMessage());
        }
    }
}
