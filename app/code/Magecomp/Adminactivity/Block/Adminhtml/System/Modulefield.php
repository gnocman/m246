<?php

namespace Magecomp\Adminactivity\Block\Adminhtml\System;

class Modulefield implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'module_order', 'label' => __('Order')],
			['value' => 'module_product', 'label' => __('Product')],
			['value' => 'module_category', 'label' => __('Category')],
			['value' => 'module_customer', 'label' => __('Customer')],
			['value' => 'module_promotion', 'label' => __('Promotion')],
			['value' => 'module_email', 'label' => __('Email')],
			['value' => 'module_page', 'label' => __('Page')],
			['value' => 'module_block', 'label' => __('Block')],
			['value' => 'page_title', 'label' => __('Widget')],
			['value' => 'module_theme', 'label' => __('Theme')],
			['value' => 'page_title', 'label' => __('System Config')],
			['value' => 'module_attribute', 'label' => __('Attribute')],
			['value' => 'page_title', 'label' => __('Admin User')],
			['value' => 'module_seo', 'label' => __('SEO')]
		];
	}
}
