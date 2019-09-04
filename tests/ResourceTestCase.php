<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests;

use DG\BypassFinals;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

abstract class ResourceTestCase extends TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpClientCreatorMock;

    public function setUp(): void
    {
        parent::setUp();

        BypassFinals::enable();

        $this->httpClientCreatorMock = $this->createMock(HttpClientCreator::class);

        $this->httpClientCreatorMock
          ->method('retrieveAppCredentials')
          ->willReturn([
            'application_secret' => 'app_secret',
            'application_token' => 'app_token',
            'access_token' => 'token',
            'access_secret' => 'secret',
          ]);
    }

    protected function setupHttpClientCreatorMock(array $responses = [])
    {
        $this->httpClientCreatorMock
          ->method('createHttpClient')
          ->willReturn($this->createHttpClientMock($responses));
    }

    private function createHttpClientMock(array $responses = [])
    {
        $mockResponses = !empty($responses) ? $responses : $this->getMockResponses();
        return new MockHttpClient($mockResponses);
    }

    abstract protected function getMockResponses(): array;

}
