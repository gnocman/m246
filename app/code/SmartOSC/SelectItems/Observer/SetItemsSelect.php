<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use SmartOSC\SelectItems\Helper\Data;
use Magento\Framework\App\RequestInterface;

class SetItemsSelect implements ObserverInterface
{
    /**
     * @var Data
     */
    private Data $isEnabledHelper;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param Data $isEnabledHelper
     * @param RequestInterface $request
     */
    public function __construct(
        Data $isEnabledHelper,
        RequestInterface $request
    ) {
        $this->isEnabledHelper = $isEnabledHelper;
        $this->request = $request;
    }

    /**
     * Observer for sales_quote_address_collect_totals_before
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->isEnabledHelper->isEnabled() && $this->isMatchingPath()) {
            $shippingAssignment = $observer->getShippingAssignment();
            $address = $shippingAssignment->getShipping()->getAddress();

            $allItems = $address->getAllItems();
            $itemsSelect = [];
            foreach ($allItems as $item) {
                if ($item->getData('is_select')) {
                    $itemsSelect[] = $item;
                }
            }

            $shippingAssignment->setItems($itemsSelect);
        }
    }

    /**
     * Check if the current request path matches the specified paths.
     *
     * @return bool
     */
    public function isMatchingPath()
    {
        $path = $this->request->getPathInfo();

        $matchingPaths = [
            '/product/select/updateItemSelectStatus',
            '/rest/default/V1/carts/mine/totals-information',
            '/rest/default/V1/carts/mine/estimate-shipping-methods'
        ];

        return in_array($path, $matchingPaths);
    }
}
