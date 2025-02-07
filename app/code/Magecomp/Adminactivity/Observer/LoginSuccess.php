<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;

/**
 * Class LoginSuccess
 * @package Magecomp\Adminactivity\Observer
 */
class LoginSuccess implements ObserverInterface
{
    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Magecomp\Adminactivity\Api\Loginrepositoryinterface
     */
    public $loginRepository;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * LoginSuccess constructor.
     * @param Helper $helper
     * @param \Magecomp\Adminactivity\Api\Loginrepositoryinterface $loginRepository
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        Helper                                               $helper,
        \Magecomp\Adminactivity\Api\Loginrepositoryinterface $loginRepository,
        \Magecomp\Adminactivity\Helper\Activitytime          $activitytime
    ) {
        $this->helper = $helper;
        $this->loginRepository = $loginRepository;
        $this->activitytime = $activitytime;
    }

    /**
     * Login success
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->activitytime->start(__METHOD__);
        if (!$this->helper->isLoginEnable()) {
            return $observer;
        }

        $this->loginRepository
            ->setUser($observer->getUser())
            ->addSuccessLog();
        $this->activitytime->end(__METHOD__);
    }
}
