<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Cron;

use SmartOSC\MagentoIntegration\Service\Helper\Orders;
use SmartOSC\MagentoIntegration\Model\OrdersFactory;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Orders as OrderResource;
use SmartOSC\MagentoIntegration\Model\ResourceModel\Orders\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class SyncOrders
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
     * @var Context
     */
    private Context $context;
    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @param Context $context
     * @param Orders $orders
     * @param OrdersFactory $ordersFactory
     * @param OrderResource $ordersResource
     * @param CollectionFactory $ordersCollectionFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        Orders $orders,
        OrdersFactory $ordersFactory,
        OrderResource $ordersResource,
        CollectionFactory $ordersCollectionFactory,
        ResultFactory $resultFactory
    ) {
        $this->context = $context;
        $this->orders = $orders;
        $this->ordersFactory = $ordersFactory;
        $this->ordersResource = $ordersResource;
        $this->ordersCollectionFactory = $ordersCollectionFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Create Logic Button Sync Orders
     *
     * @return \Magento\Framework\Controller\Result\Redirect|(\Magento\Framework\Controller\Result\Redirect&\Magento\Framework\Controller\ResultInterface)
     */
    public function execute()
    {
        $prevDate = date('Y-m-d', strtotime('-3 days'));
        $currentDate = date('Y-m-d');

        try {
            while ($currentDate <= date('Y-m-d')) {
                $fetchedOrderIds = array_column($this->orders->getAllOrders($prevDate, $currentDate), 'increment_id');

                if (!empty($fetchedOrderIds)) {
                    $existingOrders = $this->ordersCollectionFactory->create()
                        ->addFieldToFilter('order_id', ['nin' => $fetchedOrderIds]);

                    foreach ($existingOrders as $existingOrder) {
                        $this->ordersResource->delete($existingOrder);
                    }

                    $orders = $this->orders->getAllOrders($prevDate, $currentDate);

                    foreach ($orders as $order) {
                        $this->syncOrder($order);
                    }

                    $this->context->getMessageManager()->addSuccessMessage(
                        __('Orders Fetch Successfully from %1 to %2', $prevDate, $currentDate)
                    );
                }

                $currentDate = date('Y-m-d', strtotime('-3 days', strtotime($currentDate)));
                $prevDate = date('Y-m-d', strtotime('-3 days', strtotime($prevDate)));
            }
        } catch (\Exception $e) {
            $this->context->getMessageManager()->addErrorMessage(__($e->getMessage()));
        } finally {
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
