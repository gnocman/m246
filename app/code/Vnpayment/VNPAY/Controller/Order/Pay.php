<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnpayment\VNPAY\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Sales\Model\Order;

class Pay extends Action implements HttpGetActionInterface
{
    /**
     * @var Order
     */
    protected Order $order;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param Context $context
     * @param Order $order
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Order $order,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->order = $order;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Controller
     *
     * @return Redirect
     */
    public function execute()
    {
        $vnpSecureHash = $this->getRequest()->getParam('vnp_SecureHash', '');
        $secureSecret = $this->scopeConfig->getValue('payment/vnpay/hash_code');
        $responseParams = $this->getRequest()->getParams();
        $vnpResponseCode = $this->getRequest()->getParam('vnp_ResponseCode', '');
        $inputData = $this->prepareInputData($responseParams);
        $hashData = $this->generateHashData($inputData);

        if ($this->isValidHash($vnpSecureHash, $hashData, $secureSecret)) {
            return $this->processPaymentResult($vnpResponseCode);
        } else {
            $this->messageManager->addErrorMessage('Thanh toán qua VNPAY thất bại.');

            return $this->getFailureRedirect();
        }
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
     * Logic processPaymentResult
     *
     * @param $vnpResponseCode
     * @return Redirect
     */
    protected function processPaymentResult($vnpResponseCode)
    {
        if ($vnpResponseCode == '00') {
            $this->messageManager->addSuccessMessage('Thanh toán thành công qua VNPAY');

            return $this->getSuccessRedirect();
        } else {
            $errorMessage = 'Thanh toán qua VNPAY thất bại. ' . $this->getResponseDescription($vnpResponseCode);
            $this->messageManager->addErrorMessage($errorMessage);

            return $this->getFailureRedirect();
        }
    }

    /**
     * Logic getSuccessRedirect
     *
     * @return Redirect
     */
    protected function getSuccessRedirect(): Redirect
    {
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
    }

    /**
     * Logic getFailureRedirect
     *
     * @return Redirect
     */
    protected function getFailureRedirect(): Redirect
    {
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
    }

    /**
     * Logic getResponseDescription
     *
     * @param $responseCode
     * @return string
     */
    protected function getResponseDescription($responseCode): string
    {
        return match ($responseCode) {
            "00" => "Giao dịch thành công",
            "01" => "Giao dịch đã tồn tại",
            "02" => "Merchant không hợp lệ (kiểm tra lại vnp_TmnCode)",
            "03" => "Dữ liệu gửi sang không đúng định dạng",
            "04" => "Khởi tạo GD không thành công do Website đang bị tạm khóa",
            "05" => "Giao dịch không thành công do: Quý khách nhập sai mật khẩu quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch",
            "06" => "Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.",
            "07" => "Giao dịch bị nghi ngờ là giao dịch gian lận",
            "09" => "Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.",
            "10" => "Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần",
            "11" => "Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.",
            "12" => "Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.",
            "51" => "Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.",
            "65" => "Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.",
            "08" => "Giao dịch không thành công do: Hệ thống Ngân hàng đang bảo trì. Xin quý khách tạm thời không thực hiện giao dịch bằng thẻ/tài khoản của Ngân hàng này.",
            "99" => "Có lỗi sảy ra trong quá trình thực hiện giao dịch",
            default => "Giao dịch thất bại - Failured",
        };
    }
}
