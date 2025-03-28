<?php

namespace Magecomp\Adminactivity\Helper;

use Magecomp\Adminactivity\Helper\Confighelper;
use Magento\Framework\DataObject;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
/**
 * Class ThemeConfig
 * @package Magecomp\Adminactivity\Model\Activity
 */
class Themehelper implements \Magecomp\Adminactivity\Api\Activity\Modelinterface
{
    /**
     * @var \Magento\Framework\DataObject
     */
    public $dataObject;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    public $valueFactory;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    public $configWriter;

    /**
     * Request
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;
    private \Magecomp\Adminactivity\Helper\Confighelper $configHelper;

    /**
     * ThemeConfig constructor.
     * @param DataObject $dataObject
     * @param ValueFactory $valueFactory
     * @param RequestInterface $request
     * @param WriterInterface $configWriter
     */
    public function __construct(
        DataObject $dataObject,
        ValueFactory $valueFactory,
        RequestInterface $request,
        WriterInterface $configWriter,
        Confighelper $configHelper
    ) {
        $this->dataObject = $dataObject;
        $this->valueFactory = $valueFactory;
        $this->request = $request;
        $this->configWriter = $configWriter;
        $this->configHelper = $configHelper;
    }

    /**
     * Get config path of theme configuration
     * @param $model
     * @return string
     */
//    public function getPath($model)
//    {
//        if ($model->getData('path')) {
//            return current(
//                explode(
//                    '/',
//                    $model->getData('path')
//                )
//            );
//        }
//        return '';
//    }

    /**
     * Get old activity data of theme configuration
     * @param $model
     * @return mixed
     */
    public function getOldData($model)
    {
        $path = $this->configHelper->getPath($model);
        $systemData = $this->valueFactory->create()->getCollection()
                            ->addFieldToFilter('path', ['like'=> $path.'/%']);

        $data = [];
        foreach ($systemData->getData() as $config) {
            $path = str_replace('design_', '', str_replace('/', '_', $config['path']));
            $data[$path] = $config['value'];
        }
        return $data;
    }

    /**
     * Get edit activity data of theme configuration
     * @param $model
     * @return mixed
     */
    public function getEditData($model, $fieldArray)
    {
        $path = 'stores/scope_id/'.$model->getScopeId();
        $oldData = $this->getOldData($model);
        $newData = $this->request->getPostValue();
        $result = $this->collectAdditionalData($oldData, $newData, $fieldArray);
        $model->setConfig('Theme Configuration');
        $model->setId($path);
        return $result;
    }

    /**
     * Get revert activity data of theme configuration
     * @param $logData
     * @param $scopeId
     * @return bool
     */
//    public function revertData($logData, $scopeId, $scope)
//    {
//        if (!empty($logData)) {
//            foreach ($logData as $log) {
//                $this->configWriter->save(
//                    $log->getFieldName(),
//                    $log->getOldValue(),
//                    $scope,
//                    $scopeId
//                );
//            }
//        }
//        return true;
//    }

    /**
     * Set additional data
     * @param $oldData
     * @param $newData
     * @return array
     */
    public function collectAdditionalData($oldData, $newData, $fieldArray)
    {
        $logData = [];
        foreach ($newData as $key => $value) {
            if (in_array($key, $fieldArray)) {
                continue;
            }
            $newValue = !empty($value) ? $value : '';
            $oldValue = !empty($oldData[$key]) ? $oldData[$key] : '';

            if ($newValue != $oldValue) {
                $key = 'design/'.preg_replace('/_/', '/', $key, 1);
                $logData[$key] = [
                    'old_value' => $oldValue,
                    'new_value'=> $newValue
                ];
            }
        }

        return $logData;
    }
}
