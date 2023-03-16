<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace NamCong\FrontEnd\Controller\Page;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use NamCong\FrontEnd\Model\PostFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 *  Save data to db
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;
    /**
     * @var PostFactory
     */
    private PostFactory $postFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PostFactory $postFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->postFactory = $postFactory;
    }

    /**
     * Check value and save data
     *
     * @return ResponseInterface|Redirect|Redirect&ResultInterface|ResultInterface
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            $check = $data['rating'];
            if (0 < $check && $check < 6) {
                $existingModel = $this->postFactory->create()->load($data['name'], 'name');
                if ($existingModel->getId()) {
                    $this->messageManager->addErrorMessage(__('Name already exists, please choose another.'));
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
                $model = $this->postFactory->create();
                $model->addData([
                    "name" => $data['name'],
                    "description" => $data['description'],
                    "rating" => $data['rating'],
                ]);
                $model->save();
                $this->messageManager->addSuccessMessage(__("Data Saved Successfully."));
            } else {
                $this->messageManager->addErrorMessage(__('Insert data Error,Please Checking Entering ... !'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can't submit your request, Please try again."));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
