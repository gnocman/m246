<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Service;

use NamCong\Loyalty\Api\RewardRepositoryInterface;
use NamCong\Loyalty\Api\Data\RewardInterface;
use NamCong\Loyalty\Model\RewardFactory;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Reward manager with cache layer for active rewards.
 */
class RewardManager
{
    private const CACHE_KEY    = 'namcong_loyalty_active_rewards';
    private const CACHE_TAG    = 'namcong_loyalty';
    private const CACHE_LIFETIME = 3600; // 1 hour

    public function __construct(
        private readonly RewardRepositoryInterface $rewardRepository,
        private readonly RewardFactory $rewardFactory,
        private readonly FrontendInterface $cache,
        private readonly Json $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Get active rewards with caching.
     *
     * @return RewardInterface[]
     */
    public function getCachedActiveRewards(): array
    {
        $cached = $this->cache->load(self::CACHE_KEY);

        if ($cached !== false) {
            try {
                $rewardData = $this->serializer->unserialize($cached);
                $rewards = [];

                foreach ($rewardData as $data) {
                    $reward = $this->rewardFactory->create();
                    $reward->setData($data);
                    $rewards[] = $reward;
                }

                return $rewards;
            } catch (\Exception $e) {
                $this->logger->warning('[NamCong_Loyalty] Cache deserialise failed: ' . $e->getMessage());
            }
        }

        $rewards = $this->rewardRepository->getActiveRewards();
        $rewardData = [];

        foreach ($rewards as $reward) {
            $rewardData[] = $reward->getData();
        }

        try {
            $this->cache->save(
                $this->serializer->serialize($rewardData),
                self::CACHE_KEY,
                [self::CACHE_TAG],
                self::CACHE_LIFETIME
            );
        } catch (\Exception $e) {
            $this->logger->warning('[NamCong_Loyalty] Cache save failed: ' . $e->getMessage());
        }

        return $rewards;
    }

    /**
     * Invalidate the rewards cache (call after save/delete).
     */
    public function invalidateCache(): void
    {
        $this->cache->remove(self::CACHE_KEY);
    }
}
