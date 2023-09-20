<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Controller\Adminhtml\Orders;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use SmartOSC\MagentoIntegration\Service\Helper\Orders;
use SmartOSC\MagentoIntegration\Model\OrdersFactory;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Orders as OrderResource;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Orders\CollectionFactory;

class Sync extends Action
{
    /**
     * @var Orders
     */
    private Orders $orders;
    /**
     * @var OrdersFactory
     */
    private OrdersFactory $ordersFactory;
    /**
     * @var OrderResource
     */
    private OrderResource $ordersResource;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $ordersCollectionFactory;

    /**
     * @param Context $context
     * @param Orders $orders
     * @param OrdersFactory $ordersFactory
     * @param OrderResource $ordersResource
     * @param CollectionFactory $ordersCollectionFactory
     */
    public function __construct(
        Context $context,
        Orders $orders,
        OrdersFactory $ordersFactory,
        OrderResource $ordersResource,
        CollectionFactory $ordersCollectionFactory
    ) {
        parent::__construct($context);
        $this->orders = $orders;
        $this->ordersFactory = $ordersFactory;
        $this->ordersResource = $ordersResource;
        $this->ordersCollectionFactory = $ordersCollectionFactory;
    }

    /**
     * Create Logic Button Sync Orders
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|(\Magento\Framework\Controller\Result\Redirect&\Magento\Framework\Controller\ResultInterface)|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $fromDate = $postData['from_date'] ?? null;
        $toDate = $postData['to_date'] ?? null;

        if ($fromDate !== null && $toDate !== null) {
            try {
                $fetchedOrderIds = array_column($this->orders->getAllOrders($fromDate, $toDate), 'increment_id');

                if (empty($fetchedOrderIds)) {
                    $this->messageManager->addErrorMessage(__('There are no orders within this date range.'));
                    return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath(
                        'magento_integration/orders/index'
                    );
                }

                $existingOrders = $this->ordersCollectionFactory->create()
                    ->addFieldToFilter('order_id', ['nin' => $fetchedOrderIds]);

                foreach ($existingOrders as $existingOrder) {
                    $this->ordersResource->delete($existingOrder);
                }

                $orders = $this->orders->getAllOrders($fromDate, $toDate);

                foreach ($orders as $order) {
                    $this->syncOrder($order);
                }

                $this->messageManager->addSuccessMessage(
                    __('Orders Fetch Successfully from %1 to %2', $fromDate, $toDate)
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            } finally {
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath(
                    'magento_integration/orders/index'
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please provide both from_date and to_date.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath(
                'magento_integration/orders/index'
            );
        }
    }

    /**
     * SyncOrder for execute
     *
     * @param array $orderData
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function syncOrder(array $orderData): void
    {
        $existingOrder = $this->ordersCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderData['increment_id'])
            ->getFirstItem();

        $orderModel = $existingOrder->getId()
            ? $existingOrder
            : $this->ordersFactory->create();

        $orderModel->addData([
            'order_id' => $orderData['increment_id'],
            'store_id' => $orderData['store_id'],
            'created_at' => $orderData['created_at'],
            'billing_name' => $orderData['customer_firstname'] . ' ' . $orderData['customer_lastname'],
            'base_grand_total' => $orderData['base_grand_total'],
            'status' => $orderData['status'],
        ]);

        $this->ordersResource->save($orderModel);
    }
}
