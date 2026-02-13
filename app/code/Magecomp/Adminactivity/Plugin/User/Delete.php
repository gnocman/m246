<?php

namespace Magecomp\Adminactivity\Plugin\User;

/**
 * Class Delete
 * @package Magecomp\Adminactivity\Plugin\User
 */
class Delete
{
    /**
     * @var \Magecomp\Adminactivity\Helper\Activitytime
     */
    public $activitytime;

    /**
     * Delete constructor.
     * @param \Magecomp\Adminactivity\Helper\Activitytime $activitytime
     */
    public function __construct(
        \Magecomp\Adminactivity\Helper\Activitytime $activitytime
    ) {
        $this->activitytime = $activitytime;
    }
    /**
     * @param \Magento\User\Model\ResourceModel\User $user
     * @param callable $proceed
     * @param $object
     * @return mixed
     */
    public function aroundDelete(\Magento\User\Model\ResourceModel\User $user, callable $proceed, $object)
    {
        $this->activitytime->start(__METHOD__);
        $object->load($object->getId());

        $result = $proceed($object);
        $object->afterDelete();

        $this->activitytime->end(__METHOD__);
        return $result;
    }
}
