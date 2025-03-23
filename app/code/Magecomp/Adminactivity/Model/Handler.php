<?php

namespace Magecomp\Adminactivity\Model;

use \Magecomp\Adminactivity\Helper\Fieldhelper;
use \Magento\Framework\App\Request\Http;
use \Magento\Framework\UrlInterface;
use Magecomp\Adminactivity\Model\ActivityLogFactory;

/**
 * Class Handler
 * @package Magecomp\Adminactivity\Model
 */
class Handler
{
    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    public $header;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * @var UrlInterface
     */
    public $urlInterface;

    /**
     * @var ActivityLogFactory
     */
    public $activityLogFactory;
    private Fieldhelper $fieldhelper;

    /**
     * Handler constructor.
     * @param Fieldhelper $fieldhelper
     * @param \Magento\Framework\HTTP\Header $header
     * @param Http $request
     * @param UrlInterface $urlInterface
     * @param ActivityLogFactory $activityLogFactory
     */
    public function __construct(
        Fieldhelper $fieldhelper,
        \Magento\Framework\HTTP\Header $header,
        Http $request,
        UrlInterface $urlInterface,
        ActivityLogFactory $activityLogFactory
    ) {
        $this->fieldhelper = $fieldhelper;
        $this->header = $header;
        $this->request = $request;
        $this->urlInterface = $urlInterface;
        $this->activityLogFactory = $activityLogFactory;
    }

    /**
     * Set log data
     * @param $logs
     * @return mixed
     */
    public function __initLog($logs)
    {
        if (!empty($logs)) {
            foreach ($logs as $field => $value) {
                $log = $this->activityLogFactory->create()->setData($value);
                $log->setFieldName($field);
                $logs[$field] = $log;
            }
        }
        return $logs;
    }

    /**
     * Get add activity log data
     * @param $model
     * @param $method
     * @return mixed
     */
    public function modelAdd($model, $method)
    {
        return $this->__initLog(
            $this->fieldhelper->getAddData($model, $method)
        );
    }

    /**
     * Get edit activity log data
     * @param $model
     * @param $method
     * @return mixed
     */
    public function modelEdit($model, $method)
    {
        return $this->__initLog(
            $this->fieldhelper->getEditData($model, $method)
        );
    }

    /**
     * Get delete activity log data
     * @param $model
     * @param $method
     * @return mixed
     */
    public function modelDelete($model, $method)
    {
        return $this->__initLog(
            $this->fieldhelper->getDeleteData($model, $method)
        );
    }
}
