<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\AddOrderGrid\Model\ResourceModel\Order\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;
use Zend_Db_Expr;

/**
 * Class Order Grid Collection
 */
class Collection extends OrderGridCollection
{
    /**
     * Add field to filter.
     *
     * @param string|array $field
     * @param string|int|array|null $condition
     * @return Collection
     */
    public function addFieldToFilter($field, $condition = null): Collection
    {
        if ($field === 'order_items' && !$this->getFlag('product_filter_added')) {
            // Add the sales/order_item model to this collection
            $this->getSelect()->join(
                [$this->getTable('sales_order_item')],
                "main_table.entity_id = {$this->getTable('sales_order_item')}.order_id",
                []
            );

            // Group by the order id, which is initially what this grid is id'd by
            $this->getSelect()->group('main_table.entity_id');

            // On the products field, let's add the sku and name as filterable fields
            $this->addFieldToFilter([
                "{$this->getTable('sales_order_item')}.sku",
                "{$this->getTable('sales_order_item')}.name",
            ], [
                $condition,
                $condition,
            ]);

            $this->setFlag('product_filter_added', 1);

            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Perform operations after collection load.
     *
     * @return SearchResult
     */
    protected function _afterLoad(): SearchResult
    {
        $items = $this->getColumnValues('entity_id');

        if (count($items)) {
            $connection = $this->getConnection();

            // Build out item sql to add products to the order data
            $select = $connection->select()
                ->from([
                    'sales_order_item' => $this->getTable('sales_order_item'),
                ], [
                    'order_id',
                    'product_skus' => new Zend_Db_Expr('GROUP_CONCAT(`sales_order_item`.sku SEPARATOR "|")'),
                    'product_names' => new Zend_Db_Expr('GROUP_CONCAT(`sales_order_item`.name SEPARATOR "|")'),
                    'product_qtys' => new Zend_Db_Expr('GROUP_CONCAT(`sales_order_item`.qty_ordered SEPARATOR "|")'),
                ])
                ->where('order_id IN (?)', $items)
                ->where('parent_item_id IS NULL') // Eliminate configurable products, otherwise two products show
                ->group('order_id');

            $items = $connection->fetchAll($select);

            // Loop through this sql an add items to related orders
            foreach ($items as $item) {
                $row = $this->getItemById($item['order_id']);
                $productSkus = explode('|', $item['product_skus']);
                $productQtys = explode('|', $item['product_qtys']);
                $productNames = explode('|', $item['product_names']);
                $html = '';

                foreach ($productSkus as $index => $sku) {
                    $html .= sprintf('<div>%d x [%s] %s </div>', $productQtys[$index], $sku, $productNames[$index]);
                }

                $row->setData('order_items', $html);
            }
        }

        return parent::_afterLoad();
    }
}
