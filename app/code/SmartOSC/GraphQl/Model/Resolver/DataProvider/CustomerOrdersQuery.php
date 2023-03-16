<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GraphQl\Model\Resolver\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Retrieves data for the customer_orders GraphQL query.
 */
class CustomerOrdersQuery
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private FilterBuilder $filterBuilder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Get customer orders.
     *
     * @param object $customer
     * @return array
     */
    public function getCustomerOrders(object $customer): array
    {
        $filters = [
            $this->filterBuilder
                ->setField(OrderInterface::CUSTOMER_ID)
                ->setValue($customer->getId())
                ->create(),
        ];

        $this->searchCriteriaBuilder->addFilters($filters);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        return array_map(function ($order) {
            return [
                'increment_id' => $order->getIncrementId(),
                'order_status' => $order->getStatus(),
                'order_grand_total' => $order->getGrandTotal(),
            ];
        }, $orders);
    }

    /**
     * Get total amount of customer orders.
     *
     * @param array $orders
     * @return float
     */
    public function getCustomerTotalAmount(array $orders): float
    {
        return array_sum(array_column($orders, 'order_grand_total'));
    }
}
