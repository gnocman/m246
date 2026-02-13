<?php

namespace Magecomp\Adminactivity\Controller\Adminhtml\Adminactivity;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magecomp\Adminactivity\Model\Processor;

/**
 * Class Revert
 * @package Magecomp\Adminactivity\Controller\Adminhtml\Activity
 */
class Revertaction extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var \Magecomp\Adminactivity\Model\Processor
     */
    public $processor;

    /**
     * Revert constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magecomp\Adminactivity\Model\Processor $processor
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Processor $processor
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->processor = $processor;
    }

    /**
     * Revert action
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $activityId = $this->getRequest()->getParam('id');
        $result = $this->processor->revertActivity($activityId);
        return $this->resultJsonFactory->create()->setData($result);
    }
}
