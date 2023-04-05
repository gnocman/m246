<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test2\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Button Reload page
 */
class Button extends Field
{
    /**
     * Get _getElementHtml
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'my_section']);

        return '<button id="' . $element->getId() . '"
                        onclick="location.href=\'' . $url . '\';"
                        type="button"
                        class="action-default scalable save primary">
                        <span>' . $element->getLabel() . '</span>
                </button>';
    }
}
