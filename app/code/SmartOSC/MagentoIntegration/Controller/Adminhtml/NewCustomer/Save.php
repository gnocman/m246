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
     * @param Context $context
     * @param CustomersFactory $customersFactory
     * @param CustomerResource $customerResource
     * @param CollectionFactory $customerCollectionFactory
     * @param CustomerResourceFactory $customerResourceFactory
     */
    public function __construct(
        Context $context,
        CustomersFactory $customersFactory,
        CustomerResource $customerResource,
        CollectionFactory $customerCollectionFactory,
        CustomerResourceFactory $customerResourceFactory
    ) {
        parent::__construct($context);
        $this->customersFactory = $customersFactory;
        $this->customerResource = $customerResource;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerResourceFactory = $customerResourceFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            $model = $this->customersFactory->create();
            if (!empty($data['entity_id'])) {
                $this->customerResourceFactory->create()->load($model, $data['entity_id']);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('Invalid Customer ID.'));
                    $this->_redirect('magento_integration/newcustomer/index');
                    return;
                }

                $model->addData([
                    "customer_name" => $data['customer_name'],
                    "customer_status" => $data['customer_status'],
                ]);
            } else {
                // Check for duplicate name
                $duplicateCustomer = $this->customerCollectionFactory->create()
                    ->addFieldToFilter('customer_name', $data['customer_name'])
                    ->getFirstItem();
                if ($duplicateCustomer->getId() && ($duplicateCustomer->getId() != $model->getId())) {
                    $this->messageManager->addErrorMessage(
                        __('Customer code already exists. Please enter a unique Customer code.')
                    );
                    $this->_redirect('magento_integration/newcustomer/index');
                    return;
                }

                $model->addData([
                    "customer_name" => $data['customer_name'],
                    "base_url" => $data['base_url'],
                    "access_token" => $data['access_token'],
                    "customer_status" => $data['customer_status'],
                ]);
            }

            $this->customerResource->save($model);

            $this->messageManager->addSuccessMessage(__('Customer Saved Successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $this->_redirect('magento_integration/customers/index');
    }
}
