<?php
namespace Smartworking\CustomOrderProcessing\Model\ResourceModel;

class OrderStatusChangeLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * Define resource model
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @return void
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }
    
    /**
     * Define _construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('order_status_change_log', 'entity_id');
    }
}
