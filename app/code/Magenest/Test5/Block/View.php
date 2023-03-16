<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test5\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class View of Block
 */
class View extends Template
{
    /**
     * Prepares layout
     *
     * @return $this
     */
    protected function _prepareLayout(): View
    {
        parent::_prepareLayout();

        return $this;
    }
}
