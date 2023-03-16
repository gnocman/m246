<?php
/**
 * Copyright © NamCong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\RenameProduct\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Rename Product after enable field
 */
class CatalogProductLoadAfter implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Rename Product after enable field
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $enable = $this->_scopeConfig->getValue(
            'smart_renameProduct/general/enable',
            ScopeInterface::SCOPE_STORE
        );
        $newName = $this->_scopeConfig->getValue(
            'smart_renameProduct/general/new_product_name',
            ScopeInterface::SCOPE_STORE
        );

        if ($enable) {
            $observer->getData('product')->setName($newName);
        }
    }
}
