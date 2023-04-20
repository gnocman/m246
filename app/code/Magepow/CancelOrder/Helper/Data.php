<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magepow\CancelOrder\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface|mixed
     */
    protected mixed $configModule;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->configModule = $this->getConfig(strtolower($this->_getModuleName()));
    }

    /**
     * Get getConfig
     *
     * @param string $cfg
     * @return ScopeConfigInterface|mixed
     */
    public function getConfig(string $cfg = ''): mixed
    {
        if ($cfg) {
            return $this->scopeConfig->getValue($cfg, ScopeInterface::SCOPE_STORE);
        }

        return $this->scopeConfig;
    }

    /**
     * Get getConfigModule
     *
     * @param string $cfg
     * @param array|null $value
     * @return ScopeConfigInterface|mixed|null
     */
    public function getConfigModule(string $cfg = '', array $value = null): mixed
    {
        $values = $this->configModule;
        if (!$cfg) {
            return $values;
        }
        $config = explode('/', $cfg);
        $end = count($config) - 1;
        foreach ($config as $key => $vl) {
            if (isset($values[$vl])) {
                if ($key == $end) {
                    $value = $values[$vl];
                } else {
                    $values = $values[$vl];
                }
            }

        }
        return $value;
    }

    /**
     * Get isEnabled
     *
     * @return ScopeConfigInterface|mixed|null
     */
    public function isEnabled(): mixed
    {
        return $this->getConfigModule('general/enabled');
    }

    /**
     * Get getEmailSender
     *
     * @return ScopeConfigInterface|mixed|null
     */
    public function getEmailSender(): mixed
    {
        return $this->getConfigModule('general/email_sender');
    }

    /**
     * Get getEmailSeller
     *
     * @return ScopeConfigInterface|mixed|null
     */
    public function getEmailSeller(): mixed
    {
        return $this->getConfigModule('general/email_seller');
    }
}
