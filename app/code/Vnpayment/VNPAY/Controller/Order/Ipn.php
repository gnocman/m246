<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnpayment\VNPAY\Controller\Order;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Ipn extends Action implements HttpGetActionInterface
{
    /**
     * @var Order
     */
    protected Order $order;
    /**
     * @var Session
     */
    protected Session $checkoutSession;
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;
    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param Order $order
     * @param Session $checkoutSession
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Order $order,
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->order = $order;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Controller
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $vnpSecureHash = $this->getRequest()->getParam('vnp_SecureHash', '');
        $secureSecret = $this->scopeConfig->getValue('payment/vnpay/hash_code');
        $responseParams = $this->getRequest()->getParams();
        $vnpResponseCode = $this->getRequest()->getParam('vnp_ResponseCode', '');
        $inputData = $this->prepareInputData($responseParams);
        $hashData = $this->generateHashData($inputData);
        $returnData = [];

        try {
            if ($this->isValidHash($vnpSecureHash, $hashData, $secureSecret)) {
                $vnpTxnRef = $this->getRequest()->getParam('vnp_TxnRef', '000000000');
                $order = $this->order->loadByIncrementId($vnpTxnRef);

                if ($order->getId()) {
                    if ($order->getStatus() !== null && $order->getStatus() == 'pending') {
                        $amount = $this->getRequest()->getParam('vnp_Amount', '0');
                        $this->updateOrderStatus($order, $vnpResponseCode, $amount);
                        $returnData = ['RspCode' => '00', 'Message' => 'Confirm Success'];
                    } else {
                        $returnData = ['RspCode' => '02', 'Message' => 'Order already confirmed'];
                    }
                } else {
                    $returnData = ['RspCode' => '01', 'Message' => 'Order not found'];
                }
            } else {
                $returnData = ['RspCode' => '97', 'Message' => 'Invalid signature'];
            }
        } catch (Exception $e) {
            $returnData = ['RspCode' => '99', 'Message' => 'Unknown error'];
        }

        $resultJson = $this->jsonFactory->create();
        $resultJson->setData($returnData);

        return $resultJson;
    }

    /**
     * Logic prepareInputData
     *
     * @param $responseParams
     * @return mixed
     */
    protected function prepareInputData($responseParams)
    {
        $inputData = $responseParams;
        unset($inputData['vnp_SecureHashType'], $inputData['vnp_SecureHash']);
        ksort($inputData);

        return $inputData;
    }

    /**
     * Logic generateHashData
     *
     * @param $inputData
     * @return string
     */
    protected function generateHashData($inputData)
    {
        return http_build_query($inputData, '', '&');
    }

    /**
     * Logic isValidHash
     *
     * @param $vnpSecureHash
     * @param $hashData
     * @param $secureSecret
     * @return bool
     */
    protected function isValidHash($vnpSecureHash, $hashData, $secureSecret)
    {
        $generatedHash = hash_hmac('sha512', $hashData, $secureSecret);
        return $vnpSecureHash === $generatedHash;
    }

    /**
     * Logic updateOrderStatus
     *
     * @param $order
     * @param $vnpResponseCode
     * @param $amount
     * @return void
     */
    protected function updateOrderStatus($order, $vnpResponseCode, $amount)
    {
        $order->setTotalPaid(floatval($amount) / 100);

        if ($vnpResponseCode == '00') {
            $orderState = Order::STATE_PROCESSING;
        } else {
            $order->addStatusHistoryComment('Transaction failed');
            $orderState = Order::STATE_CLOSED;
        }
        $order->setState($orderState)->setStatus($orderState);

        $order->save();
    }
}
