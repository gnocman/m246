<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gein\Quiz\Api\Data;

interface QuestionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Question list.
     * @return \Gein\Quiz\Api\Data\QuestionInterface[]
     */
    public function getItems();

    /**
     * Set content list.
     * @param \Gein\Quiz\Api\Data\QuestionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

