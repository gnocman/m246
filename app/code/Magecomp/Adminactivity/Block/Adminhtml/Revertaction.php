<?php

namespace Magecomp\Adminactivity\Block\Adminhtml;

class Revertaction extends \Magento\Backend\Block\Template
{

    public function getRevertUrl()
    {
        return $this->getUrl('adminactivity/adminactivity/revertaction');
    }
}
