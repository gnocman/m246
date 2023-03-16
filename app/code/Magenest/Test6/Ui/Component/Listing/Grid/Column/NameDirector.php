<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test6\Ui\Component\Listing\Grid\Column;

use Magenest\Test2\Model\DirectorFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Show data to column
 */
class NameDirector extends Column
{
    /**
     * @var DirectorFactory
     */
    private DirectorFactory $directorFactory;

    /**
     * @param DirectorFactory $directorFactory
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        DirectorFactory $directorFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->directorFactory = $directorFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {

            return $dataSource;
        }

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if ($item[$fieldName] !== '') {
                $adminName = $this->getDirectorName((int)$item[$fieldName]);
                $item[$fieldName] .= ' (' . $adminName . ')';
            }
        }

        return $dataSource;
    }

    /**
     * Get Director Name
     *
     * @param int $userId
     * @return string|null
     */
    private function getDirectorName(int $userId): ?string
    {
        $director = $this->directorFactory->create()->load($userId);

        return $director->getData('name') ?? null;
    }
}
