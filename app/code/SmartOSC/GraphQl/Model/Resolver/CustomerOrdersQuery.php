<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use SmartOSC\GraphQl\Model\Resolver\DataProvider\CustomerOrdersQuery as CustomerOrdersDataProvider;

/**
 * Resolver for the customer_orders GraphQL query
 */
class CustomerOrdersQuery implements ResolverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var CustomerOrdersDataProvider
     */
    private CustomerOrdersDataProvider $customerOrdersDataProvider;

    /**
     * CustomerOrdersQuery constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerOrdersDataProvider $customerOrdersDataProvider
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerOrdersDataProvider $customerOrdersDataProvider
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerOrdersDataProvider = $customerOrdersDataProvider;
    }

    /**
     * Resolves the customer_orders query.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlNoSuchEntityException
     * @throws GraphQlAuthorizationException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        $customerEmail = $args['customer_email'] ?? null;

        if (!$customerEmail) {
            throw new GraphQlAuthorizationException(__('Email for customer should be specified'));
        }

        $customer = $this->customerRepository->get($customerEmail);

        $orders = $this->customerOrdersDataProvider->getCustomerOrders($customer);

        return [
            'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'customer_email' => $customer->getEmail(),
            'orders' => $orders,
            'customer_total_amount' => $this->customerOrdersDataProvider->getCustomerTotalAmount($orders),
        ];
    }
}
