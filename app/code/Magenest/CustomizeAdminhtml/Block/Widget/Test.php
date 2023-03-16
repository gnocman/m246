<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomizeAdminhtml\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Call template widget
 */
class Test extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/test.phtml";
}
