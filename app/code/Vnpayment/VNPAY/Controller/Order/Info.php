<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnpayment\VNPAY\Controller\Order;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;

class Info extends Action implements HttpGetActionInterface
{
    /**
     * @var OrderRepository
     */
    protected OrderRepository $order;
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;
    /**
     * @var Session
     */
    protected Session $checkoutSession;
    /**
     * @var Json
     */
    protected Json $jsonFac;

    /**
     * @param Context $context
     * @param Json $json
     * @param OrderRepository $order
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Json $json,
        OrderRepository $order,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->jsonFac = $json;
        $this->order = $order;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Controller
     *
     * @return Json
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->order->get($orderId);

        if ($order->getId()) {
            $url = $this->scopeConfig->getValue('payment/vnpay/payment_url');
            $returnUrl = rtrim($this->storeManager->getStore()->getBaseUrl(), "/") . "/paymentvnpay/order/pay";

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $this->scopeConfig->getValue('payment/vnpay/tmn_code'),
                "vnp_Amount" => round($order->getTotalDue() * 100, 0),
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $this->getRequest()->getClientIp(),
                "vnp_Locale" => 'vn',
                "vnp_OrderInfo" => $order->getIncrementId(),
                "vnp_OrderType" => 'other',
                "vnp_ReturnUrl" => $returnUrl,
                "vnp_TxnRef" => $order->getIncrementId(),
            ];

            ksort($inputData);
            $hashData = http_build_query($inputData, '', '&');
            $vnp_Url = $url . '?' . $hashData;

            $secureSecret = $this->scopeConfig->getValue('payment/vnpay/hash_code');
            if (isset($secureSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashData, $secureSecret);
                $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
            }

            $this->jsonFac->setData($vnp_Url);
        }

        return $this->jsonFac;
    }
}
