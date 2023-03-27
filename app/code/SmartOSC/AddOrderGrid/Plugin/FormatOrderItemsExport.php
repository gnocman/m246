<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\AddOrderGrid\Plugin;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Ui\Model\Export\MetadataProvider;

class FormatOrderItemsExport
{
    /**
     * Format order items into a semicolon-delimited list for order export.
     *
     * @param MetadataProvider $metadataProvider
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     * @return array
     */
    public function beforeGetRowData(
        MetadataProvider $metadataProvider,
        DocumentInterface $document,
        array $fields,
        array $options
    ): array {
        $orderItems = $document->getData('order_items');
        if ($orderItems === null) {
            return [$document, $fields, $options];
        }

        $decodedItems = html_entity_decode($orderItems);
        $explodedItems = explode('</div><div>', $decodedItems);

        foreach ($explodedItems as &$item) {
            $item = trim(strip_tags($item));
        }
        unset($item);

        $implodedItems = implode(';', $explodedItems);
        $document->setData('order_items', $implodedItems);

        return [$document, $fields, $options];
    }
}
