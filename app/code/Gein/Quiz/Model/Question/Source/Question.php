<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Gein\Quiz\Model\Question\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Gein\Quiz\Model\ResourceModel\Question\CollectionFactory;

/**
 * Class Theme
 */
class Question implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $_questionCollectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $questionCollectionFactory
     */
    public function __construct(CollectionFactory $questionCollectionFactory)
    {
        $this->_questionCollectionFactory = $questionCollectionFactory;
    }

    /**
     * Get list of all available questions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $collection = $this->_questionCollectionFactory->create();
        return $collection->load()->toOptionArray();
    }

    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        $options[] = ['label' => 'Default', 'value' => ''];
        return array_merge($options, $this->getAllOptions());
    }
}
