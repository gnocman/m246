<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\CustomerData;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

class SetQuoteUrlToCopy
{
    /**
     * @var Session
     */
    protected Session $checkoutSession;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * Cart constructor.
     *
     * @param Session $checkoutSession
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Session $checkoutSession,
        UrlInterface $urlBuilder
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param $result
     *
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        $result['quote_url'] = $this->urlBuilder->getUrl(
            'sharecart',
            ['key' => $this->checkoutSession->getQuote()->getOrderCartToken()]
        );

        return $result;
    }
}
