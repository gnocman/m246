<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;

/**
 * Class SaveAfter
 * @package Magecomp\Adminactivity\Observer
 */
class SaveAfter implements ObserverInterface
{
    /**
     * @var string
     */
    const ACTION_MASSCANCEL = 'massCancel';

    /**
     * @var string
     */
    const SYSTEM_CONFIG = 'adminhtml_system_config_save';

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
     * SaveAfter constructor.
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
     * Save after
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer|boolean
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->activitytime->start(__METHOD__);

        if (!$this->helper->isEnable()) {
            return $observer;
        }
        $object = $observer->getEvent()->getObject();
        if ($object->getCheckIfIsNew()) {
            if ($this->processor->initAction==self::SYSTEM_CONFIG) {
                $this->processor->modelEditAfter($object);
            }
            $this->processor->modelAddAfter($object);
        } else {
            if ($this->processor->validate($object)) {
                if ($this->processor->eventConfig['action']==self::ACTION_MASSCANCEL) {
                    $this->processor->modelDeleteAfter($object);
                }
                $this->processor->modelEditAfter($object);
            }
        }
        $this->activitytime->end(__METHOD__);
        return true;
    }
}
