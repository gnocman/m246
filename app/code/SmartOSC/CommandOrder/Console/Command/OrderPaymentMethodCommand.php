<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\CommandOrder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class OrderPaymentMethodCommand extends Command
{
    public const COMMAND_NAME = 'module:order-payment-method';
    public const NAME = 'is_patch';
    public const SALES_METHOD = 'payment_method';
    public const NUMBER = 100;

    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $orderCollectionFactory;

    /**
     * OrderPaymentMethodCommand constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct();
    }

    /**
     * Get configure
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Insert payment method data or show the total number of orders with payment method = null')
            ->addOption(
                self::NAME,
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify whether it is a patch. "yes" for inserting data, "no" for counting orders.'
            );

        parent::configure();
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isPatch = $input->getOption(self::NAME);

        if ($isPatch === 'yes') {
            $this->executePatch($output);
            return 0;
        } elseif ($isPatch === 'no') {
            $this->executeCountOrders($output);
            return 0;
        } else {
            $output->writeln('Invalid option. Please specify "yes" or "no" for is_patch.');
            return 1;
        }
    }

    /**
     * Execute the patch operation.
     *
     * @param OutputInterface $output
     */
    protected function executePatch(OutputInterface $output)
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->getSelect()->joinLeft(
            ['payment' => $orderCollection->getTable('sales_order_payment')],
            'main_table.entity_id = payment.parent_id',
            [self::SALES_METHOD => 'payment.method']
        );

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('sales_order');

        $data = [];
        foreach ($orderCollection as $order) {
            $data[] = [
                'entity_id' => $order->getId(),
                self::SALES_METHOD => $order->getData(self::SALES_METHOD),
            ];
        }

        if (count($data) < self::NUMBER) {
            $connection->insertOnDuplicate($tableName, $data, [self::SALES_METHOD]);
        }

        $chunkData = array_chunk($data, self::NUMBER);
        foreach ($chunkData as $value) {
            $connection->insertOnDuplicate($tableName, $value, [self::SALES_METHOD]);
        }

        $output->writeln(sprintf("Data has been inserted: %s", count($data)));
    }

    /**
     * Execute the count orders operation.
     *
     * @param OutputInterface $output
     */
    protected function executeCountOrders(OutputInterface $output)
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter(self::SALES_METHOD, ['null' => true]);
        $totalOrders = $orderCollection->getSize();

        $output->writeln("Total orders with payment method = null: $totalOrders");
    }
}
