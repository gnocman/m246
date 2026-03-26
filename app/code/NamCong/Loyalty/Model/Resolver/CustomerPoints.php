<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Resolver;

use NamCong\Loyalty\Api\PointRepositoryInterface;
use NamCong\Loyalty\Service\LevelManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CustomerPoints implements ResolverInterface
{
    public function __construct(
        private readonly PointRepositoryInterface $pointRepository,
        private readonly LevelManager $levelManager
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('Customer must be logged in to access loyalty points.'));
        }

        $customerId = (int) $context->getUserId();

        try {
            $points = $this->pointRepository->getByCustomerId($customerId);
            $total  = $points->getTotalPoints();
            $level  = $points->getLevel();

            return [
                'total_points'        => $total,
                'level'               => $level,
                'next_level_points'   => $this->levelManager->getNextLevelThreshold($level),
                'progress_percentage' => $this->levelManager->getProgressPercentage($total),
                'model'               => $points, // Passed to nested resolvers
            ];
        } catch (\Exception $e) {
            return [
                'total_points'        => 0,
                'level'               => 'bronze',
                'next_level_points'   => LevelManager::SILVER_THRESHOLD,
                'progress_percentage' => 0.0,
                'model'               => null,
            ];
        }
    }
}
