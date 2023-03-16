<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Block;

use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\View\Element\Template;

/**
 *
 */
class CustomerGroups extends Template
{
    /**
     * @var CustomerGroup
     */
    public CustomerGroup $_customerGroup;

    /**
     * @param CustomerGroup $customerGroup
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        CustomerGroup $customerGroup,
        Template\Context $context,
        array $data = []
    ) {
        $this->_customerGroup = $customerGroup;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCustomerGroup()
    {
        return $this->_customerGroup->toOptionArray();
    }
}
