<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddReturnShieldAttributes implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributes = [
            'return_risk_manual_adjustment' => [
                'type' => 'int',
                'label' => 'Return Risk Manual Adjustment',
                'input' => 'text',
                'default' => 0,
                'note' => 'Optional manual score increase on a 0-100 scale.'
            ],
            'return_risk_override_note' => [
                'type' => 'text',
                'label' => 'Return Risk Merchant Note',
                'input' => 'textarea',
                'note' => 'Short note shown in the risk reasons list.'
            ],
            'return_size_guidance' => [
                'type' => 'text',
                'label' => 'Return Size Guidance',
                'input' => 'textarea',
                'note' => 'Use for fit advice, body measurements, or recommended size framing.'
            ],
            'return_compatibility_notes' => [
                'type' => 'text',
                'label' => 'Return Compatibility Notes',
                'input' => 'textarea',
                'note' => 'Use for compatibility, installation, or setup constraints.'
            ]
        ];

        foreach ($attributes as $code => $attribute) {
            if ($eavSetup->getAttributeId(Product::ENTITY, $code)) {
                continue;
            }

            $eavSetup->addAttribute(
                Product::ENTITY,
                $code,
                [
                    'type' => $attribute['type'],
                    'label' => $attribute['label'],
                    'input' => $attribute['input'],
                    'required' => false,
                    'sort_order' => 300,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'is_html_allowed_on_front' => false,
                    'group' => 'ReturnShield',
                    'note' => $attribute['note'],
                    'default' => $attribute['default'] ?? null
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
