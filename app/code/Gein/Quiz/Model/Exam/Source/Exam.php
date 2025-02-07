<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Gein\Quiz\Model\Exam\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Gein\Quiz\Model\ResourceModel\Exam\CollectionFactory;

/**
 * Class Theme
 */
class Exam implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $_examFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $examFactory
     */
    public function __construct(CollectionFactory $examFactory)
    {
        $this->_examFactory = $examFactory;
    }

    /**
     * Get list of all available exams
     *
     * @return array
     */
    public function getAllOptions()
    {
        $collection = $this->_examFactory->create();
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
