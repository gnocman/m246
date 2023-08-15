<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Controller\Adminhtml\Accounts;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use SmartOSC\ShopeeIntegration\Model\ResourceModel\Accounts\CollectionFactory;

class MassDelete extends Action
{
    /**
     * @var CollectionFactory
     */
    public CollectionFactory $collectionFactory;
    /**
     * @var Filter
     */
    public Filter $filter;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Controller Mass Delete
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|(\Magento\Framework\Controller\Result\Redirect&\Magento\Framework\Controller\ResultInterface)|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $count = 0;
            foreach ($collection as $model) {
                $model->delete();
                $count++;
            }
            unset($model);
            $this->messageManager->addSuccessMessage(__('A total of %1 accounts(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        } finally {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('shopee_integration/accounts/index');
        }
    }
}