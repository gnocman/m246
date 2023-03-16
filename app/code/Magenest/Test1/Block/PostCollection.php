<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test1\Block;

use Magenest\Test1\Model\ResourceModel\Post\Collection;
use Magenest\Test1\Model\ResourceModel\Post\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Block class that retrieves a collection of posts with actor and directory data.
 */
class PostCollection extends Template
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $postCollectionFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param CollectionFactory $postCollectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $postCollectionFactory
    ) {
        $this->postCollectionFactory = $postCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Retrieves a collection of posts with actor and directory data.
     *
     * @return Collection
     */
    public function getPostsWithActorAndDirectoryData(): Collection
    {
        $collection = $this->postCollectionFactory->create();
        $collection->joinTableToGetActorAndDirectory();

        return $collection;
    }
}
