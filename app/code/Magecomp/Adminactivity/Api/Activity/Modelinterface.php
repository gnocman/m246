<?php

namespace Magecomp\Adminactivity\Api\Activity;

/**
 * Interface ModelInterface
 * @package Magecomp\Adminactivity\Api\Activity
 */
interface Modelinterface
{
    /**
     * Get old data
     * @param $model
     * @return mixed
     */
    public function getOldData($model);

    /**
     * Get edit data
     * @param $model
     * @return mixed
     */
    public function getEditData($model, $fieldArray);
}
