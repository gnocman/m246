<?php

namespace Magecomp\Adminactivity\Plugin;

use \Magecomp\Adminactivity\Helper\Data as Helper;

/**
 * Class Auth
 * @package Magecomp\Adminactivity\Plugin
 */
class Auth
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
    public $adminactivity;

    /**
     * Auth constructor.
     * @param Helper $helper
     * @param \Magecomp\Adminactivity\Api\Loginrepositoryinterface $loginRepository
     * @param \Magecomp\Adminactivity\Helper\Activitytime $adminactivity
     */
    public function __construct(
        Helper                                               $helper,
        \Magecomp\Adminactivity\Api\Loginrepositoryinterface $loginRepository,
        \Magecomp\Adminactivity\Helper\Activitytime          $adminactivity
    ) {
        $this->helper = $helper;
        $this->loginRepository = $loginRepository;
        $this->adminactivity = $adminactivity;
    }

    /**
     * Track admin logout activity
     * @param \Magento\Backend\Model\Auth $auth
     * @param callable $proceed
     * @return mixed
     */
    public function aroundLogout(\Magento\Backend\Model\Auth $auth, callable $proceed)
    {
        $this->adminactivity->start(__METHOD__);
        if ($this->helper->isLoginEnable()) {
            $user = $auth->getAuthStorage()->getUser();
            $this->loginRepository->setUser($user)->addLogoutLog();
        }
        $result = $proceed();
        $this->adminactivity->end(__METHOD__);
        return $result;
    }
}
