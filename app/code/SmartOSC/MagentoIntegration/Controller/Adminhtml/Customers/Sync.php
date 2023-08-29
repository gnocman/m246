<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Controller\Adminhtml\Customers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use SmartOSC\MagentoIntegration\Service\Helper\Customers;
use SmartOSC\MagentoIntegration\Model\CustomersFactory;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers as CustomerResource;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers\CollectionFactory;

class Sync extends Action
{
    /**
     * @var Customers
     */
    private Customers $customers;
    /**
     * @var CustomersFactory
     */
    private CustomersFactory $customersFactory;
    /**
     * @var CustomerResource
     */
    private CustomerResource $customersResource;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $customersCollectionFactory;

    /**
     * @param Context $context
     * @param Customers $customers
     * @param CustomersFactory $customersFactory
     * @param CustomerResource $customersResource
     * @param CollectionFactory $customersCollectionFactory
     */
    public function __construct(
        Context $context,
        Customers $customers,
        CustomersFactory $customersFactory,
        CustomerResource $customersResource,
        CollectionFactory $customersCollectionFactory
    ) {
        parent::__construct($context);
        $this->customers = $customers;
        $this->customersFactory = $customersFactory;
        $this->customersResource = $customersResource;
        $this->customersCollectionFactory = $customersCollectionFactory;
    }

    /**
     * Create Logic Button Sync Customers
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|(\Magento\Framework\Controller\Result\Redirect&\Magento\Framework\Controller\ResultInterface)|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $fetchedCustomerIds = array_column($this->customers->getAllCustomers(), 'id');
            $existingCustomers = $this->customersCollectionFactory->create()
                ->addFieldToFilter('customer_id', ['nin' => $fetchedCustomerIds]);

            foreach ($existingCustomers as $existingCustomer) {
                $this->customersResource->delete($existingCustomer);
            }

            $customers = $this->customers->getAllCustomers();

            foreach ($customers as $customer) {
                $this->syncCustomer($customer);
            }

            $this->messageManager->addSuccessMessage(__('Customers Fetch Successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        } finally {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath(
                'magento_integration/customers/index'
            );
        }
    }

    /**
     * SyncCustomer for execute
     *
     * @param array $customerData
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function syncCustomer(array $customerData): void
    {
        $existingCustomer = $this->customersCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerData['id'])
            ->getFirstItem();

        $customerModel = $existingCustomer->getId()
            ? $existingCustomer
            : $this->customersFactory->create();

        $customerModel->addData([
            'customer_id' => $customerData['id'],
            'email' => $customerData['email'],
            'firstname' => $customerData['firstname'],
            'lastname' => $customerData['lastname'],
            'created_in' => $customerData['created_in'],
        ]);

        $this->customersResource->save($customerModel);
    }
}
