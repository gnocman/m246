<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\DatabaseEAV\Setup\Patch\Data;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResourceModel;
use Magenest\DatabaseEAV\Model\Config\Source\AddressClassification;

/**
 * EAV AddAddressClassificationAttribute
 */
class AddAddressClassificationAttribute implements DataPatchInterface
{
    public const ATTRIBUTE_CODE = 'address_classification';

    /**
     * @var AttributeResourceModel
     */
    private AttributeResourceModel $attributeResourceModel;
    /**
     * @var EavConfig
     */
    private EavConfig $eavConfig;
    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param AttributeResourceModel $attributeResourceModel
     * @param EavConfig $eavConfig
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        AttributeResourceModel $attributeResourceModel,
        EavConfig $eavConfig,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->attributeResourceModel = $attributeResourceModel;
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Get getDependencies
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get getAliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Add new attribute code
     *
     * @return $this
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws \Magento\Framework\Validator\ValidateException
     */
    public function apply(): self
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            self::ATTRIBUTE_CODE,
            [
                'type' => 'int',
                'label' => 'Address Classification',
                'input' => 'select',
                'source' => AddressClassification::class,
                'required' => true,
                'default' => 0,
                'system' => false,
                'position' => 150,
                'sort_order' => 150,
            ]
        );
        $attribute = $this->eavConfig->getAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            self::ATTRIBUTE_CODE
        );
        $attribute->setData('used_in_forms', [
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address',
        ]);
        $this->attributeResourceModel->save($attribute);

        return $this;
    }
}
