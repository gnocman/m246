<?php
/**
 * Copyright © Nam Cong, Inc.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magenest\Test3\Observer;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class of Observer DisplayText
 */
class DisplayText implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * DisplayText constructor.
     *
     * @param RequestInterface $request
     * @param WriterInterface $configWriter
     */
    public function __construct(RequestInterface $request, WriterInterface $configWriter)
    {
        $this->request = $request;
        $this->configWriter = $configWriter;
    }

    /**
     * Observer change Ping to Pong
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        $groups = $this->request->getParam('groups');
        $value = $groups['general']['fields']['text_field']['value'];

        if ($value === 'Ping') {
            $this->configWriter->save('movie/general/text_field', 'Pong');
        }
    }
}
