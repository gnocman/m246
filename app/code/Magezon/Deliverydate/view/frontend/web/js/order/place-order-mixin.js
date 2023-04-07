/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_CheckoutAgreements/js/model/agreements-assigner',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'uiRegistry'
], function (
    $,
    wrapper,
    agreementsAssigner,
    quote,
    customer,
    urlBuilder,
    urlFormatter,
    errorProcessor,
) {
    'use strict';

    return function (placeOrderAction) {

        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            agreementsAssigner(paymentData);
            let isCustomer = customer.isLoggedIn();
            let quoteId = quote.getQuoteId();

            let url = urlFormatter.build('mgzcustom/quote/save');

            let deliveryDate = $('[name="delivery_date"]').val();

            if (deliveryDate) {

                let payload = {
                    'cartId': quoteId,
                    'delivery_date': deliveryDate,
                    'is_customer': isCustomer
                };

                if (!payload.delivery_date) {
                    return true;
                }

                let result = true;

                $.ajax({
                    url: url,
                    data: payload,
                    dataType: 'text',
                    type: 'POST',
                }).done(
                    function () {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );
            }

            return originalAction(paymentData, messageContainer);
        });
    };
});
