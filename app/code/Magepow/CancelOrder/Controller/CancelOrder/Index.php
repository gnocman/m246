<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magepow\CancelOrder\Controller\CancelOrder;

use Magepow\CancelOrder\Helper\Data;

use Exception;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Pricing\Helper\Data as dataPrice;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;
    /**
     * @var dataPrice
     */
    protected dataPrice $priceHelper;
    /**
     * @var Order
     */
    protected Order $order;
    /**
     * @var Data
     */
    protected Data $helper;
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;
    /**
     * @var Session
     */
    protected Session $customerSession;
    /**
     * @var LoggerInterface
     */
    private mixed $logger;

    /**
     * @param Context $context
     * @param dataPrice $priceHelper
     * @param TransportBuilder $transportBuilder
     * @param Order $order
     * @param Session $customerSession
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        dataPrice $priceHelper,
        TransportBuilder $transportBuilder,
        Order $order,
        Session $customerSession,
        Data $helper,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger = null
    ) {
        $this->priceHelper = $priceHelper;
        $this->order = $order;
        $this->customerSession = $customerSession;
        $this->transportBuilder = $transportBuilder;
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);

        return parent::__construct($context);
    }

    /**
     * Controller create delete order
     *
     * @return ResponseInterface|Redirect|(Redirect&ResultInterface)|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $orderId = $this->getRequest()->getParam('orderid');
        $order = $this->order->load($orderId);

        if (!$order->getId()) {
            $this->messageManager->addErrorMessage(__('Order not found.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $productId = [];
        foreach ($order->getAllItems() as $item) {
            $productId[] = $item->getProductId();
        }
        $productCollection = $this->collectionFactory->create();
        $productCollection->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $productId]);

        $products = $productCollection->getItems();

        $post = [
            'collectionProduct' => $products,
            'store_name' => $order->getStore()->getName(),
            'site_name' => $order->getStore()->getWebsite()->getName(),
            'entity_id' => $order->getId(),
            'base_grand_total' => $this->priceHelper->currency($order->getBaseGrandTotal(), true, false),
            'created_at' => $order->getCreatedAt(),
            'customer_lastname' => $order->getCustomerLastname(),
            'orderid' => $order->getIncrementId(),
        ];

        if ($order->canCancel()) {
            $order->cancel();
            $order->save();
            $this->messageManager->addSuccessMessage(__('Order has been canceled successfully.'));

            if ($this->helper->getEmailSender()) {
                $customerData = $this->customerSession->getCustomer();
                $senderName = $customerData->getName();
                $senderEmail = $customerData->getEmail();
                $sender = [
                    'name' => $senderName,
                    'email' => $this->helper->getEmailSender(),
                ];

                $transport = $this->transportBuilder->setTemplateIdentifier('cancel_order_email_template')
                    ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID])
                    ->setTemplateVars($post)
                    ->setFrom($sender)
                    ->addTo($senderEmail);

                if ($this->helper->getEmailSeller()) {
                    $transport->addCc($this->helper->getEmailSeller());
                }

                try {
                    $transport->getTransport()->sendMessage();
                } catch (Exception $e) {
                    $this->logger->critical($e);
                }
            }
        } else {
            $this->messageManager->addErrorMessage(__('Order cannot be canceled.'));
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
