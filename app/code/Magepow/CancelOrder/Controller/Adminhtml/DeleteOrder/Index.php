<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magepow\CancelOrder\Controller\Adminhtml\DeleteOrder;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Ui\Component\MassAction\Filter as MassActionFilter;

class Index extends Action
{
    /** @var MassActionFilter */
    protected MassActionFilter $massActionFilter;

    /** @var OrderCollectionFactory */
    protected OrderCollectionFactory $orderCollectionFactory;

    /**
     * @param MassActionFilter $massActionFilter
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Context $context
     */
    public function __construct(
        MassActionFilter $massActionFilter,
        OrderCollectionFactory $orderCollectionFactory,
        Context $context
    ) {
        parent::__construct($context);
        $this->massActionFilter = $massActionFilter;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Create controller delete order
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $result = $this->resultRedirectFactory->create();
        try {
            $collection = $this->massActionFilter->getCollection(
                $this->orderCollectionFactory->create()
            );
            $numberOrders = $collection->getSize();

            // Check if all orders are in 'Canceled' state
            $areAllOrdersCanceled = $collection->getFirstItem()->getStatus() === Order::STATE_CANCELED;
            foreach ($collection->getItems() as $order) {
                if ($order->getStatus() !== Order::STATE_CANCELED) {
                    $areAllOrdersCanceled = false;
                    break;
                }
            }

            if ($areAllOrdersCanceled) {
                $collection->walk('delete');
                $this->messageManager->addSuccessMessage(__('%1 orders was successfully deleted.', $numberOrders));
            } else {
                $this->messageManager->addErrorMessage(__('Only orders in "Canceled" state can be deleted.'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $result->setPath('sales/order/index');
        }
        $result->setPath('sales/order/index');

        return $result;
    }
}
