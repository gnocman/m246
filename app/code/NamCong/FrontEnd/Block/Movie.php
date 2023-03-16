<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NamCong\FrontEnd\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;
use NamCong\FrontEnd\Model\ResourceModel\Post\CollectionFactory;

/**
 * Get items to FE
 */
class Movie extends Template
{
    /**
     * @var CollectionFactory
     */
    public CollectionFactory $collection;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(Context $context, CollectionFactory $collectionFactory, array $data = [])
    {
        $this->collection = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get Items
     *
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection->create()->getItems();
//        return $this->collection->create()->getData();
    }

    /**
     * Get Form Action
     *
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->getUrl('frontend/page/save', ['_secure' => true]);
    }
}
