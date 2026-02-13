<?php

namespace Magecomp\Adminactivity\Plugin\App;

use Magecomp\Adminactivity\Model\Processor;
use Magecomp\Adminactivity\Helper\Activitytime;
/**
 * Class Action
 * @package Magecomp\Adminactivity\Plugin\App
 */
class Action
{
    /**
     * @var \Magecomp\Adminactivity\Model\Processor
     */
    public $processor;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * Action constructor.
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        Processor $processor,
        Activitytime $activitytime
    ) {
        $this->processor = $processor;
        $this->activitytime = $activitytime;
    }

    /**
     * Get before dispatch data
     * @param \Magento\Framework\Interception\InterceptorInterface $controller
     * @return void
     */
    public function beforeDispatch(\Magento\Framework\Interception\InterceptorInterface $controller)
    {
        $this->activitytime->start(__METHOD__);
        $actionName = $controller->getRequest()->getActionName();
        $fullActionName = $controller->getRequest()->getFullActionName();
        $this->processor->init($fullActionName, $actionName);
        $this->processor->addPageVisitLog($controller->getRequest()->getModuleName());
        $this->activitytime->end(__METHOD__);
    }
}
