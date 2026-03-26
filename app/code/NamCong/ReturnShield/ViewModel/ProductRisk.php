<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\ViewModel;

use NamCong\ReturnShield\Model\Config;
use NamCong\ReturnShield\Model\RiskAnalysis;
use NamCong\ReturnShield\Service\RiskAnalyzer;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductRisk implements ArgumentInterface
{
    private ?RiskAnalysis $analysis = null;

    public function __construct(
        private readonly Registry $registry,
        private readonly RiskAnalyzer $riskAnalyzer,
        private readonly Context $httpContext,
        private readonly Config $config
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    public function getAnalysis(): ?RiskAnalysis
    {
        if (!$this->isEnabled()) {
            return null;
        }

        if ($this->analysis instanceof RiskAnalysis) {
            return $this->analysis;
        }

        $product = $this->registry->registry('current_product');
        if (!$product instanceof ProductInterface) {
            return null;
        }

        $this->analysis = $this->riskAnalyzer->analyze($product, $this->getCustomerGroupId());

        return $this->analysis;
    }

    public function shouldRender(): bool
    {
        $analysis = $this->getAnalysis();
        return $analysis !== null;
    }

    private function getCustomerGroupId(): int
    {
        return (int)$this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }
}
