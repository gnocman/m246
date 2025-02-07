<?php
namespace Magecomp\Adminactivity\Controller\Adminhtml\Adminactivity;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

/**
 * Class Log
 * @package Magecomp\Adminactivity\Controller\Adminhtml\Activity
 */
class Activitylogdetail extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * Log constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;

        parent::__construct($context);
    }

    /**
     * view action
     * @return $this
     */
    public function execute()
    {
        $content = $this->layoutFactory->create()
            ->createBlock(
                \Magecomp\Adminactivity\Block\Adminhtml\Activitylogdetail::class
            );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($content->toHtml());
    }
}
