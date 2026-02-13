<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;

/**
 * Class PostDispatch
 * @package Magecomp\Adminactivity\Observer
 */
class PostDispatch implements ObserverInterface
{
    /**
     * @var \Magecomp\Adminactivity\Model\Processor
     */
    private $processor;

    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * PostDispatch constructor.
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     * @param Helper $helper
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        \Magecomp\Adminactivity\Model\Processor $processor,
        Helper $helper,
        \Magecomp\Adminactivity\Helper\Activitytime $activitytime
    ) {
        $this->processor = $processor;
        $this->helper = $helper;
        $this->activitytime = $activitytime;
    }

    /**
     * Post dispatch
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->activitytime->start(__METHOD__);
        if (!$this->helper->isEnable()) {
            return $observer;
        }
        $this->processor->saveLogs();
        $this->activitytime->end(__METHOD__);
    }
}
