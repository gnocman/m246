<?php

namespace Magecomp\Adminactivity\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magecomp\Adminactivity\Model\Config;
use Magecomp\Adminactivity\Model\ActivityFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\Model\Auth\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Email\Model\Template\Config as Mailconfig;

/**
 * Class Data
 * @package Magecomp\Adminactivity\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements \Magecomp\Adminactivity\Api\Data\Datainterface
{

    /**
     * @var Int
     */
    const ACTIVITY_REVERTABLE = 1;

    /**
     * @var Int
     */
    const ACTIVITY_REVERT_SUCCESS = 2;

    /**
     * @var Int
     */
    const ACTIVITY_FAIL = 3;
    /**
     * @var \Magecomp\Adminactivity\Model\Config
     */
    public $config;

    /**
     * @var array
     */
    public static $wildcardModels = [
        \Magento\Framework\App\Config\Value\Interceptor::class
    ];

    /**
     * Data constructor.
     * @param Context $context
     * @param Config $config
     * @param ActivityFactory $activityFactory
     */
    public function __construct(
        Context $context,
        Config $config,
        ActivityFactory $activityFactory,
        WriterInterface $configWriter,
        TimezoneInterface $timezoneInterface,
        Session $authSession,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        Mailconfig $emailTemplateConfig
    ) {
        $this->config = $config;
        $this->activityFactory = $activityFactory;
        $this->configWriter = $configWriter;
        $this->timezoneInterface = $timezoneInterface;
        $this->authSession = $authSession;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->emailTemplateConfig = $emailTemplateConfig;
        parent::__construct($context);
    }

    /**
     * Check and return status of module
     * @return bool
     */
    public function isEnable()
    {
        $status = $this->scopeConfig->isSetFlag(self::ACTIVITY_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        if ($status == '1') {
            return true;
        }

        return false;
    }

    /**
     * Check and return status for login activity
     * @return bool
     */
    public function isLoginEnable()
    {
        $status = $this->scopeConfig->isSetFlag(self::ACTIVITY_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $loginStatus = $this->scopeConfig
            ->isSetFlag(self::LOGIN_ACTIVITY_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        if ($status == '1' && $loginStatus == '1') {
            return true;
        }

        return false;
    }

    /**
     * Check and return status for page visit history
     * @return bool
     */
    public function isPageVisitEnable()
    {
        $status = $this->scopeConfig->isSetFlag(self::ACTIVITY_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $pageVisitStatus = $this->scopeConfig
            ->isSetFlag(self::PAGE_VISIT_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        if ($status == '1' && $pageVisitStatus == '1') {
            return true;
        }

        return false;
    }

    /**
     * Get value of system config from path
     * @param $path
     * @return bool
     */
    public function getConfigValue($path)
    {
        $moduleValue = $this->scopeConfig->getValue(self::CONFIG_ALLOWED_MODULE,ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $moduleValue=explode(",", strtoupper($moduleValue));
        if(is_array($moduleValue)){
            if(in_array($path, $moduleValue)){
                return true;
            }
        }
        return false;
    }

    /**
     * Get translated label by action name
     * @param string $action
     * @return string
     */
    public function getActionTranslatedLabel($action)
    {
        return $this->config->getActionLabel($action);
    }

    /**
     * Get all actions
     * @return array
     */
    public function getAllActions()
    {
        return $this->config->getActions();
    }

    /**
     * Get activity module name
     * @return bool
     */
    public function getActivityModuleName($module)
    {
        return $this->config->getActivityModuleName($module);
    }

    /**
     * Get module name is valid or not
     * @param $model
     * @return bool
     */
    public static function isWildCardModel($model)
    {
        $model = is_string($model)?$model:get_class($model);
        if (in_array($model, self::$wildcardModels)) {
            return true;
        }
        return false;
    }

    /**
     * Set success revert status
     * @param $activityId
     * @return void
     */
    public function markSuccess($activityId)
    {
        $activityModel = $this->activityFactory->create()->load($activityId);
        $activityModel->setIsRevertable(self::ACTIVITY_REVERT_SUCCESS);
        $activityModel->save();
    }

    /**
     * Set fail revert status
     * @param $activityId
     * @return void
     */
    public function markFail($activityId)
    {
        $activityModel = $this->activityFactory->create()->load($activityId);
        $activityModel->setIsRevertable(self::ACTIVITY_FAIL);
        $activityModel->save();
    }
    public function setConfigDate(){
        $value=$this->timezoneInterface->date()->format('Y-m-d H:i:s');
        $this->configWriter->save('adminactivity/general/clearlogdate',  $value);
    }
    public function getAdminEmail($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_ADMIN_EMAIL,ScopeInterface::SCOPE_STORE, $storeId);
    }
    public function getMailConfig($path,$storeId = null)
    {
        return $this->scopeConfig->getValue($path,ScopeInterface::SCOPE_STORE, $storeId);
    }
    public function getCurrentAdminUser()
    {
        return $this->authSession->getUser()->getUsername();
    }
    public function sendEmail($user)
    {
        $sender = [
            'name' => $this->getMailConfig(self::CONFIG_SENDER_NAME),
            'email' => $this->getMailConfig(self::CONFIG_SENDER_EMAIL),
        ];
        $toEmail=$this->getMailConfig(self::CONFIG_ADMIN_EMAIL);

        try {
            $emailTemplateVariables['admin']='admin';
            $emailTemplateVariables['name']=$user;
            $storeId = $this->storeManager->getStore()->getId();

            $this->inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId,
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($this->getMailConfig(self::CONFIG_EMAIL_TEMPLATE))
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($emailTemplateVariables)
                ->setFromByScope($sender)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }
}
