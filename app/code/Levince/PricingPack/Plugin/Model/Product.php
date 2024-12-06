<?php

namespace Levince\PricingPack\Plugin\Model;

class Product
{
    public function afterGetFinalPrice(\Magento\Catalog\Model\Product $subject, $result, $qty = null)
    {
        // Giá bán lẻ và giá pack
        $retailPrice = $subject->getPrice(); // Giá bán lẻ (5$)
        $qty = $subject->getQty();
        $packPrices = [
            10 => 45, // Giá pack 10 cái
            20 => 80, // Giá pack 20 cái
        ];

        if ($qty) {
            $remainingQty = $qty; // Số lượng còn lại để tính
            $totalPrice = 0;

            // Sắp xếp giá pack từ lớn đến nhỏ
            foreach (array_reverse($packPrices, true) as $packSize => $packPrice) {
                if ($remainingQty >= $packSize) {
                    // Số lượng pack có thể mua
                    $packs = intdiv($remainingQty, $packSize);
                    // Tính tổng giá cho các pack này
                    $totalPrice += $packs * $packPrice;
                    // Cập nhật lại số lượng còn lại
                    $remainingQty %= $packSize;
                }
            }

            // Tính giá bán lẻ cho số lượng lẻ còn lại
            if ($remainingQty > 0) {
                $totalPrice += $remainingQty * $retailPrice;
            }

            return $totalPrice;
        }

        return $result;
    }
}
