<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Gein\Quiz\Block\Adminhtml\Exam;

use Gein\Quiz\Api\QuestionRepositoryInterface;

class AssignQuestions extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Gein_Quiz::exam/edit/assign_questions.phtml';

    /**
     * @var \Gein\Quiz\Block\Adminhtml\Exam\Tab\Question
     */
    protected $blockGrid;

    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * AssignQuestions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param QuestionRepositoryInterface $questionRepositoryInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        QuestionRepositoryInterface $questionRepositoryInterface,
        array $data = []
    ) {
        $this->questionRepository = $questionRepositoryInterface;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Gein\Quiz\Block\Adminhtml\Exam\Tab\Question::class,
                'exam.question.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getQuestionsString()
    {
        $examId = $this->getRequest()->getParam('exam_id') ?? null;
        if ($examId) {
            $questions = $this->questionRepository->getExamQuestions($examId);
            return implode(',', $questions);
        }
        return '';
    }
}
