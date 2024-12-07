<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Levince\PricingPack\Observer;

use Magento\Framework\Event\ObserverInterface;
use Levince\PricingPack\Helper\IsEnable;

class CustomPrice implements ObserverInterface
{
    /**
     * @var IsEnable
     */
    private IsEnable $isEnable;

    /**
     * @param IsEnable $isEnable
     */
    public function __construct(IsEnable $isEnable)
    {
        $this->isEnable = $isEnable;
    }

    /**
     * Pricing Pack
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->isEnable->isEnable()) {
            return;
        }

        $item = $observer->getEvent()->getItem();
        if ($item->getParentItem()) {
            return; // Bỏ qua nếu là item con
        }

        $sku = $item->getSku();
        $qty = $item->getQty();

        if ($sku == 'test1') {
            $retailPrice = $item->getPrice(); // Giá lẻ
            $packPrices = [
                20 => 80,
                10 => 45,
            ];

            $remainingQty = $qty;
            $totalPrice = 0;

            foreach ($packPrices as $packSize => $packPrice) {
                if ($remainingQty >= $packSize) {
                    $packs = intdiv((int)$remainingQty, $packSize);
                    $totalPrice += $packs * $packPrice;
                    $remainingQty %= $packSize;
                }
            }

            if ($remainingQty > 0) {
                $totalPrice += $remainingQty * $retailPrice;
            }

            // Cập nhật giá trị quan trọng
            $item->setCustomPrice($retailPrice);
            $item->setOriginalCustomPrice($retailPrice);
            $item->setRowTotal($totalPrice);
            $item->setBaseRowTotal($totalPrice);
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}
