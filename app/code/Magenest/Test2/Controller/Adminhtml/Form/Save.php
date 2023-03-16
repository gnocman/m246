<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\Test2\Controller\Adminhtml\Form;

use Magenest\Test2\Model\MovieFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 *
 */
class Save extends Action
{
    /**
     * @var MovieFactory
     */
    protected MovieFactory $movieFactory;

    /**
     * @param Context $context
     * @param MovieFactory $MovieFactory
     */
    public function __construct(
        Context $context,
        MovieFactory $MovieFactory
    ) {
        parent::__construct($context);
        $this->movieFactory = $MovieFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            $model = $this->movieFactory->create();

            // Check for duplicate name
            $duplicateMovie = $model->getCollection()
                ->addFieldToFilter('name', $data['name'])
                ->getFirstItem();
            if ($duplicateMovie->getId()) {
                $this->messageManager->addError(__('Movie name already exists. Please enter a unique name.'));
                $this->_redirect('backend/movie/index');
                return;
            }

            $model->addData([
                "name" => $data['name'],
                "description" => $data['description'],
                "rating" => $data['rating'],
                "director_id" => $data['director_id'],
            ]);
//      $this->_eventManager->dispatch('movie_save_before', ['magenest_movie' => $model]);  //events
            $saveData = $model->save();
            if ($saveData) {
                $this->messageManager->addSuccess(__('Insert data Successfully !'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('backend/movie/index');
    }
}
