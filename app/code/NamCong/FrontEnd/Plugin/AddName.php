<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NamCong\FrontEnd\Plugin;

use NamCong\FrontEnd\Controller\Page\Save;

/**
 * Set name with "Perfect"
 */
class AddName
{
    /**
     * Set name
     *
     * @param Save $subject
     * @return void
     */
    public function beforeExecute(Save $subject): void
    {
        $name = $subject->getRequest()->getParam('name');
        $subject->getRequest()->setParam('name', $name . ' ' . "Perfect");
    }
}
