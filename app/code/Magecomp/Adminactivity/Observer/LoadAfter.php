<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadAfter
 * @package Magecomp\Adminactivity\Observer
 */
class LoadAfter implements ObserverInterface
{
    /**
     * @var \Magecomp\Adminactivity\Model\Processor
     */
    private $processor;

    /**
     * @var \Magecomp\Adminactivity\Helper\Data
     */
    public $helper;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * LoadAfter constructor.
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     * @param \Magecomp\Adminactivity\Helper\Data $helper
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        \Magecomp\Adminactivity\Model\Processor $processor,
        \Magecomp\Adminactivity\Helper\Data $helper,
        \Magecomp\Adminactivity\Helper\Activitytime $activitytime
    ) {
        $this->processor = $processor;
        $this->helper = $helper;
        $this->activitytime = $activitytime;
    }

    /**
     * Delete after
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->activitytime->start(__METHOD__);
        if (!$this->helper->isEnable()) {
            return $observer;
        }
        $object = $observer->getEvent()->getObject();
        $this->processor->modelLoadAfter($object);
        $this->activitytime->end(__METHOD__);
    }
}
