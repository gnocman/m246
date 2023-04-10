<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magezon\Deliverydate\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Plugin LayoutProcessorPlugin
 */
class LayoutProcessorPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * LayoutProcessorPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Plugin LayoutProcessorPlugin
     *
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        array $jsLayout
    ) {
        $minDeliveryDate = $this->scopeConfig->getValue(
            'delivery_date/general/min_delivery_date',
            ScopeInterface::SCOPE_STORE
        );
        $maxDeliveryDate = $this->scopeConfig->getValue(
            'delivery_date/general/max_delivery_date',
            ScopeInterface::SCOPE_STORE
        );

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['before-form']['children']['delivery_date'] = [
            'component' => 'Magento_Ui/js/form/element/date',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/date',
                'options' => ['minDate' => $minDeliveryDate, 'maxDate' => $maxDeliveryDate],
                'id' => 'delivery_date'
            ],
            'dataScope' => 'shippingAddress.delivery_date',
            'label' => __('Delivery Date'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['validate-delivery-date' => true],
            'sortOrder' => 200,
            'id' => 'delivery_date',
            'value' => date('Y-m-d', strtotime('+5 days')), // Set default value as current date plus 5 days
        ];

        return $jsLayout;
    }
}
