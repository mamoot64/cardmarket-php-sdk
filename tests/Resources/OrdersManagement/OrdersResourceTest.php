<?php

namespace Mamoot\CardMarket\Tests\Resources;

use Mamoot\CardMarket\Resources\OrdersManagement\OrdersResource;
use Mamoot\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class OrdersResourceTest extends ResourceTestCase
{

    private $ordersResrource;

    private $sentResponse;

    private $receivedResponse;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->ordersResource = new OrdersResource($this->httpClientCreatorMock);
        $this->sentResponse = $this->ordersResource->getSentOrders();
        $this->receivedResponse = $this->ordersResource->getReceivedOrders();
    }

    public function testRetrieveSentOrdersForTheCurrentSeller()
    {
        $this->assertArrayHasKey('order', $this->sentResponse);
        $this->assertCount(2, $this->sentResponse['order']);
        $this->assertEquals(OrdersResource::ORDER_STATE_SENT, $this->sentResponse['order'][0]['state']['state']);
        $this->assertEquals(OrdersResource::ORDER_STATE_SENT, $this->sentResponse['order'][1]['state']['state']);
    }

    public function testRetrieveReceivedOrdersForTheCurrentSeller()
    {
        $this->assertArrayHasKey('order', $this->receivedResponse );
        $this->assertCount(2, $this->receivedResponse ['order']);
        $this->assertEquals("evaluated", $this->receivedResponse ['order'][0]['state']['state']);
        $this->assertEquals("evaluated", $this->receivedResponse ['order'][1]['state']['state']);
    }


    protected function getMockResponses(): array
    {
        return [
          $this->createMockResponse("sent", 20),
          $this->createMockResponse("received", 20),
        ];
    }

    private function createMockResponse(string $state, int $nbUsed): MockResponse
    {
        return new MockResponse(file_get_contents(sprintf(__DIR__ . "/../MockResponse/%s_orders.json", $state)),
          [
            'response_headers' => [
              'X-Request-Limit-Max' => 5000,
              'X-Request-Limit-Count' => $nbUsed,
            ],
          ]);
    }

}