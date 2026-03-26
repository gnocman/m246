<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\ViewModel;

use NamCong\ReturnShield\Model\Config;
use NamCong\ReturnShield\Service\RiskAnalyzer;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CartRisk implements ArgumentInterface
{
    /**
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $items = null;

    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly RiskAnalyzer $riskAnalyzer,
        private readonly Context $httpContext,
        private readonly Config $config
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getItems(): array
    {
        if ($this->items !== null) {
            return $this->items;
        }

        if (!$this->isEnabled()) {
            $this->items = [];
            return $this->items;
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote->getId()) {
            $this->items = [];
            return $this->items;
        }

        $items = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $analysis = $this->riskAnalyzer->analyze($item->getProduct(), $this->getCustomerGroupId());
            $items[] = [
                'name' => (string)$item->getName(),
                'qty' => (float)$item->getQty(),
                'score' => $analysis->getScore(),
                'label' => $analysis->getLabel(),
                'reasons' => $analysis->getReasons(),
                'recommendations' => $analysis->getRecommendations()
            ];
        }

        usort(
            $items,
            static fn(array $left, array $right): int => $right['score'] <=> $left['score']
        );

        $this->items = $items;
        return $this->items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFlaggedItems(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->getItems(),
                fn(array $item): bool => (int)$item['score'] >= $this->config->getMediumThreshold()
            )
        );
    }

    public function hasFlaggedItems(): bool
    {
        return $this->getFlaggedItems() !== [];
    }

    public function shouldRender(): bool
    {
        return $this->getItems() !== [];
    }

    private function getCustomerGroupId(): int
    {
        return (int)$this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }
}
