<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gein\Quiz\Ui\DataProvider\Question\Form\Modifier;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;


abstract class AbstractModifier implements ModifierInterface
{
    const FORM_NAME = 'gein_quiz_question_form';
    const DATA_SOURCE_DEFAULT = 'answers';
    const DATA_SCOPE_QUESTION = 'answers';

    /**
     * Name of default general panel
     */
    const DEFAULT_GENERAL_PANEL = 'question-details';

    /**
     * Default general panel order
     */
    const GENERAL_PANEL_ORDER = 10;

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * Retrieve next group sort order
     *
     * @param array $meta
     * @param array|string $groupCodes
     * @param int $defaultSortOrder
     * @param int $iteration
     * @return int
     * @since 101.0.0
     */
    protected function getNextGroupSortOrder(array $meta, $groupCodes, $defaultSortOrder, $iteration = 1)
    {
        $groupCodes = (array)$groupCodes;

        foreach ($groupCodes as $groupCode) {
            if (isset($meta[$groupCode]['arguments']['data']['config']['sortOrder'])) {
                return $meta[$groupCode]['arguments']['data']['config']['sortOrder'] + $iteration;
            }
        }

        return $defaultSortOrder;
    }

    /**
     * Retrieve next attribute sort order
     *
     * @param array $meta
     * @param array|string $attributeCodes
     * @param int $defaultSortOrder
     * @param int $iteration
     * @return int
     * @since 101.0.0
     */
    protected function getNextAttributeSortOrder(array $meta, $attributeCodes, $defaultSortOrder, $iteration = 1)
    {
        $attributeCodes = (array)$attributeCodes;

        foreach ($meta as $groupMeta) {
            $defaultSortOrder = $this->_getNextAttributeSortOrder(
                $groupMeta,
                $attributeCodes,
                $defaultSortOrder,
                $iteration
            );
        }

        return $defaultSortOrder;
    }

    /**
     * Retrieve next attribute sort order
     *
     * @param array $meta
     * @param array $attributeCodes
     * @param int $defaultSortOrder
     * @param int $iteration
     * @return mixed
     */
    private function _getNextAttributeSortOrder(array $meta, $attributeCodes, $defaultSortOrder, $iteration = 1)
    {
        if (isset($meta['children'])) {
            foreach ($meta['children'] as $attributeCode => $attributeMeta) {
                if ($this->startsWith($attributeCode, self::CONTAINER_PREFIX)) {
                    $defaultSortOrder = $this->_getNextAttributeSortOrder(
                        $attributeMeta,
                        $attributeCodes,
                        $defaultSortOrder,
                        $iteration
                    );
                } elseif (in_array($attributeCode, $attributeCodes)
                    && isset($attributeMeta['arguments']['data']['config']['sortOrder'])
                ) {
                    $defaultSortOrder = $attributeMeta['arguments']['data']['config']['sortOrder'] + $iteration;
                }
            }
        }

        return $defaultSortOrder;
    }

    /**
     * Search backwards starting from haystack length characters from the end
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @since 101.0.0
     */
    protected function startsWith($haystack, $needle)
    {
        return $needle === '' || strrpos($haystack, (string) $needle, -strlen($haystack)) !== false;
    }

    /**
     * Return name of first panel (general panel)
     *
     * @param array $meta
     * @return string
     * @since 101.0.0
     */
    protected function getGeneralPanelName(array $meta)
    {
        if (!$meta) {
            return null;
        }

        if (isset($meta[self::DEFAULT_GENERAL_PANEL])) {
            return self::DEFAULT_GENERAL_PANEL;
        }

        return $this->getFirstPanelCode($meta);
    }

    /**
     * Retrieve first panel name
     *
     * @param array $meta
     * @return string|null
     * @since 101.0.0
     */
    protected function getFirstPanelCode(array $meta)
    {
        $min = null;
        $name = null;

        foreach ($meta as $fieldSetName => $fieldSetMeta) {
            if (isset($fieldSetMeta['arguments']['data']['config']['sortOrder'])
                && (null === $min || $fieldSetMeta['arguments']['data']['config']['sortOrder'] <= $min)
            ) {
                $min = $fieldSetMeta['arguments']['data']['config']['sortOrder'];
                $name = $fieldSetName;
            }
        }

        return $name;
    }
}
