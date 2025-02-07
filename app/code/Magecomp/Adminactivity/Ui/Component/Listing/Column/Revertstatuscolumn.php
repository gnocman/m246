<?php

namespace Magecomp\Adminactivity\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class StatusColumn
 * @package Magecomp\Adminactivity\Ui\Component\Listing\Column
 */
class Revertstatuscolumn extends Column
{
    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['is_revertable']==\Magecomp\Adminactivity\Helper\Data::ACTIVITY_REVERTABLE) {
                    $item[$this->getData('name')] =
                        '<span class="grid-severity-minor" title=""><span>Revert</span></span>';
                } elseif ($item['is_revertable']==
                    \Magecomp\Adminactivity\Helper\Data::ACTIVITY_REVERT_SUCCESS) {
                    $item[$this->getData('name')] =
                        '<span class="grid-severity-notice" title=""><span>Success</span></span>'.
                        '<br/><strong>Reverted By:</strong> '.$item['revert_by'];
                } elseif ($item['is_revertable']==\Magecomp\Adminactivity\Helper\Data::ACTIVITY_FAIL) {
                    $item[$this->getData('name')] =
                        '<span class="grid-severity-critical" title=""><span>Faild</span></span>';
                } else {
                    $item[$this->getData('name')] = '-';
                }
            }
        }

        return $dataSource;
    }
}
