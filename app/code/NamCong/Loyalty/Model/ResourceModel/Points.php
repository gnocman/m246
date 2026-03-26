<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Points extends AbstractDb
{
    public const MAIN_TABLE = 'loyalty_points';
    public const ID_FIELD   = 'point_id';

    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD);
    }

    /**
     * Load by customer ID.
     */
    public function loadByCustomerId(\Magento\Framework\Model\AbstractModel $object, int $customerId): self
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('customer_id = ?', $customerId)
            ->limit(1);

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);
        return $this;
    }
}
