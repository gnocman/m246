<?php

namespace Magecomp\Adminactivity\Model\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;

/**
 * Class Data
 * @package Magecomp\Adminactivity\Model\Config
 */
class Data extends \Magento\Framework\Config\Data
{
    public function __construct(
        ReaderInterface $reader,
        CacheInterface $cache,
                        $cacheId
    ){
        parent::__construct($reader,$cache,$cacheId);
    }
}
