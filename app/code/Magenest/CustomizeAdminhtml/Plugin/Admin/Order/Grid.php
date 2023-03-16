<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomizeAdminhtml\Plugin\Admin\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;

/**
 * Get name
 */
class Grid extends \Magento\Framework\Data\Collection
{
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $coreResource;
    /**
     * @var UserCollection
     */
    protected UserCollection $adminUsers;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param ResourceConnection $coreResource
     * @param UserCollection $adminUsers
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        ResourceConnection $coreResource,
        UserCollection $adminUsers
    ) {
        parent::__construct($entityFactory);
        $this->coreResource = $coreResource;
        $this->adminUsers = $adminUsers;
    }

    /**
     * @param $printQuery
     * @param $logQuery
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    public function beforeLoad($printQuery = false, $logQuery = false)
    {
        if ($printQuery instanceof Collection) {
            $collection = $printQuery;

            $joined_tables = array_keys(
                $collection->getSelect()->getPart('from')
            );

            $collection->getSelect()
                ->columns(
                    [
                        'name' => new \Zend_Db_Expr('(SELECT GROUP_CONCAT(`name` SEPARATOR " & ") FROM `sales_order_item` WHERE `sales_order_item`.`order_id` = main_table.`entity_id` GROUP BY `sales_order_item`.`order_id`)')
                    ]
                );
        }
    }
}
