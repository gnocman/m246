<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Dev\Grid\Controller\Adminhtml\Category;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Controller MassDelete
 */
class MassDelete extends Action
{
    public const ADMIN_RESOURCE = 'Magento_Catalog::categories';

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var CategoryRepositoryInterface|mixed
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CategoryRepositoryInterface|null $categoryRepository
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CategoryRepositoryInterface $categoryRepository = null
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->categoryRepository = $categoryRepository
            ?: ObjectManager::getInstance()->create(CategoryRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Category delete action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|(\Magento\Framework\Controller\Result\Redirect&\Magento\Framework\Controller\ResultInterface)|\Magento\Framework\Controller\ResultInterface
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found'));
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $categoryDeleted = 0;
        foreach ($collection->getItems() as $category) {
            $this->categoryRepository->delete($category);
            $categoryDeleted++;
        }

        if ($categoryDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $categoryDeleted)
            );
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('dev_grid/index/index');
    }
}
