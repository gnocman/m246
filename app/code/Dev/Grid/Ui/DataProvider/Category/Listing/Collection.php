<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Dev\Grid\Ui\DataProvider\Category\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * DataProvider Collection
 */
class Collection extends SearchResult
{

    /**
     * Override _initSelect to add custom columns
     *
     * @return Collection|void
     */
    protected function _initSelect()
    {
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
        $this->addFilterToMap('name', 'devgridname.value');
        parent::_initSelect();
    }
}
