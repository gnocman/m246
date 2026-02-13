<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Gein\Quiz\Ui\DataProvider\Question\Form\Modifier;

use Gein\Quiz\Api\AnswerRepositoryInterface;
use Gein\Quiz\Ui\Component\Listing\Column\QuestionType;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;

class AnswerOptions extends AbstractModifier
{
    /**#@+
     * Group values
     */
    public const GROUP_CUSTOM_OPTIONS_NAME = 'answer_options';
    public const GROUP_CUSTOM_OPTIONS_SCOPE = 'answers';
    public const GROUP_CUSTOM_OPTIONS_DEFAULT_SORT_ORDER = 20;

    /**#@+
     * Container values
     */
    public const CONTAINER_OPTION = 'container_option';
    public const CONTAINER_COMMON_NAME = 'container_common';
    public const CONTAINER_TYPE_CONTENT_NAME = 'container_type_content';
    /**#@-*/

    /**#@+
     * Grid values
     */
    public const GRID_OPTIONS_NAME = 'options';
    /**#@-*/

    /**#@+
     * Field values
     */
    public const FIELD_QUESTION_TYPE_NAME = 'type';
    public const FIELD_ANSWER_ID_NAME = 'answer_id';
    public const FIELD_CONTENT_NAME = 'content';
    public const FIELD_IS_CORRECT_NAME = 'is_correct';
    public const FIELD_SORT_ORDER_NAME = 'sort_order';
    public const FIELD_IS_DELETE = 'is_delete';
    public const FIELD_IS_USE_DEFAULT = 'is_use_default';

    /**
     * @var QuestionType
     */
    private $questionType;

    /**
     * @var AnswerRepositoryInterface
     */
    protected $resultRepository;

    /**
     * @var array
     * @since 101.0.0
     */
    protected $meta = [];

    /**
     * @param QuestionType $questionType
     * @param AnswerRepositoryInterface $resultRepository
     */
    public function __construct(
        QuestionType $questionType,
        AnswerRepositoryInterface $resultRepository
    ) {
        $this->questionType = $questionType;
        $this->resultRepository = $resultRepository;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        $options = [];
        $questionId = array_values($data)[0]['question_id'] ?? null;
        if ($questionId) {
            $answerOptions = $this->resultRepository->getListAnswersByQuestionId($questionId);

            /** @var \Gein\Quiz\Model\Answer $option */
            foreach ($answerOptions as $index => $option) {
                $options[$index] = $option->getData();
            }

            return array_replace_recursive(
                $data,
                [
                    $questionId => [
                        static::DATA_SOURCE_DEFAULT => [
                            static::GRID_OPTIONS_NAME => $options,
                            static::FIELD_QUESTION_TYPE_NAME => array_values($data)[0][static::FIELD_QUESTION_TYPE_NAME],
                            static::FIELD_CONTENT_NAME => $options[0][static::FIELD_CONTENT_NAME] ?? null,
                            static::FIELD_ANSWER_ID_NAME => $options[0][static::FIELD_ANSWER_ID_NAME] ?? null
                        ]
                    ]
                ]
            );
        }
        return $data;
    }

    /**
     * @inheritdoc
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->createCustomOptionsPanel();

        return $this->meta;
    }

    /**
     * Create "Customizable Options" panel
     *
     * @return $this
     * @since 101.0.0
     */
    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_CUSTOM_OPTIONS_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Answer Options'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_CUSTOM_OPTIONS_SCOPE,
                                'collapsible' => true,
                                'additionalClasses' => 'answer-options',
                                'positionProvider' => static::CONTAINER_OPTION . '.' . static::FIELD_SORT_ORDER_NAME,
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_OPTION => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Fieldset::NAME,
                                        'collapsible' => true,
                                        'label' => null,
                                        'sortOrder' => 10,
                                        'opened' => true,
                                    ],
                                ],
                            ],
                            'children' => [
                                static::CONTAINER_COMMON_NAME => $this->getCommonContainerConfig(10),
                                static::CONTAINER_TYPE_CONTENT_NAME => $this->getContentTypeContainerConfig(20),
                                static::GRID_OPTIONS_NAME => $this->getSelectTypeGridConfig(30)
                            ]
                        ],
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * Get config for container with common fields for any type
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getCommonContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_QUESTION_TYPE_NAME => $this->getTypeFieldConfig(30)
            ]
        ];
    }

    /**
     * Get config for container with input type
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getContentTypeContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns',
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_ANSWER_ID_NAME => $this->getAnswerIdFieldConfig(5),
                static::FIELD_CONTENT_NAME =>
                    [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Answer content'),
                                    'componentType' => Field::NAME,
                                    'formElement' => Textarea::NAME,
                                    'dataScope' => static::FIELD_CONTENT_NAME,
                                    'dataType' => Text::NAME,
                                    'sortOrder' => $sortOrder,
                                    'validation' => [
                                        'required-entry' => true
                                    ],
                                ],
                            ],
                        ],
                    ],
            ]
        ];
    }

    /**
     * Get config for grid for "select" types
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Answer'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Gein_Quiz/js/question/dynamic-rows-per-page',
                        'template' => 'Magento_Catalog/components/dynamic-rows-per-page',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                        'validation' => [
                            'required-entry' => true
                        ],
                        'sizesConfig' => [
                            'enabled' => false
                        ]
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_ANSWER_ID_NAME => $this->getAnswerIdFieldConfig(5),
                        static::FIELD_CONTENT_NAME => $this->getContentFieldConfig(10),
                        static::FIELD_IS_CORRECT_NAME => $this->getIsCorrectFieldConfig(20),
                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(30),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(40)
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for hidden field used for sorting
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getAnswerIdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Hidden::NAME,
                        'dataScope' => static::FIELD_ANSWER_ID_NAME,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "Title" fields
     *
     * @param int $sortOrder
     * @param array $options
     * @return array
     * @since 101.0.0
     */
    protected function getContentFieldConfig($sortOrder, array $options = [])
    {
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Answer content'),
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => static::FIELD_CONTENT_NAME,
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'validation' => [
                                'required-entry' => true
                            ],
                        ],
                    ],
                ],
            ],
            $options
        );
    }

    /**
     * Get config for "Option Type" field
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getTypeFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Question Type'),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'component' => 'Gein_Quiz/js/question/question-types',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'dataScope' => static::FIELD_QUESTION_TYPE_NAME,
                        'dataType' => Select::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->questionType->toOptionArray(),
                        'disableLabel' => true,
                        'multiple' => false,
                        'additionalClasses' => 'question-type',
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                        'validation' => [
                            'required-entry' => true
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "Required" field
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getIsCorrectFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Correct'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => static::FIELD_IS_CORRECT_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'value' => '0',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for sorting
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getPositionFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Hidden::NAME,
                        'dataScope' => static::FIELD_SORT_ORDER_NAME,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for removing rows
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getIsDeleteFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => ActionDelete::NAME,
                        'fit' => true,
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
    }
}
