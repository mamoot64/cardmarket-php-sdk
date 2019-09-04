<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Tests\Resources;

use DG\BypassFinals;
use Mamoot\CardMarket\Exception\HttpClientException;
use Mamoot\CardMarket\Exception\HttpServerException;
use Mamoot\CardMarket\Exception\UnknownErrorException;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use Mamoot\CardMarket\Resources\HttpCaller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class HttpCallerTest extends TestCase
{
    private $httpClientCreatorMock;

    protected function setUp(): void
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

    public function testIfInstanceIdHttpCaller()
    {
        $this->assertInstanceOf(HttpCaller::class, $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "coucou"}', [])
        ));
    }

    public function testBadExceptionWithoutJsonHeader()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('coucou', [
            "http_code" => 400,
            "response_headers" => [
              'Content-Type' => "application/fake",
            ]
          ])
        );

        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage("The parameters passed to the API were invalid. Check your inputs!\n\ncoucou");
        $abtractHttpCaller->get('/fake');
    }

    public function testExpectBadRequestException()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "coucou"}', [
            "http_code" => 400,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(HttpClientException::class);
        $abtractHttpCaller->get('/fake');
    }

    public function testExpectUnauthorizedException()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "You don\'t have the permission."}', [
            "http_code" => 401,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('Authentication or authorization fails during your request, e.g. your Authorization (signature) is not correct.');
        $abtractHttpCaller->get('/unahthorized');
    }

    public function testExpectForbiddenException()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "You don\'t have the permission."}', [
            "http_code" => 403,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('You try to access a forbidden resource. Check your Authorization Header.');
        $abtractHttpCaller->get('/forbidden');
    }

    public function testExpectNotFoundException()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "Resource not found."}', [
            "http_code" => 404,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('The endpoint you have tried to access does not exist.');
        $abtractHttpCaller->get('/not-found');
    }

    public function testExpectTooManyRequestsException()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "Too many requests."}', [
            "http_code" => 429,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('You have rich your maximum calls per day.');
        $abtractHttpCaller->get('/too-many-requests');
    }

    public function testExpectError500Exception()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "Too many requests."}', [
            "http_code" => 500,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(HttpServerException::class);
        $this->expectExceptionMessage('An unexpected error occurred on Cardmarket servers. Status code 500.');
        $abtractHttpCaller->get('/error-500');
    }

    public function testExpectUnknownException()
    {
        $abtractHttpCaller = $this->setAbstracthttpCallerWithCustomMockResponse(
          new MockResponse('{"message": "Too many requests."}', [
            "http_code" => 1,
            "response_headers" => [
              'Content-Type' => "application/json",
            ]
          ])
        );

        $this->expectException(UnknownErrorException::class);
        $abtractHttpCaller->get('/unknown-exception');
    }

    private function setAbstracthttpCallerWithCustomMockResponse(MockResponse $mockResponse)
    {
        $this->httpClientCreatorMock->method('createHttpClient')->willReturn(new MockHttpClient($mockResponse));

        return new class($this->httpClientCreatorMock) extends HttpCaller {
            public function __construct($httpClientCreator)
            {
                parent::__construct($httpClientCreator);
            }

            public function get($uri): array
            {
                return parent::get($uri);
            }
        };
    }

}
