<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;
use Magecomp\Adminactivity\Api\Activityrepositoryinterface;

/**
 * Class SaveBefore
 * @package Magecomp\Adminactivity\Observer
 */
class SaveBefore implements ObserverInterface
{
    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Magecomp\Adminactivity\Model\Processor
     */
    public $processor;

    /**
     * @var Activityrepositoryinterface
     */
    public $activityRepository;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * SaveBefore constructor.
     * @param Helper $helper
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     * @param Activityrepositoryinterface $activityRepository
     * @param \Magecomp\Adminactivity\Helper\Activitytime $banchmark
     */
    public function __construct(
        Helper                                      $helper,
        \Magecomp\Adminactivity\Model\Processor     $processor,
        Activityrepositoryinterface                 $activityRepository,
        \Magecomp\Adminactivity\Helper\Activitytime $activitytime
    ) {
        $this->helper = $helper;
        $this->processor = $processor;
        $this->activityRepository = $activityRepository;
        $this->activitytime = $activitytime;
    }

    /**
     * Save before
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
        if ($object->getId() == 0) {
            $object->setCheckIfIsNew(true);
        } else {
            $object->setCheckIfIsNew(false);
            if ($this->processor->validate($object)) {
                $origData = $object->getOrigData();
                if (!empty($origData)) {
                    return $observer;
                }
                $data = $this->activityRepository->getOldData($object);
                foreach ($data->getData() as $key => $value) {
                    $object->setOrigData($key, $value);
                }
            }
        }
        $this->activitytime->end(__METHOD__);
        return $observer;
    }
}
