/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/action/get-totals'
], function ($, getTotalsAction) {
    'use strict';
    return function (checkboxId, itemId) {
        $(document).ready(function () {
            $("#" + checkboxId).change(function () {
                let isChecked = $(this).prop('checked');
                let value = isChecked ? 1 : 0;

                $.ajax({
                    url: '/product/select/updateItemSelectStatus',
                    type: 'POST',
                    data: {
                        item_id: itemId,
                        value: value
                    },
                    success: function (response) {
                        let deferred = $.Deferred();
                        getTotalsAction([], deferred);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error in AJAX request:', error);
                    }
                });
            });
        });
    };
});
