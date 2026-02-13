<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api\Data;

interface AnswerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Result list.
     * @return \Gein\Quiz\Api\Data\AnswerInterface[]
     */
    public function getItems();

    /**
     * Set question_id list.
     * @param \Gein\Quiz\Api\Data\AnswerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

