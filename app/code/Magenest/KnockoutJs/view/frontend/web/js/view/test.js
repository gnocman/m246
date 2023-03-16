/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
], function (ko, Component, urlBuilder, storage) {
    'use strict';
    let id = 1;

    return Component.extend({

        defaults: {
            template: 'Magenest_KnockoutJs/test',
        },

        productList: ko.observableArray([]),

        getProduct: function () {
            let self = this;
            let serviceUrl = urlBuilder.build('knockout/test/product?id=' + id);
            id++;
            return storage.post(
                serviceUrl,
                ''
            ).done(
                function (response) {
                    self.productList.push(JSON.parse(response));
                }
            ).fail(
                function (response) {
                    alert(response);
                }
            );
        },

    });
});
