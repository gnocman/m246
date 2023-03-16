<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CustomUI\Controller\Adminhtml\NewFilm;

use SmartOSC\CustomUI\Model\FilmFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Controller Save data
 */
class Save extends Action
{
    /**
     * @var FilmFactory
     */
    private FilmFactory $filmFactory;

    /**
     * @param Context $context
     * @param FilmFactory $filmFactory
     */
    public function __construct(Context $context, FilmFactory $filmFactory)
    {
        parent::__construct($context);
        $this->filmFactory = $filmFactory;
    }

    /**
     * Check and Save data
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            $model = $this->filmFactory->create();
            if (!empty($data['film_id'])) {
                $model->load($data['film_id']);
                if (!$model->getId()) {
                    $this->messageManager->addError(__('Invalid Film ID.'));
                    $this->_redirect('backend/film/index');
                    return;
                }
            }

            // Check for duplicate name
            $duplicateFilm = $model->getCollection()
                ->addFieldToFilter('name', $data['name'])
                ->getFirstItem();
            if ($duplicateFilm->getId()) {
                $this->messageManager->addError(__('Film name already exists. Please enter a unique name.'));
                $this->_redirect('backend/film/index');
                return;
            }

            $model->addData([
                "name" => $data['name'],
                "description" => $data['description'],
                "rating" => $data['rating'],
            ]);
            $saveData = $model->save();
            if ($saveData) {
                $this->messageManager->addSuccess(__('Data saved successfully !'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('backend/film/index');
    }
}
