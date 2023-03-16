<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\Test2\Block\Adminhtml\Request;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 *
 */
class Report extends Template
{
    /**
     * @var FullModuleList
     */
    protected $fullModuleList;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $_invoiceCollectionFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory
     */
    protected $_creditmemoCollectionFactory;

    /**
     * @param Context $context
     * @param FullModuleList $fullModuleList
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        FullModuleList $fullModuleList,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollection,
        array $data = []
    ) {
        $this->fullModuleList = $fullModuleList;
        $this->_customerFactory = $customerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_creditmemoCollectionFactory = $creditmemoCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return int|null
     * get All Module in magento2
     */
    public function getCountAllModule()
    {
        return count($this->fullModuleList->getNames());
    }

    /**
     * @return array
     * get Module of you in magento2
     */
    public function getModuleMagento()
    {
        $result = [];
        $modules = $this->fullModuleList->getNames();
        foreach ($modules as $_module) {
            if (str_contains($_module, 'Magento')) {
                $result[] = $_module;
            }
        }
        return $result;
    }

    /**
     * @return int
     * get all customers
     */
    public function getCustomerCollection()
    {
        return $this->_customerFactory->create()->count();
    }

    /**
     * @return int
     * get all products
     */
    public function getProductCollection()
    {
        return $this->_productCollectionFactory->create()->count();
    }

    /**
     * @return int
     * get all orders
     */
    public function getOrderCollection()
    {
        return $this->_orderCollectionFactory->create()->count();
    }

    /**
     * @return int
     * get all invoice
     */
    public function getInvoiceCollection()
    {
        return $this->_invoiceCollectionFactory->create()->count();
    }

    /**
     * @return int
     * get all credit
     */
    public function getCreditCollection()
    {
        return $this->_creditmemoCollectionFactory->create()->count();
    }
}
