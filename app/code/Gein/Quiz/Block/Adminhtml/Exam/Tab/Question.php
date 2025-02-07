<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product in category grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Gein\Quiz\Block\Adminhtml\Exam\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Gein\Quiz\Ui\Component\Listing\Column\QuestionType;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

class Question extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Gein\Quiz\Model\QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var QuestionType
     */
    private $questionType;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Gein\Quiz\Model\QuestionFactory $questionFactory
     * @param array $data
     * @param QuestionType|null $questionType
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data            $backendHelper,
        \Gein\Quiz\Model\QuestionFactory        $questionFactory,
        array                                   $data = [],
        QuestionType                            $questionType = null
    )
    {
        $this->_questionFactory = $questionFactory;
        $this->questionType = $questionType ?: ObjectManager::getInstance()->get(QuestionType::class);
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('exam_questions');
        $this->setDefaultSort('question_id');
        $this->setUseAjax(true);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_exam') {
            $questionIds = $this->_getSelectedQuestions();
            if (empty($questionIds)) {
                $questionIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('question_id', ['in' => $questionIds]);
            } elseif (!empty($questionIds)) {
                $this->getCollection()->addFieldToFilter('question_id', ['nin' => $questionIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        if (isset($this->getRequest()->getParams()['exam_id'])) {
            $this->setDefaultFilter(['in_exam' => 1]);
        }

        $collection = $this->_questionFactory->create()->getCollection()->addFieldToSelect(
            'question_id'
        )->addFieldToSelect(
            'exam_id'
        )->addFieldToSelect(
            'content'
        )->addFieldToSelect(
            'type'
        )->addFieldToSelect(
            'create_time'
        )->addFieldToSelect(
            'update_time'
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_exam',
            [
                'type' => 'checkbox',
                'name' => 'in_exam',
                'values' => $this->_getSelectedQuestions(),
                'index' => 'exam_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'question_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'question_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => $this->questionType->getAvailableTypes()
            ]
        );
        $this->addColumn('content', ['header' => __('Name'), 'index' => 'content']);
        $this->addColumn('content', ['header' => __('Content'), 'index' => 'content']);
        $this->addColumn('create_time', ['header' => __('Create time'), 'index' => 'create_time']);
        $this->addColumn('update_time', ['header' => __('Update time'), 'index' => 'update_time']);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('gein_quiz/exam/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedQuestions()
    {
        $questions = $this->getRequest()->getPost('selected_questions');
        $params = $this->getRequest()->getParams();
        if (isset($params['exam_id'])) {
            $questions = $this->_questionFactory->create()->getCollection()
                ->addFieldToFilter('exam_id', $params['exam_id'])
                ->getAllIds();
        }
        return $questions;
    }
}
