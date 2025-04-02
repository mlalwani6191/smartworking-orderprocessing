<?php
namespace Smartworking\CustomOrderProcessing\Model\ResourceModel\OrderStatusChangeLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'smartworking_order_status_change_log_collection';

     /**
      * @var string
      */
    protected $_eventObject = 'smartworking_order_status_change_log_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Smartworking\CustomOrderProcessing\Model\OrderStatusChangeLog::class,
            Smartworking\CustomOrderProcessing\Model\ResourceModel\OrderStatusChangeLog::class
        );
    }
}
