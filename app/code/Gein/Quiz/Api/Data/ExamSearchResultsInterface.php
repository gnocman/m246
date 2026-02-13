<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api\Data;

interface ExamSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Exam list.
     * @return \Gein\Quiz\Api\Data\ExamInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Gein\Quiz\Api\Data\ExamInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

