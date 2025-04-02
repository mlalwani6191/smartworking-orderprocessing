<?php
declare(strict_types = 1);

namespace Smartworking\CustomOrderProcessing\Test\Integration\Model\Api;

use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class OrderProcessingTest extends TestCase
{
    public function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->publisher = $objectManager->create(PublisherInterface::class);
        $this->criteriaBuilder = $objectManager->create(SearchCriteriaBuilder::class);
        $this->orderRepository = $objectManager->create(OrderRepositoryInterface::class);
        $this->json = $objectManager->create(JsonHelper::class);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_new.php
     */
    public function changeOrderStatusToProcessing()
    {
        $status = 'processing';
        $searchCriteria = $this->criteriaBuilder
                ->addFilter('increment_id','100000001')->create();
        $orderData = $this->orderRepository->getList($searchCriteria)->getItems();
        if (count($orderData) == 0) {
            return false;
        }
        foreach ($orderData as $order) {
            $orderObj =  $order;
            break;
        }
        $orderObject->setStatus($status);
        $orderObject->save();

        $this->assertEquals($status, $orderObject->getStatus());
    }
}