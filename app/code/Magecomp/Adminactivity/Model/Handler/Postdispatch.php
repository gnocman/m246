<?php

namespace Magecomp\Adminactivity\Model\Handler;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Backend\Model\Session;
/**
 * Class PostDispatch
 * @package Magecomp\Adminactivity\Model\Handler
 */
class Postdispatch
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    public $response;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var \Magento\Backend\Model\Session
     */
    public $session;

    /**
     * PostDispatch constructor.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ProductRepositoryInterface $productRepository
     * @param Session $session
     */
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
        ProductRepositoryInterface $productRepository,
        Session $session
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->productRepository = $productRepository;
        $this->session = $session;
    }

    /**
     * @param $model
     * @return array
     */
    public function getProductAttributes($model)
    {
        $logData = [];
        $status = $this->request->getParam('status', '');
        if($status != '') {
            $logData['status'] = [
                'old_value' => $model->getStatus(),
                'new_value' => $status
            ];
        }

        $attributes = $this->request->getParam('attributes', []);
        if(!empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $logData[$attribute] = [
                    'old_value' => $model->getData($attribute),
                    'new_value' => $value
                ];
            }
        }

        $inventories = $this->request->getParam('inventory', []);
        if(!empty($inventories)) {
            foreach ($inventories as $field => $value) {
                $logData[$field] = [
                    'old_value' => $model->getData($field),
                    'new_value' => $value
                ];
            }
        }

        $websiteIds = $this->request->getParam('remove_website', []);
        if ($websiteIds) {
            $logData['remove_website_ids'] = [
                'old_value' => '[]',
                'new_value' => implode(', ', $websiteIds)
            ];
        }

        $websiteIds = $this->request->getParam('add_website', []);
        if ($websiteIds) {
            $logData['add_website_ids'] = [
                'old_value' => '[]',
                'new_value' => implode(', ', $websiteIds)
            ];
        }

        return $logData;
    }

    /**
     * Set product update activity log
     * @param $config
     * @param $processor
     */
    public function productUpdate($config, $processor)
    {
        $activity = $processor->_initLog();
        $activity->setIsRevertable(1);

        $selected = $this->request->getParam('selected');
        if(empty($selected)) {
            $selected = $this->session->getProductIds();
        }
        if(!empty($selected)) {
            foreach ($selected as $id) {

                $model = $this->productRepository->getById($id);

                $log = clone $activity;
                $log->setItemName($model->getData($processor->config->getActivityModuleItemField($config['module'])));
                $log->setItemUrl($processor->getEditUrl($model));

                $logData = $processor->handler->__initLog($this->getProductAttributes($model));
                $logDetail = $processor->_initActivityDetail($model);

                $processor->activityLogs[] = [
                    \Magecomp\Adminactivity\Model\Activity::class => $log,
                    \Magecomp\Adminactivity\Model\ActivityLog::class => $logData,
                    \Magecomp\Adminactivity\Model\ActivityLogDetail::class => $logDetail
                ];
            }
        }
    }
}
