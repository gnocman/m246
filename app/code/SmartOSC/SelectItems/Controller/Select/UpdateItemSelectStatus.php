<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Controller\Select;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\ItemFactory;

class UpdateItemSelectStatus extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;
    /**
     * @var ItemFactory
     */
    private ItemFactory $quoteItemFactory;
    /**
     * @var CartItemRepositoryInterface
     */
    private CartItemRepositoryInterface $cartItemRepository;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CartRepositoryInterface $cartRepository
     * @param ItemFactory $quoteItemFactory
     * @param CartItemRepositoryInterface $cartItemRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CartRepositoryInterface $cartRepository,
        ItemFactory $quoteItemFactory,
        CartItemRepositoryInterface $cartItemRepository
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartRepository = $cartRepository;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * Set value for is_select
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $value = (int)$this->getRequest()->getParam('value');

        $quoteId = $this->quoteItemFactory->create()->load($itemId, 'item_id')->getQuoteId();
        try {
            $quote = $this->cartRepository->getActive($quoteId);
            $quoteItem = $quote->getItemById($itemId);

            if ($quoteItem) {
                $quoteItem->setData('is_select', $value);
                $this->cartItemRepository->save($quoteItem);
            }

            return $result->setData(['success' => true]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
