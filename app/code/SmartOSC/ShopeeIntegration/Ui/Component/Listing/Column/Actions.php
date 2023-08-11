<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\ShopeeIntegration\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => '/edit',
                        'label' => __('Edit')
                    ],
                    'duplicate' => [
                        'href' => '/duplicate',
                        'label' => __('Duplicate')
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
