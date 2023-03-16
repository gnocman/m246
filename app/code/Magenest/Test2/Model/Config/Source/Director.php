<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\Test2\Model\Config\Source;

use Magenest\Test2\Model\ResourceModel\Director\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 *
 */
class Director implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '-- Please Select --', 'value' => ''];
        $collection = $this->collectionFactory->create()->load();

        foreach ($collection as $director) {
            $options[] = [
                'label' => $director->getName(),
                'value' => $director->getId(),
            ];
        }

        return $options;
    }
}
