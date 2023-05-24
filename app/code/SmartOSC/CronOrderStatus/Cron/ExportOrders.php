<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CronOrderStatus\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order;

class ExportOrders
{
    /**
     * @var Filesystem
     */
    protected Filesystem $fileSystem;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param Filesystem $filesystem
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Filesystem $filesystem,
        CollectionFactory $collectionFactory
    ) {
        $this->fileSystem = $filesystem;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save csv file
     *
     * @return void
     * @throws FileSystemException
     */
    public function execute()
    {
        $currentDate = date('y-m-d');

        $orderCollection = $this->getCompleteOrders();

        $csvContent = $this->generateCsvContent($orderCollection);

        $this->saveCsvFile($csvContent, $currentDate);
    }

    /**
     * Get CompleteOrders
     *
     * @return Collection
     */
    protected function getCompleteOrders()
    {
        $orderCollection = $this->collectionFactory->create();
        $orderCollection->addFieldToFilter('status', Order::STATE_COMPLETE);

        return $orderCollection;
    }

    /**
     * Generate Csv Content
     *
     * @param $orderCollection
     * @return array
     */
    protected function generateCsvContent($orderCollection)
    {
        $csvContent = [];
        $csvContent[] = ['Order ID', 'Customer Name', 'Total'];

        foreach ($orderCollection as $order) {
            $csvContent[] = [
                $order->getId(),
                $order->getCustomerName(),
                $order->getGrandTotal()
            ];
        }

        return $csvContent;
    }

    /**
     * Save Csv File
     *
     * @param $csvContent
     * @param $currentDate
     * @return void
     * @throws FileSystemException
     */
    protected function saveCsvFile($csvContent, $currentDate)
    {
        $directory = $this->fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $filePath = $directory->getAbsolutePath($currentDate . '-order.csv');

        $file = $directory->openFile($filePath, 'w+');
        $file->lock();

        foreach ($csvContent as $row) {
            $file->writeCsv($row);
        }

        $file->unlock();
        $file->close();
    }
}
