/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function ($, Component, customerData, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SmartOSC_GroupOrder/minicart'
        },

        initialize: function () {
            this._super();
            this.customer = customerData.get('cart');
        },

        moveShareCart: function () {
            $(document).ready(function () {
                $('.secondary.sharecart').appendTo($('#minicart-content-wrapper .action.viewcart').parent());
            });
        },

        getQuoteId: function () {
            return this.customer().quote_url;
        },

        copyQuote: function (object, e) {
            const quoteUrl = document.createElement('textarea');
            quoteUrl.value = this.customer().quote_url;
            document.body.appendChild(quoteUrl);
            quoteUrl.select();
            document.execCommand('copy');
            document.body.removeChild(quoteUrl);

            e.currentTarget.classList.add('mp-tooltipped');
            e.currentTarget.setAttribute('aria-label', $t('Copied!'));
        },

        leaveQuote: function (object, e) {
            e.currentTarget.classList.remove('mp-tooltipped');
            e.currentTarget.removeAttribute('aria-label');
        },

        isDisplay: function () {
            return this.customer().summary_count;
        }
    });
});
