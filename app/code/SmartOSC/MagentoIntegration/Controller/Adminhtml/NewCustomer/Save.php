<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Controller\Adminhtml\NewCustomer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SmartOSC\MagentoIntegration\Model\CustomersFactory;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers as CustomerResource;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers\CollectionFactory;
use SmartOSC\MagentoIntegration\Model\ResourceModel\CustomersFactory as CustomerResourceFactory;
use SmartOSC\MagentoIntegration\Service\Helper\Customers;

class Save extends Action
{
    /**
     * @var CustomersFactory
     */
    private CustomersFactory $customersFactory;
    /**
     * @var CustomerResource
     */
    private CustomerResource $customerResource;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $customerCollectionFactory;
    /**
     * @var CustomerResourceFactory
     */
    private CustomerResourceFactory $customerResourceFactory;
    /**
     * @var Customers
     */
    private Customers $customers;

    /**
     * @param Context $context
     * @param CustomersFactory $customersFactory
     * @param CustomerResource $customerResource
     * @param CollectionFactory $customerCollectionFactory
     * @param CustomerResourceFactory $customerResourceFactory
     * @param Customers $customers
     */
    public function __construct(
        Context $context,
        CustomersFactory $customersFactory,
        CustomerResource $customerResource,
        CollectionFactory $customerCollectionFactory,
        CustomerResourceFactory $customerResourceFactory,
        Customers $customers
    ) {
        parent::__construct($context);
        $this->customersFactory = $customersFactory;
        $this->customerResource = $customerResource;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->customers = $customers;
    }

    /**
     * Create new customer
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $model = $this->customersFactory->create();
            if (!empty($data['entity_id'])) {
                $this->customerResourceFactory->create()->load($model, $data['entity_id']);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('Invalid Customer ID.'));
                    $this->_redirect('magento_integration/newcustomer/index');
                    return;
                }

                $newCustomerData = [
                    "customer" => [
                        "firstname" => $data['firstname'],
                        "lastname" => $data['lastname'],
                        "created_in" => $data['created_in'],
                    ]
                ];

                $this->customers->editCustomer($model->getCustomerId(), $newCustomerData);
            } else {
                $duplicateCustomer = $this->customerCollectionFactory->create()
                    ->addFieldToFilter('email', $data['email'])
                    ->getFirstItem();
                if ($duplicateCustomer->getId()) {
                    $this->messageManager->addErrorMessage(
                        __('Email already exists. Please enter a unique Email.')
                    );
                    $this->_redirect('magento_integration/newcustomer/index');
                    return;
                }

                $newCustomerData = [
                    "customer" => [
                        "email" => $data['email'],
                        "firstname" => $data['firstname'],
                        "lastname" => $data['lastname'],
                        "created_in" => $data['created_in'],
                    ]
                ];

                $this->customers->createCustomer($newCustomerData);
            }

            $fetchedCustomerIds = array_column($this->customers->getAllCustomers(), 'id');
            $existingCustomers = $this->customerCollectionFactory->create()
                ->addFieldToFilter('customer_id', ['nin' => $fetchedCustomerIds]);

            foreach ($existingCustomers as $existingCustomer) {
                $this->customerResource->delete($existingCustomer);
            }

            $customers = $this->customers->getAllCustomers();

            foreach ($customers as $customer) {
                $this->syncCustomer($customer);
            }

            $this->messageManager->addSuccessMessage(__('Customer Saved Successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $this->_redirect('magento_integration/customers/index');
    }

    /**
     * Sync Customer
     *
     * @param array $customerData
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function syncCustomer(array $customerData): void
    {
        $existingCustomer = $this->customerCollectionFactory->create()
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

        $this->customerResource->save($customerModel);
    }
}
