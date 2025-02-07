<?php

namespace Magecomp\Adminactivity\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Activitytime
 * @package Magecomp\Adminactivity\Helper
 */
class Activitytime extends AbstractHelper
{

    /**
     * Get Activitytime is enable or not
     */
    const ACTIVITYTIME_ENABLE = 1;

    /**
     * @var String[] Start time of execution
     */
    public $startTime;

    /**
     * @var String[] End time of execution
     */
    public $endTime;

    /**
     * Activitytime constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * log info about start time in millisecond
     * @param $method
     * @return void
     */
    public function start($method)
    {
        $this->reset($method);
        if (self::ACTIVITYTIME_ENABLE) {
            $this->startTime[$method] = round(microtime(true) * 1000);

            \Magento\Framework\Profiler::start($method);
        }
    }

    /**
     * log info about end time and time diiference in millisecond
     * @param $method
     * @return void
     */
    public function end($method)
    {
        if (self::ACTIVITYTIME_ENABLE) {
            $this->endTime[$method] = round(microtime(true) * 1000);
            $difference = $this->endTime[$method] - $this->startTime[$method];

            \Magento\Framework\Profiler::stop($method);
        }
    }

    /**
     * Reset start time and end time
     * @param $method
     * @return void
     */
    public function reset($method)
    {
        $this->startTime[$method] = 0;
        $this->endTime[$method] = 0;
    }
}
