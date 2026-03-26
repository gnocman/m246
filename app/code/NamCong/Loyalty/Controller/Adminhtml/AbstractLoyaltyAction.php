<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

abstract class AbstractLoyaltyAction extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('NamCong_Loyalty::loyalty');
    }
}
