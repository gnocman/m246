<?php
namespace Magecomp\Adminactivity\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magecomp\Adminactivity\Api\Activityrepositoryinterface;
use Magecomp\Adminactivity\Helper\Browser;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use DateTimeZone;
use DateTime;

/**
 * Class ActivityLogListing
 * @package Magecomp\Adminactivity\Block\Adminhtml
 */
class Activitylogdetail extends Template
{
    /**
     * @var Activityrepositoryinterface
     */
    public $activityRepository;

    /**
     * @var Browser
     */
    public $browser;

    /**
     * Path to template file in theme.
     * @var string
     */
    public $_template = 'Magecomp_Adminactivity::activitylogdetail.phtml';
    
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * ActivityLogListing constructor.
     * @param Template\Context $context
     * @param Activityrepositoryinterface $activityRepository
     * @param Browser $browser
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context                     $context,
        Activityrepositoryinterface $activityRepository,
        Browser                     $browser,
        TimezoneInterface           $timezone
    ) {
        $this->activityRepository = $activityRepository;
        $this->browser = $browser;
        $this->timezone = $timezone;
        parent::__construct($context);
    }

    /**
     * Get admin activity log listing
     * @return array
     */
    public function getLogListing()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->activityRepository->getActivityLog($id);
        return $data->getData();
    }

    /**
     * Get admin activity details
     * @return array
     */
    public function getAdminDetails()
    {
        $id = $this->getRequest()->getParam('id');
        $activity = $this->activityRepository->getActivityById($id);

        //$this->browser->reset();
        $this->browser->setUserAgent($activity->getUserAgent());
        $browser = $this->browser->__toString();
        
        //format datetime based on configuration timezone
        $date = $activity->getUpdatedAt();
        $storeTimezone = $this->timezone->getConfigTimezone();
        $date = new DateTime($date, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($storeTimezone));
        $date = $date->format('Y-m-d H:i:s');

        $logData = [];
        $logData['username'] = $activity->getUsername();
        $logData['module'] = $activity->getModule();
        $logData['name'] = $activity->getName();
        $logData['fullaction'] = $activity->getFullaction();
        $logData['browser'] = $browser;
        $logData['date'] = $date;
        return $logData;
    }
}
