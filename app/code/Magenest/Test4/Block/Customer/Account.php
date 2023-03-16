<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test4\Block\Customer;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Information
 */
class Account extends Template
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Customer
     */
    protected Customer $customerModel;

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param SessionFactory $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Customer $customerModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        SessionFactory $customerSession,
        StoreManagerInterface $storeManager,
        Customer $customerModel,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession->create();
        $this->storeManager = $storeManager;
        $this->customerModel = $customerModel;

        parent::__construct($context, $data);

        $collection = $this->getContracts();
        $this->setCollection($collection);
    }

    /**
     * Get Base Url
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get Media Url
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->getBaseUrl() . 'pub/media/';
    }

    /**
     * Get Customer Image Url
     *
     * @param $filePath
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerImageUrl($filePath)
    {
        return $this->getMediaUrl() . 'customer' . $filePath;
    }

    /**
     * Get File Url
     *
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFileUrl()
    {
        $customerData = $this->customerModel->load($this->customerSession->getId());    //lay ra du lieu ra FE
        $url = $customerData->getData('new_attribute');           //chon key new_attribute de lay dung anh
        if (!empty($url)) {

            return $this->getCustomerImageUrl($url);
        }

        return false;
    }
}
