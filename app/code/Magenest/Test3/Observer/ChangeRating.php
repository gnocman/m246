<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test3\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class of Observer Change Rating
 */
class ChangeRating implements ObserverInterface
{
    /**
     * Observer Change Rating
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        $movie = $observer->getData('magenest_movie');
        $movie->setData('rating', '0');
        $observer->setData('magenest_movie', $movie);
    }
}
