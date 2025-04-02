<?php
declare(strict_types = 1);
namespace Smartworking\CustomOrderProcessing\Test\Unit\Model\Api;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Smartworking\CustomOrderProcessing\Model\Api\OrderProcessing;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class OrderProcessingTest extends TestCase
{

    private OrderProcessing $object;

    private MockObject $searchCriteriaBuilder;
    private MockObject $orderRepository;
    private MockObject $publisher;
    private MockObject $jsonHelper;

    protected function setUp() : void
    {
        $this->searchCriteriaBuilder = $this->getMockBuilder(
            SearchCriteriaBuilder::class
        )
        ->disableOriginalConstructor()
        ->getMock();

        $this->orderRepository = $this->getMockForAbstractClass(
            OrderRepositoryInterface::class,
            [],
            '',
            false,
            false,
            true,
            ['getList','getItems']
        );

        $this->publisher = $this->getMockForAbstractClass(
            PublisherInterface::class,
            [],
            '',
            false,
            false,
            true,
            []
        );
        $this->jsonHelper = $this->getMockBuilder(
            JsonHelper::class
        )
        ->disableOriginalConstructor()
        ->getMock();

        $this->object = new OrderProcessing(
            $this->searchCriteriaBuilder,
            $this->orderRepository,
            $this->publisher,
            $this->jsonHelper
        );
    }
    
    public function testOrderStatusTransitionNotPossible(): void
    {
        $expected = false;
        $currentStatus = 'new';
        $newStatus = 'shipped';
        $this->assertEquals($expected, $this->object->validateOrderStatusTransition($currentStatus,$newStatus));
    }
    public function testOrderStatusTransitionPossible(): void
    {
        $expected = true;
        $currentStatus = 'new';
        $newStatus = 'processing';
        $this->assertEquals($expected, $this->object->validateOrderStatusTransition($currentStatus,$newStatus));
    }

    public function testValidRequestToQueue():void
    {
        $expected = true;
        $request = '{
            "request": {
                "orders": [
                    {
                        "order_id": "000000009",
                        "status": "shipped"
                    },
                    {
                        "order_id": "00000001qwqw3",
                        "status": "shipped"
                    }
                ],
                "synchronous": false
            }
        }';
        $this->assertEquals($expected, $this->object->addRequestToQueue($request));
    }
    public function testInValidRequestToQueue():void
    {
        $expected = false;
        $request = '';
        $this->assertEquals($expected, $this->object->addRequestToQueue($request));
    }

    public function testRequestProcessedInQueue():void
    {
        $expected = 'Request to process orders submitted successfully';
        $request = [
            'synchronous' => false,
            'orders' => [
                'order_id' => '000000009',
                'status' => 'processing'
            ]
        ];
        $this->assertEquals($expected, $this->object->changeStatus($request));
    }
    
}