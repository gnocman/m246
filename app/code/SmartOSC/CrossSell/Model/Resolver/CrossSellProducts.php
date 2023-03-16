<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\CrossSell\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Resolver for retrieving cross-sell products of an order item.
 */
class CrossSellProducts implements ResolverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Resolves the cross-sell products of an order item.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param mixed|null $value
     * @param array|null $args
     *
     * @return array
     * @throws GraphQlAuthorizationException If the order ID is missing or invalid.
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $orderId = $args['orderId'] ?? null;

        if (!$orderId) {
            throw new GraphQlAuthorizationException(__('Order ID should be specified'));
        }

        try {
            /** @var OrderInterface $order */
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlAuthorizationException(__('Order does not exist'));
        }

        $crossSellItems = [];

        foreach ($order->getAllVisibleItems() as $orderItem) {
            $productId = $orderItem->getProductId();

            try {
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                // If the product is deleted, continue with the next one
                continue;
            }

            /** @var \Magento\Catalog\Api\Data\ProductInterface[] $crossSellProducts */
            $crossSellProducts = $product->getCrossSellProductCollection()
                ->addAttributeToSelect('*')
                ->load();

            foreach ($crossSellProducts as $crossSellProduct) {
                $crossSellItems[] = [
                    'sku' => $crossSellProduct->getSku(),
                    'name' => $crossSellProduct->getName(),
                    'price' => $crossSellProduct->getPrice(),
                    'image' => $crossSellProduct->getImage(),
                ];
            }
        }

        return ['items' => $crossSellItems];
    }
}
