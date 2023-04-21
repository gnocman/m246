/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data'
], function ($, authenticationPopup, customerData) {
    'use strict';

    return function (Component) {
        return Component.extend({

            /**
             * @override
             */
            "getCartParam": function (name) {
                if (name === 'possible_onepage_checkout') {
                    $('#top-cart-btn-checkout').click(function (event) {
                        let customer = customerData.get('customer');
                        if (!customer().firstname) {
                            event.preventDefault();
                            authenticationPopup.showModal();

                            return false;
                        }
                    });
                }
                return this._super(name);
            },
        });
    }
});
