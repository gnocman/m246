<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\Resolver;

use NamCong\Loyalty\Api\HistoryRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PointsHistory implements ResolverInterface
{
    public function __construct(
        private readonly HistoryRepositoryInterface $historyRepository
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('Customer must be logged in.'));
        }

        $customerId = (int) $context->getUserId();
        $historyItems = $this->historyRepository->getByCustomerId($customerId, 20);
        $result = [];

        foreach ($historyItems as $entry) {
            $result[] = [
                'history_id'  => $entry->getHistoryId(),
                'points'      => $entry->getPoints(),
                'action_type' => $entry->getActionType(),
                'comment'     => $entry->getComment(),
                'created_at'  => $entry->getCreatedAt(),
            ];
        }

        return $result;
    }
}
