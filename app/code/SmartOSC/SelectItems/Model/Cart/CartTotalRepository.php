<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\SelectItems\Model\Cart;

use Magento\Quote\Api;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Quote\Api\Data\TotalsInterface as QuoteTotalsInterface;
use Magento\Quote\Model\Cart\TotalsConverter;

/**
 * Cart totals data object.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartTotalRepository implements CartTotalRepositoryInterface
{
    /**
     * Cart totals factory.
     *
     * @var Api\Data\TotalsInterfaceFactory
     */
    private $totalsFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ItemConverter
     */
    private $itemConverter;

    /**
     * @var CouponManagementInterface
     */
    protected $couponService;

    /**
     * @var TotalsConverter
     */
    protected $totalsConverter;

    /**
     * @param Api\Data\TotalsInterfaceFactory $totalsFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CouponManagementInterface $couponService
     * @param TotalsConverter $totalsConverter
     * @param ItemConverter $converter
     */
    public function __construct(
        Api\Data\TotalsInterfaceFactory $totalsFactory,
        CartRepositoryInterface $quoteRepository,
        DataObjectHelper $dataObjectHelper,
        CouponManagementInterface $couponService,
        TotalsConverter $totalsConverter,
        ItemConverter $converter
    ) {
        $this->totalsFactory = $totalsFactory;
        $this->quoteRepository = $quoteRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->couponService = $couponService;
        $this->totalsConverter = $totalsConverter;
        $this->itemConverter = $converter;
    }

    /**
     * @inheritdoc
     */
    public function get($cartId): QuoteTotalsInterface
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual()) {
            $quote->collectTotals();
            $addressTotalsData = $quote->getBillingAddress()->getData();
            $addressTotals = $quote->getBillingAddress()->getTotals();
        } else {
            $addressTotalsData = $quote->getShippingAddress()->getData();
            $addressTotals = $quote->getShippingAddress()->getTotals();
        }
        unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        /** @var QuoteTotalsInterface $quoteTotals */
        $quoteTotals = $this->totalsFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteTotals,
            $addressTotalsData,
            QuoteTotalsInterface::class
        );
        $allItems = $quote->getAllVisibleItems();
        $itemsSelect = [];
        $itemsQty = [];
        foreach ($allItems as $item) {
            if ($item->getData('is_select')) {
                $itemsSelect[] = $item;
                $itemsQty[] = $item->getQty();
            }
        }

        $totalQty = array_sum($itemsQty);

        $items = array_map([$this->itemConverter, 'modelToDataObject'], $itemsSelect);
        $calculatedTotals = $this->totalsConverter->process($addressTotals);
        $quoteTotals->setTotalSegments($calculatedTotals);
        $quoteTotals->setCouponCode($this->couponService->get($cartId));
        $quoteTotals->setItems($items);
        $quoteTotals->setItemsQty($totalQty);
        $quoteTotals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
        $quoteTotals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
        return $quoteTotals;
    }
}
