<?php
/**
 * Copyright © Nam Cong, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\DatabaseEAV\Controller\Adminhtml\NewBlog;

use Magenest\DatabaseEAV\Model\BlogFactory;
use Magenest\DatabaseEAV\Model\ResourceModel\Blog;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

/**
 *
 */
class Save extends Action
{
    /**
     * @var UrlRewriteFactory
     */
    protected UrlRewriteFactory $_urlRewriteFactory;
    /**
     * @var Blog
     */
    protected Blog $_resourceBlog;
    /**
     * @var UrlRewrite
     */
    protected UrlRewrite $_urlRewrite;
    /**
     * @var BlogFactory
     */
    protected BlogFactory $blogFactory;

    /**
     * @param UrlRewrite $urlRewrite
     * @param Context $context
     * @param BlogFactory $BlogFactory
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param Blog $resourceBlog
     */
    public function __construct(
        UrlRewrite $urlRewrite,
        Context $context,
        BlogFactory $BlogFactory,
        UrlRewriteFactory $urlRewriteFactory,
        Blog $resourceBlog
    ) {
        parent::__construct($context);
        $this->_urlRewrite = $urlRewrite;
        $this->blogFactory = $BlogFactory;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_resourceBlog = $resourceBlog;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            $existingModel = $this->blogFactory->create()->load($data['url_rewrite'], 'url_rewrite');
            if ($existingModel->getId()) {
                throw new \Exception((string)__('Blog with URL rewrite %1 already exists', $data['url_rewrite']));
            }

            $model = $this->blogFactory->create();
            $model->addData([
                "title" => $data['title'],
                "description" => $data['description'],
                "content" => $data['content'],
                "url_rewrite" => $data['url_rewrite'],
                "status" => $data['status'],
                "created_at" => $data['created_at'],
                "update_at" => $data['update_at'],
                "author_id" => $data['author_id'],
            ]);
            $this->_resourceBlog->save($model);
            $this->messageManager->addSuccess(__('Insert data Successfully !'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('backend/blog/index');
    }
}
