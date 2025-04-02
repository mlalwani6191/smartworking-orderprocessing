<?php
namespace Smartworking\CustomOrderProcessing\Model\Api;

use Smartworking\CustomOrderProcessing\Api\OrderProcessingInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class OrderProcessing implements OrderProcessingInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

     /**
      * @var string
      */
    protected $response;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * API Model constructor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param PublisherInterface $publisher
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        PublisherInterface $publisher,
        JsonHelper $jsonHelper
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->jsonHelper = $jsonHelper;
        $this->publisher = $publisher;
    }

    /**
     * Function to update status
     *
     * @param mixed $request
     * @return string
     */
    public function changeStatus(mixed $request)
    {
        $requestTypeSynchronous = true;
        
        if (isset($request['synchronous'])) {
            $requestTypeSynchronous = $request['synchronous'];
        }

        if ($requestTypeSynchronous == false) {
            $this->addRequestToQueue($request);
            $this->response = "Request to process orders submitted successfully";
        } else {
            $orders = $request['orders'];
            $this->response = $this->processOrder($orders);
        }
        return $this->response;
    }

     /**
      * Function to get order
      *
      * @param string $orderIncrementId
      */
    private function getOrder($orderIncrementId)
    {
        $orderObj = null;
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('increment_id', $orderIncrementId)->create();
            $orderData = $this->orderRepository->getList($searchCriteria)->getItems();
            if (count($orderData) == 0) {
                return false;
            }
            foreach ($orderData as $order) {
                $orderObj =  $order;
                break;
            }
            return $orderObj;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Validate Order transition
     *
     * @param string $currentStatus
     * @param string $newStatus
     */
    public function validateOrderStatusTransition($currentStatus, $newStatus)
    {
        $orderStatusMapping = [
            'new' => 'processing,on_hold,canceled,payment_pending,payment_recieved',
            'processing' =>'shipped,canceled,complete,closed',
            'shipped' =>'complete,closed',
            'payment_pending' => 'processing,payment_recieved'
        ];
        if (isset($orderStatusMapping[$currentStatus])) {
            $mapping = $orderStatusMapping[$currentStatus];
            if (in_array($newStatus, explode(',', $mapping))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Process order
     *
     * @param array $orders
     */
    public function processOrder(array $orders)
    {
        $response = [];
        foreach ($orders as $order) {
            try {
                $orderIncrementId = $order['order_id'];
                $status = $order['status'];
                $orderObject = $this->getOrder($orderIncrementId);
                if (false == $orderObject) {
                    $response[] = 'Order with ID ' . $orderIncrementId . ' doesnot exists';
                    continue;
                }
                $currentOrderStatus = $orderObject->getStatus();
                if ($currentOrderStatus == $status) {
                    // No need to update the status
                    $response[] = 'Order with ID ' . $orderIncrementId . ' has same status - Update not applicable';
                    continue;
                }
                $isOrderStatusTransition = $this->validateOrderStatusTransition($currentOrderStatus, $status);
                if (false == $isOrderStatusTransition) {
                    $response[] = 'Order with ID ' . $orderIncrementId . ' cannot be updated to given status';
                    continue;
                }
                $orderObject->setStatus($status);
                $orderObject->save();
                $response[] = 'Order with ID ' . $orderIncrementId . ' updated successfully';
            } catch (Exception $ex) {
                return $ex->getMessage();
            }
            
        }
        return $response;
    }

    /**
     * Add order request to queue
     *
     * @param array $request
     */
    public function addRequestToQueue($request)
    {
        if(empty($request)){
            return false;
        }
        $this->publisher->publish(
            'smartworking.customorderprocessing',
            $this->jsonHelper->jsonEncode($request)
        );
        return true;
    }
}
