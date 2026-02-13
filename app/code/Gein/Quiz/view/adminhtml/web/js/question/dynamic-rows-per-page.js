/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Catalog/js/components/dynamic-rows-per-page',
    'underscore',
    'mageUtils',
    'uiLayout',
    'rjsResolver',
    'jquery'
], function (DynamicRows, _, utils, layout, resolver, $) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            maxRows: 4
        },

        /**
         * @inheritdoc
         */
        initElement: function (elem) {
            // var questionType = $('.question-type').value();
            $(".question-type").children("childElement")
            var questionType = null;
            if (this.getRecordCount() >= this.maxRows || (questionType === 'essay' && this.getRecordCount() === 1)) {
                $('button[data-action=add_new_row]').attr('disabled', 'disabled');
            } else {
                $('button[data-action=add_new_row]').removeAttr('disabled');
            }
            this._super();
        },

    });
});
