<?php

namespace Smartworking\CustomOrderProcessing\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Smartworking_CustomOrderProcessing::order_status_logs');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Status Change Logs'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Smartworking_CustomOrderProcessing::order_status_logs');
    }
}