<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\HttpClient;

use Mamoot\CardMarket\Exception\HttpClientNotConfiguredException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientCreator
{
    const API_URL = 'https://api.cardmarket.com/ws/v2.0/output.json';

    /**
     * @var array
     */
    private $clientParams = [];

    /**
     * @var array
     */
    private $defaultParams = [
      'http_version' => '2.0',
    ];

    /**
     * @var string
     */
    private $applicationToken;

    /**
     * @var string
     */
    private $applicationSecret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $accessSecret;

    /**
     * HttpClientCreator constructor.
     *
     * @param array $clientParams
     *    The custom parameters to build the HttpClient.
     */
    public function __construct(array $clientParams = [])
    {
        $this->clientParams = $clientParams;
    }

    /**
     * Build a dedicated client to consume CardMarket API
     *
     * @return HttpClientInterface
     */
    public function createHttpClient(): HttpClientInterface
    {
        if (!self::isConfigured()) {
            throw new HttpClientNotConfiguredException();
        }

        return HttpClient::create(self::mergeParameters());
    }

    /**
     * Dedicated merge parameters method.
     *
     * @return array
     */
    private function mergeParameters(): array
    {
        return array_merge($this->defaultParams, $this->clientParams);
    }

    /**
     * Check if CardMarket Tokens and Secrets are set.
     *
     * @return bool
     */
    private function isConfigured(): bool
    {
        return !empty($this->applicationSecret)
           && !empty($this->applicationToken)
           && !empty($this->accessToken)
           && !empty($this->accessSecret);
    }

    public function retrieveAppCredentials(): array
    {
        return [
          'application_secret' => $this->applicationSecret,
          'application_token' => $this->applicationToken,
          'access_token' => $this->accessToken,
          'access_secret' => $this->accessSecret,
        ];
    }

    /**
     * Set CardMarket application secret string.
     *
     * @param string $appSecret
     *
     * @return HttpClientCreator
     */
    public function setApplicationSecret(string $appSecret): self
    {
        $this->applicationSecret = $appSecret;

        return $this;
    }

    /**
     * Set CardMarket application token string.
     *
     * @param string $appToken
     *
     * @return HttpClientCreator
     */
    public function setApplicationToken(string $appToken): self
    {
        $this->applicationToken = $appToken;

        return $this;
    }

    /**
     * Set CardMarket access token string.
     *
     * @param string $accessToken
     *
     * @return HttpClientCreator
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Set CardMarket access secret string.
     *
     * @param string $accessSecret
     *
     * @return HttpClientCreator
     */
    public function setAccessSecret(string $accessSecret): self
    {
        $this->accessSecret = $accessSecret;

        return $this;
    }
}
