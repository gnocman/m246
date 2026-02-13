<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gein\Quiz\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Gein\Common\Constants\Quiz;

class QuestionType implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $statuses = $this->getAvailableTypes();

        foreach ($statuses as $statusCode => $statusName) {
            $this->options[$statusCode]['label'] = $statusName;
            $this->options[$statusCode]['value'] = $statusCode;
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function getAvailableTypes(): array
    {
        return [
            Quiz::SINGLE_CHOICE => __('Single Choice'),
            Quiz::MULTIPLE_CHOICE => __('Multiple Choice'),
            Quiz::ESSAY => __('Essay')
        ];
    }
}
