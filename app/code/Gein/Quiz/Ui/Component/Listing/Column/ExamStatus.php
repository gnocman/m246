<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gein\Quiz\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Gein\Common\Constants\Quiz;

class ExamStatus implements OptionSourceInterface
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

        $statuses = $this->getAvailableStatuses();

        foreach ($statuses as $statusCode => $statusName) {
            $this->options[$statusCode]['label'] = $statusName;
            $this->options[$statusCode]['value'] = $statusCode;
        }

        return $this->options;
    }

    /**
     * @return array
     */
    private function getAvailableStatuses(): array
    {
        return [
            Quiz::DRAFT_EXAM_STATUS => __('Draft'),
            Quiz::READY_EXAM_STATUS => __('Ready'),
            Quiz::PENDING_EXAM_STATUS => __('Pending'),
            Quiz::FINISH_EXAM_STATUS => __('Finished')
        ];
    }
}
