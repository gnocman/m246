<?php

namespace Magecomp\Adminactivity\Model;

use Magecomp\Adminactivity\Model\Config\Data;
/**
 * Class Config
 * @package Magecomp\Adminactivity\Model
 */
class Config
{
    /**
     * Merged activitylogdetail.xml config
     * @var array
     */
    public $_xmlConfig;

    /**
     * Translated and sorted labels
     * @var array
     */
    public $_labels = [];

    /**
     * Config constructor.
     * @param Config\Data $dataStorage
     */
    public function __construct(
        Data $dataStorage
    ) {
        $this->_xmlConfig = $dataStorage->get('config');
    }

    /**
     * Get all action labels translated and sorted ASC
     * @return array
     */
    public function getActions()
    {

        if (!$this->_labels && isset($this->_xmlConfig['actions'])) {
            foreach ($this->_xmlConfig['actions'] as $id => $label) {
                $this->_labels[$id] = __($label);
            }
            asort($this->_labels);
        }
        return $this->_labels;
    }

    /**
     * List of all full actions
     * @return array
     */
    public function getControllerActions()
    {

        $actions = [];
        foreach ($this->_xmlConfig as $module => $config) {

            if(isset($config['actions'])) {
                $actions = array_merge($actions, array_keys($config['actions']));
            }
        }
        return $actions;
    }

    /**
     * Get logging action translated label
     * @param string $action
     * @return \Magento\Framework\Phrase|string
     */
    public function getActionLabel($action)
    {
        if (isset($this->_xmlConfig['actions'])
            && array_key_exists(
                $action,
                $this->_xmlConfig['actions']
            )
        ) {
            return __($this->_xmlConfig['actions'][$action]);
        }

        return $action;
    }

    /**
     * Get event by action
     * @param $action
     * @return bool
     */
    public function getEventByAction($action)
    {
        foreach ($this->_xmlConfig as $module => $config) {
            if (isset($config['actions']) && array_key_exists($action, $config['actions'])) {
                return $config['actions'][$action];
            }
        }
        return false;
    }

    /**
     * Return Model class name
     * @param $module
     * @return string
     */
    public function getEventModel($module)
    {
        if (!array_key_exists($module, $this->_xmlConfig)) {
            return false;
        }
        return $this->_xmlConfig[$module]['model'];
    }

    /**
     * Return model label name
     * @param $module
     * @return bool
     */
    public function getActivityModuleName($module)
    {
        if (!array_key_exists($module, $this->_xmlConfig)) {
            return false;
        }

        return $this->_xmlConfig[$module]['label'];
    }

    /**
     * Return model class name
     * @param $module
     * @return string
     */
    public function getTrackFieldModel($module)
    {
        if (!array_key_exists($module, $this->_xmlConfig)) {
            return false;
        }

        return $this->_xmlConfig[$module]['config']['trackfield'];
    }

    /**
     * Return module constant
     * @param $module
     * @return bool
     */
    public function getActivityModuleConstant($module)
    {
        if (!array_key_exists($module, $this->_xmlConfig)) {
            return false;
        }
        return $this->_xmlConfig[$module]['config']['configpath'];
    }

    /**
     * Return module edit url
     * @param $module
     * @return bool
     */
    public function getActivityModuleEditUrl($module)
    {
        if (!array_key_exists($module, $this->_xmlConfig)) {
            return false;
        }
        return $this->_xmlConfig[$module]['config']['editurl'];
    }

    /**
     * Return module item name
     * @param $module
     * @return bool
     */
    public function getActivityModuleItemField($module)
    {
        if (!array_key_exists($module, $this->_xmlConfig)) {
            return false;
        }
        return $this->_xmlConfig[$module]['config']['itemfield'];
    }
}
