<?php
namespace Smartworking\CustomOrderProcessing\Model\Consumer;

use Magento\Framework\MessageQueue\ConsumerConfiguration;
use Smartworking\CustomOrderProcessing\Model\Api\OrderProcessing as OrderProcessingApi;
use Psr\Log\LoggerInterface;

/**
 * Class Consumer used to process order procesing messages.
 */
class OrderProcessing extends ConsumerConfiguration
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var OrderProcessingApi
     */
    protected $orderProcessingApi;

     /**
      * @var LoggerInterface
      */
    protected $logger;

    /**
     * consumer constructor
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param OrderProcessingApi $orderProcessingApi
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        OrderProcessingApi $orderProcessingApi,
        LoggerInterface $logger
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->orderProcessingApi = $orderProcessingApi;
        $this->logger = $logger;
    }

    /**
     * Consumer process start
     *
     * @param string $request
     */
    public function process($request)
    {
        try {
            $orderRequest = $this->jsonHelper->jsonDecode($request, true);
            if (isset($orderRequest['orders'])) {
                $this->logger->info("STARTED PROCESSING ORDER IN QUEUE");
                $response = $this->orderProcessingApi->processOrder($orderRequest['orders']);
                $this->logger->info("ORDER PROCESSED ".json_encode($response));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
