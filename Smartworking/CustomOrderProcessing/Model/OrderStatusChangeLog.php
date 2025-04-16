<?php
namespace Smartworking\CustomOrderProcessing\Model;

class OrderStatusChangeLog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected const CACHE_TAG = 'smartworking_order_status_change_log';

    /**
     * @var string
     */
    protected $_cacheTag = 'smartworking_order_status_change_log';

    /**
     * @var string
     */
    protected $_eventPrefix = 'smartworking_order_status_change_log';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Smartworking\CustomOrderProcessing\Model\ResourceModel\OrderStatusChangeLog::class);
    }

    /**
     * Get Identitities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Default Values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
