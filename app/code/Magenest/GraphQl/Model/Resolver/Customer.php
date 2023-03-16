<?php
/**
 * Copyright © NamCong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\GraphQl\Model\Resolver;

use _PHPStan_4dd92cd93\Nette\Schema\Context;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Customer\Model\CustomerFactory;

/**
 * Customers field resolver, used for GraphQL request processing
 */
class Customer implements ResolverInterface
{
    /**
     * @var ValueFactory
     */
    private ValueFactory $valueFactory;

    /**
     * @var CustomerFactory
     */
    private CustomerFactory $customerFactory;

    /**
     * @param ValueFactory $valueFactory
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        ValueFactory $valueFactory,
        CustomerFactory $customerFactory,
    ) {
        $this->valueFactory = $valueFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Resolve
     *
     * @param Field $field
     * @param Context $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['email'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'email for customer should be specified',
                    [\Magento\Customer\Model\Customer::ENTITY]
                )
            );
        }
        try {
            $data = $this->getCustomerData($args['email']);
            $result = function () use ($data) {
                return !empty($data) ? $data : [];
            };
            return $this->valueFactory->create($result);
        } catch (NoSuchEntityException|LocalizedException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        }
    }

    /**
     * Get customer data
     *
     * @param $customerEmail
     * @return array
     * @throws NoSuchEntityException
     */
    private function getCustomerData($customerEmail): array
    {
        try {
            $customerData = [];
            $customerColl = $this->customerFactory->create()->getCollection()->addFieldToFilter(
                'email',
                ['eq' => $customerEmail]
            );
            foreach ($customerColl as $customer) {
                $customerData[] = $customer->getData();
            }

            return $customerData[0] ?? [];
        } catch (NoSuchEntityException $e) {

            return [];
        } catch (LocalizedException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        }
    }
}
