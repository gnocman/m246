/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

let config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magezon_Deliverydate/js/order/place-order-mixin': true
            },
        }
    }
};
