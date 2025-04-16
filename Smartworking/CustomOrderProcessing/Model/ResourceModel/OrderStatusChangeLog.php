<?php
namespace Smartworking\CustomOrderProcessing\Model\ResourceModel;

class OrderStatusChangeLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
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
