<?php

namespace Magecomp\Adminactivity\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magecomp\Adminactivity\Helper\Data as Helper;

/**
 * Class LoginFailed
 * @package Magecomp\Adminactivity\Observer
 */
class LoginFailed implements ObserverInterface
{
    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Magento\User\Model\User
     */
    public $user;

    /**
     * @var \Magecomp\Adminactivity\Api\Loginrepositoryinterface
     */
    public $loginRepository;

    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * LoginFailed constructor.
     * @param Helper $helper
     * @param \Magento\User\Model\User $user
     * @param \Magecomp\Adminactivity\Api\Loginrepositoryinterface $loginRepository
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        Helper                                               $helper,
        \Magento\User\Model\User                             $user,
        \Magecomp\Adminactivity\Api\Loginrepositoryinterface $loginRepository,
        \Magecomp\Adminactivity\Helper\Activitytime          $activitytime
    ) {
        $this->helper = $helper;
        $this->user = $user;
        $this->loginRepository = $loginRepository;
        $this->activitytime = $activitytime;
    }

    /**
     * Login failed
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->activitytime->start(__METHOD__);
        if (!$this->helper->isLoginEnable()) {
            return $observer;
        }

        $user = null;
        if ($observer->getUserName()) {
            $user = $this->user->loadByUsername($observer->getUserName());
        }

        $this->loginRepository
            ->setUser($user)
            ->addFailedLog($observer->getException()->getMessage());
        $this->activitytime->end(__METHOD__);
    }
}
