<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\KnockoutJs\Controller\Test;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Store\Model\StoreManager;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 *
 */
class Product extends Action implements HttpGetActionInterface
{
    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;
    /**
     * @var Image
     */
    protected Image $imageHelper;
    /**
     * @var
     */
    protected $listProduct;
    /**
     * @var StoreManager
     */
    protected StoreManager $_storeManager;

    /**
     * @param Context $context
     * @param FormKey $formKey
     * @param ProductFactory $productFactory
     * @param StoreManager $storeManager
     * @param Image $imageHelper
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        ProductFactory $productFactory,
        StoreManager $storeManager,
        Image $imageHelper
    ) {
        $this->productFactory = $productFactory;
        $this->imageHelper = $imageHelper;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->productFactory->create()
            ->getCollection()
            ->addAttributeToSelect('*')
            ->setPageSize(5)
            ->setCurPage(1);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = $this->productFactory->create()->load($id);

            $productData = [
                'entity_id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'src' => $this->imageHelper->init($product, 'product_base_image')->getUrl(),
            ];

            echo json_encode($productData);
            return;
        }
    }
}
