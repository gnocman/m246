<?php

declare(strict_types=1);

namespace NamCong\ReturnShield\Observer;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class FlushSectionCaches implements ObserverInterface
{
    /**
     * Cache types that can keep stale ReturnShield output after config changes.
     */
    private const RELATED_CACHE_TYPES = [
        'config',
        'layout',
        'block_html',
        'full_page'
    ];

    public function __construct(
        private readonly TypeListInterface $cacheTypeList
    ) {
    }

    public function execute(Observer $observer): void
    {
        foreach (self::RELATED_CACHE_TYPES as $cacheType) {
            $this->cacheTypeList->cleanType($cacheType);
        }
    }
}
