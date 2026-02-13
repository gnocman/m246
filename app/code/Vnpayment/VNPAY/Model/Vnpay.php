<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnpayment\VNPAY\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class Vnpay extends AbstractMethod
{
    public const PAYMENT_METHOD_VNPAY_CODE = 'vnpay';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_VNPAY_CODE;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}
