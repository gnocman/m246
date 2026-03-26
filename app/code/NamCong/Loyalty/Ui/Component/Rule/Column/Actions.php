<?php

declare(strict_types=1);

namespace NamCong\Loyalty\Ui\Component\Rule\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (isset($item['rule_id'])) {
                $item[$this->getData('name')] = [
                    'edit' => [
                        'href'  => $this->urlBuilder->getUrl('loyalty_admin/rule/edit', ['rule_id' => $item['rule_id']]),
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href'    => $this->urlBuilder->getUrl('loyalty_admin/rule/delete', ['rule_id' => $item['rule_id']]),
                        'label'   => __('Delete'),
                        'confirm' => [
                            'title'   => __('Delete Loyalty Rule'),
                            'message' => __('Are you sure you want to delete this rule?'),
                        ],
                        'post'    => true,
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
