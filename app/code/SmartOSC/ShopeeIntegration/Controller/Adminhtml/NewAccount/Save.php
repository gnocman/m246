<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Controller\Adminhtml\NewAccount;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SmartOSC\ShopeeIntegration\Model\AccountsFactory;
use SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts as AccountResource;
use SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts\CollectionFactory;
use SmartOSC\ShopeeIntegration\Model\ResourceModel\AccountsFactory as AccountResourceFactory;

class Save extends Action
{
    /**
     * @var AccountsFactory
     */
    private AccountsFactory $accountsFactory;
    /**
     * @var AccountResource
     */
    private AccountResource $accountResource;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $accountCollectionFactory;
    /**
     * @var AccountResourceFactory
     */
    private AccountResourceFactory $accountResourceFactory;

    /**
     * @param Context $context
     * @param AccountsFactory $accountsFactory
     * @param AccountResource $accountResource
     * @param CollectionFactory $accountCollectionFactory
     * @param AccountResourceFactory $accountResourceFactory
     */
    public function __construct(
        Context $context,
        AccountsFactory $accountsFactory,
        AccountResource $accountResource,
        CollectionFactory $accountCollectionFactory,
        AccountResourceFactory $accountResourceFactory
    ) {
        parent::__construct($context);
        $this->accountsFactory = $accountsFactory;
        $this->accountResource = $accountResource;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->accountResourceFactory = $accountResourceFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            $model = $this->accountsFactory->create();
            if (!empty($data['account_id'])) {
                $this->accountResourceFactory->create()->load($model, $data['account_id']);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('Invalid Account ID.'));
                    $this->_redirect('shopee_integration/newaccount/index');
                    return;
                }
            }

            // Check for duplicate name
            $duplicateAccount = $this->accountCollectionFactory->create()
                ->addFieldToFilter('account_code', $data['account_code'])
                ->getFirstItem();
            if ($duplicateAccount->getId() && ($duplicateAccount->getId() != $model->getId())) {
                $this->messageManager->addErrorMessage(__('Account code already exists. Please enter a unique Account code.'));
                $this->_redirect('shopee_integration/newaccount/index');
                return;
            }

            $model->setData([
                "account_code" => $data['account_code'],
                "shop_id" => $data['shop_id'],
                "account_status" => $data['account_status'],
                "valid_invalid" => $data['valid_invalid'],
                "magento_store" => $data['magento_store'],
            ]);

            $this->accountResource->save($model);

            $this->messageManager->addSuccessMessage(__('Data saved successfully !'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $this->_redirect('shopee_integration/accounts/index');
    }
}
