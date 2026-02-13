<?php

namespace Magecomp\Adminactivity\Controller\Adminhtml\Adminactivity;

use Magecomp\Adminactivity\Api\Activityrepositoryinterface;
use Magecomp\Adminactivity\Api\Loginrepositoryinterface;
use Magecomp\Adminactivity\Helper\Data as Helper;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Massdelete extends Action
{
    /**
     * Index constructor.
     * @param Context $context
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        Helper                      $helper,
        Activityrepositoryinterface $activityRepository,
        Loginrepositoryinterface $loginRepository,
        DateTime $dateTime
    ) {
        $this->helper = $helper;
        $this->activityRepository = $activityRepository;
        $this->loginRepository = $loginRepository;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    /**
     * Index action
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->clearAdminActivityData();
        $this->clearLoginActivityData();
        $this->helper->setConfigDate();
        $this->messageManager->addSuccess(__("Successfully deleted activity log data"));
        $this->helper->sendEmail($this->helper->getCurrentAdminUser());
    }

    public function clearLoginActivityData()
    {
        try {
            $date = $this->dateTime->gmtDate($this->helper::DATE_FORMAT, $this->dateTime->gmtTimestamp());
            $logactivities = $this->loginRepository->getListBeforeDate($date);
            if (!empty($logactivities)) {
                foreach ($logactivities as $logactivity) {
                    $logactivity->delete();
                }
            }
        }
        catch (\Exception $e){
            $this->messageManager->addError(__("Something went wrong. Try again after sometime"));
        }
    }

    public function clearAdminActivityData()
    {
        try {
            $activities = $this->activityRepository->getList();
            if (!empty($activities)) {
                foreach ($activities as $activity) {
                    $activity->delete();
                }
            }
        } catch (\Exception $exception) {
            $this->messageManager->addError(__("Something went wrong. Try again after sometime"));
        }
    }
}
