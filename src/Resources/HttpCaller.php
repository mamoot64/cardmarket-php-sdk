<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Resources;

use Mamoot\CardMarket\Authentication\AuthenticationHeaderBuilder;
use Mamoot\CardMarket\Exception\CardmarketIsGoneException;
use Mamoot\CardMarket\Exception\UnknownErrorException;
use Mamoot\CardMarket\HttpClient\HttpClientCreator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class HttpCaller
{
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

    /**
     * @param string $uri
     *
     * @return array
     * @throws \Exception
     */
    protected function get(string $uri): array
    {
        $url = HttpClientCreator::API_URL . $uri;

        try {
            $response = $this->httpClient->request('GET', $url, [
              'headers' => self::getAuthorizationHeader($url, 'GET'),
            ]);
        } catch (CardmarketIsGoneException $exception) {
            throw $exception;
        }

        return self::processJsonResponse($response);
    }

    /**
     * Create the Authorisation header.
     *
     * @param string $url
     * @param string $method
     *
     * @return array
     */
    protected function getAuthorizationHeader(string $url, string $method): array
    {
        $headerBuilder = new AuthenticationHeaderBuilder($this->httpClientCreator, $url, $method);

        return [
          'Authorization' => $headerBuilder->getAuthorisationHeaderValue(),
        ];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return string
     * @throws \Exception
     */
    protected function processJsonResponse(ResponseInterface $response): array
    {
        if (200 !== $response->getStatusCode() && 201 !== $response->getStatusCode()) {
            $this->handleErrors($response);
        }

        return array_merge($response->toArray(), [
          'api' => [
            'request-limit-max' => $response->getHeaders()['x-request-limit-max'][0],
            'request-limit-count' => $response->getHeaders()['x-request-limit-count'][0],
          ],
        ]);
    }

    /**
     * Throw the correct exception for this error.
     *
     * @throws \Exception
     */
    protected function handleErrors(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        switch ($statusCode) {
          case 400:
            throw HttpClientException::badRequest($response);
          case 401:
            throw HttpClientException::unauthorized($response);
          case 403:
            throw HttpClientException::forbidden($response);
          case 404:
            throw HttpClientException::notFound($response);
          case 429:
            throw HttpClientException::tooManyRequests($response);
          case 500 <= $statusCode:
            throw HttpServerException::serverError($statusCode);
          default:
            throw new UnknownErrorException();
        }
    }
}
