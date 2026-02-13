/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedQuestions = config.selectedQuestions,
            examQuestions = selectedQuestions.split(','),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('in_exam_questions').value = selectedQuestions;

        /**
         * Register Exam Question
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerExamQuestion(grid, element, checked)
        {
            if (checked) {
                if (jQuery.isNumeric(element.value)) {
                    examQuestions.push(element.value);
                }
            } else {
                examQuestions = examQuestions.filter(function (value) {
                    return value !== element.value;
                });
            }
            if (examQuestions.length > 0) {
                $('in_exam_questions').value = examQuestions.join(',');
            }
            grid.reloadParams = {
                'selected_questions[]': examQuestions
            };
        }

        /**
         * Click on question row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function examQuestionRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                eventElement = Event.element(event),
                isInputCheckbox = eventElement.tagName === 'INPUT' && eventElement.type === 'checkbox',
                checked = false,
                checkbox = null;

            if (eventElement.tagName === 'LABEL' &&
                trElement.querySelector('#' + eventElement.htmlFor) &&
                trElement.querySelector('#' + eventElement.htmlFor).type === 'checkbox'
            ) {
                event.stopPropagation();
                trElement.querySelector('#' + eventElement.htmlFor).trigger('click');

                return;
            }

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInputCheckbox ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        gridJsObject.rowClickCallback = examQuestionRowClick;
        gridJsObject.checkboxCheckCallback = registerExamQuestion;
    };
});
