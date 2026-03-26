<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rule extends AbstractDb
{
    public const MAIN_TABLE = 'loyalty_rule';
    public const ID_FIELD   = 'rule_id';

    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD);
    }
}
