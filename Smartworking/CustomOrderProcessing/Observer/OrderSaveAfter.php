<?php
namespace Smartworking\CustomOrderProcessing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Smartworking\CustomOrderProcessing\Model\OrderStatusChangeLogFactory;
use Smartworking\CustomOrderProcessing\Model\ResourceModel\OrderStatusChangeLog;

class OrderSaveAfter implements ObserverInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OrderStatusChangeLogFactory
     */
    protected $orderStatusChangeLogModel;
    
    /**
     * @var OrderStatusChangeLog
     */
    protected $orderStatusChangeLogRsModel;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param OrderStatusChangeLogFactory $orderStatusChangeLogFactory
     * @param OrderStatusChangeLog $orderStatusChangeLog
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     */
    public function __construct(
        LoggerInterface $logger,
        OrderStatusChangeLogFactory $orderStatusChangeLogFactory,
        OrderStatusChangeLog $orderStatusChangeLog,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->logger = $logger;
        $this->orderStatusChangeLogModel = $orderStatusChangeLogFactory;
        $this->orderStatusChangeLogRsModel = $orderStatusChangeLog;
        $this->orderSender = $orderSender;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $this->saveOrderStatusChangeLog($order);

            // Notify to customer if orde is shipped
            if ($order->getStatus() == "complete" && $order->hasShipments()) {
                
                // Send Shipment email to customer using shipment sender object or trigger thirdpary API integration if emails are sent via external email client
                $this->orderSender->send($order, true);
            }
        } catch (Exception $ex) {
            $this->logger->critical("Error in Smartworking\CustomOrderProcessing\Observer\OrderSaveAfter".$ex->getMessage());
        }
    }

    /**
     * Update Logs in Database
     *
     * @return void
     */
    private function saveOrderStatusChangeLog($order)
    {
        $orderStatusChangeLogModel = $this->orderStatusChangeLogModel->create();
        $orderStatusChangeLogModel->setOrderIncrementId($order->getIncrementId());
        $orderStatusChangeLogModel->setOldStatus($order->getOrigData('status'));
        $orderStatusChangeLogModel->setNewStatus($order->getStatus());
        $this->orderStatusChangeLogRsModel->save($orderStatusChangeLogModel);
    }
}
