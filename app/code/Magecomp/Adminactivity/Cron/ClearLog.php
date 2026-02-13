<?php

namespace Magecomp\Adminactivity\Cron;

use Psr\Log\LoggerInterface;
use Magecomp\Adminactivity\Helper\Data as Helper;
use Magecomp\Adminactivity\Api\Activityrepositoryinterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magecomp\Adminactivity\Api\Loginrepositoryinterface;

/**
 * Class ClearLog
 * @package Magecomp\Adminactivity\Cron
 */
class ClearLog
{
    /**
     * Default date format
     * @var string
     */

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $dateTime;

    /**
     * @var \Magecomp\Adminactivity\Helper\Data
     */
    public $helper;

    /**
     * @var Activityrepositoryinterface
     */
    public $activityRepository;

    /**
     * @var Loginrepositoryinterface
     */
    public $loginRepository;

    /**
     * ClearLog constructor.
     * @param LoggerInterface $logger
     * @param DateTime $dateTime
     * @param Helper $helper
     * @param Activityrepositoryinterface $activityRepository
     * @param Loginrepositoryinterface $loginRepository
     */
    public function __construct(
        LoggerInterface             $logger,
        DateTime                    $dateTime,
        Helper                      $helper,
        Activityrepositoryinterface $activityRepository,
        Loginrepositoryinterface $loginRepository
    ) {
        $this->logger = $logger;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        $this->activityRepository = $activityRepository;
        $this->loginRepository = $loginRepository;
    }

    /**
     * Return cron cleanup date
     * @return null|string
     */
    public function __getDate()
    {
        $timestamp = $this->dateTime->gmtTimestamp();
        $day = $this->helper->getConfigValue('CLEAR_LOG_DAYS');
        if ($day) {
            $timestamp -= $day * 24 * 60 * 60;
            return $this->dateTime->gmtDate($this->helper::DATE_FORMAT, $timestamp);
        }
        return null;
    }

    /**
     * Delete record which date is less than the current date
     * @return $this|null
     */
    public function execute()
    {
        try {
            if (!$this->helper->isEnable()) {
                return $this;
            }

            if ($date = $this->__getDate()) {
                $activities = $this->activityRepository->getListBeforeDate($date);
                if (!empty($activities)) {
                    foreach ($activities as $activity) {
                        //TODO: Remove activity detail
                        $activity->delete();
                    }
                }

                //TODO: Remove login activity detail
                if ($this->helper->isLoginEnable()) {
                    $activities = $this->loginRepository->getListBeforeDate($date);
                    if (!empty($activities)) {
                        foreach ($activities as $activity) {
                            $activity->delete();
                        }
                    }
                }
                $this->helper->setConfigDate();
                $this->helper->sendEmail("Automatically");
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return null;
    }
}
