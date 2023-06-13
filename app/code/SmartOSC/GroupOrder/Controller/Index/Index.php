<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use SmartOSC\GroupOrder\Api\GroupOrderRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Api\CategoryManagementInterface;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var GroupOrderRepositoryInterface
     */
    private GroupOrderRepositoryInterface $groupOrderRepository;
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;
    private CategoryManagementInterface $categoryManagement;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param GroupOrderRepositoryInterface $groupOrderRepository
     * @param UrlInterface $url
     */
    public function __construct(
        Context $context,
        GroupOrderRepositoryInterface $groupOrderRepository,
        UrlInterface $url,
        CategoryManagementInterface $categoryManagement
    ) {
        $this->groupOrderRepository = $groupOrderRepository;
        $this->url = $url;

        parent::__construct($context);
        $this->categoryManagement = $categoryManagement;
    }

    /**
     * Controller redirect to categories
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $groupOrderToken = $this->getRequest()->getParam('key');

//        try {
//            $this->groupOrderRepository->group($groupOrderToken);
//        } catch (LocalizedException $e) {
//            $this->messageManager->addErrorMessage($e->getMessage());
//        }

        $categoryId = 2;
        $subCategories = $this->getSubCategoryByParentID($categoryId);

        $url = '';

        foreach ($subCategories as $subCategory) {
            $subCategoryUrl = $subCategory['url'];
            $url = $subCategoryUrl . '?key=' . $groupOrderToken;
            break;
        }

        return $resultRedirect->setPath($url);
    }

    /**
     * @param int $categoryId
     * @return array
     */
    public function getSubCategoryByParentID(int $categoryId): array
    {
        $categoryData = [];

        $getSubCategory = $this->getCategoryData($categoryId);
        foreach ($getSubCategory->getChildrenData() as $category) {
            $categoryData[$category->getId()] = [
                'url'=> $category->getUrl()
            ];
            if (count($category->getChildrenData())) {
                $getSubCategoryLevelDown = $this->getCategoryData($category->getId());
                foreach ($getSubCategoryLevelDown->getChildrenData() as $subcategory) {
                    $categoryData[$subcategory->getId()]  = [
                        'url'=> $subcategory->getUrl()
                    ];
                }
            }
        }

        return $categoryData;
    }

    /**
     * @param int $categoryId
     * @return CategoryTreeInterface|null
     */
    public function getCategoryData(int $categoryId): ?CategoryTreeInterface
    {
        try {
            $getSubCategory = $this->categoryManagement->getTree($categoryId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $getSubCategory = null;
        }

        return $getSubCategory;
    }
}
