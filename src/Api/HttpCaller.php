<?php
declare(strict_types=1);

namespace CardMarket\Api;

use Mamoot\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class HttpCaller {

  /**
   * @var HttpClientCreator
   */
  protected $httpClientCreator;

  /**
   * @var HttpClientInterface
   */
  protected $httpClient;

  public function __construct(HttpClientCreator $httpClientCreator)
  {
    $this->httpClientCreator = $httpClientCreator;
    $this->httpClient = $httpClientCreator->createHttpClient();
  }

  protected function get(string $url): ResponseInterface
  {

    try {

      $response = $this->httpClient->request('GET', $url, [
        "headers" => self::getAuthorizationHeader($url, 'GET'),
      ]);

    } catch(TransportExceptionInterface $exception) {
      throw $exception;
    }

    return $response;

  }

  /**
   * Create the Authorisation header.
   *
   * @param string $uri
   * @param string $method
   *
   * @return array
   */
  protected function getAuthorizationHeader(string $uri, string $method): array
  {
    return [
      "Authorisation" => self::buildAuthorizationHeader($uri, $method),
    ];
  }

  /**
   * Get the value of Authorisation header based on the URL and method call.
   *
   * @param string $url
   * @param string $method
   *
   * @return string
   */
  protected function getAuthorisationHeaderValue(string $url, string $method): string
  {
    $headerBuilder = new AuthenticationHeaderBuilder($this->httpClientCreator, $url, $method);
    return $headerBuilder->getAuthorisationHeaderValue();
  }

}