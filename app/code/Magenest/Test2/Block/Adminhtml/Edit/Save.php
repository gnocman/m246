<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\Test2\Block\Adminhtml\Edit;

use Magenest\Test2\Block\Adminhtml\Edit\Button\Generic;
use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 *
 */
class Save extends Generic implements ButtonProviderInterface
{
    /**
     * @param Context $context
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(Context $context, PageRepositoryInterface $pageRepository)
    {
        parent::__construct($context, $pageRepository);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 20,
        ];
    }
}
