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
use Magento\Ui\Component\MassAction\Filter;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Customers\CollectionFactory;
use SmartOSC\MagentoIntegration\Service\Helper\Customers;

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
     * @var Customers
     */
    private Customers $customers;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Customers $customers
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Customers $customers
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->customers = $customers;
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
                $this->customers->deleteCustomer($model->getCustomerId());
                $model->delete();
                $count++;
            }
            unset($model);
            $this->messageManager->addSuccessMessage(__('A total of %1 customers(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        } finally {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath(
                'magento_integration/customers/index'
            );
        }
    }
}
