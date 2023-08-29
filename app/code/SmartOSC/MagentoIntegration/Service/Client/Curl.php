<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\MagentoIntegration\Service\Client;

use Magento\Framework\HTTP\Client\Curl as CurlLibrary;

class Curl extends CurlLibrary
{
    /**
     * Make DELETE request
     *
     * The Magento Default curl library doesn't support delete method
     *
     * @param string $uri
     * @return void
     */
    public function delete($uri)
    {
        $this->makeRequest("DELETE", $uri);
    }

    /**
     * Make PUT request
     *
     * The Magento Default curl library doesn't support put method
     *
     * @param string $uri
     * @param array $param
     * @return void
     */
    public function put($uri, $param)
    {
        $this->makeRequest("PUT", $uri, $param);
    }
}
