<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;
use \Magecomp\Adminactivity\Api\Activityrepositoryinterface;

/**
 * Class DeleteAfter
 * @package Magecomp\Adminactivity\Observer
 */
class DeleteAfter implements ObserverInterface
{
    /**
     * @var string
     */
    const SYSTEM_CONFIG = 'adminhtml_system_config_save';

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
     * DeleteAfter constructor.
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     * @param Helper $helper
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
        if ($this->processor->validate($object) && ($this->processor->initAction==self::SYSTEM_CONFIG)) {
            $this->processor->modelEditAfter($object);
        }
        $this->processor->modelDeleteAfter($object);
        $this->activitytime->end(__METHOD__);
    }
}
