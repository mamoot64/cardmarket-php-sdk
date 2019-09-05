<?php

namespace Mamoot\CardMarket\Authentication;

use Mamoot\CardMarket\HttpClient\HttpClientCreator;

/**
 * Class AuthenticationHeaderBuilder.
 *
 * Build the Authentication header string base on the Cardmarket documentation
 * https://api.cardmarket.com/ws/documentation/API:Auth_OAuthHeader
 *
 * @package CardMarket\Authentication
 */
final class AuthenticationHeaderBuilder
{
    /**
     * @var string
     */
    private $nonce;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var string
     */
    private $signatureMethod;

    /**
     * @var string
     */
    private $version;

    /**
     * @var HttpClientCreator
     */
    private $httpClientCreator;

    /**
     * @var array
     */
    private $parsedURL;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $credentials;

    public function __construct(HttpClientCreator $httpClientCreator, string $url, string $method = 'GET')
    {
        if (!is_array(parse_url($url))) {
            throw new \LogicException(sprintf("String \"%s\" is malformed and can't be parsed.", $url));
        }

        $this->nonce = uniqid();
        $this->timestamp = time();
        $this->signatureMethod = 'HMAC-SHA1';
        $this->version = '1.0';
        $this->httpClientCreator = $httpClientCreator;
        $this->parsedURL = parse_url($url);
        $this->method = $method;
        $this->credentials = $this->httpClientCreator->retrieveAppCredentials();
    }

    /**
     * Build and return the Authorisation header correctly formatted.
     *
     * @return string
     */
    public function getAuthorisationHeaderValue(): string
    {
        $this->parameters = self::computeParameters();
        $this->parameters['oauth_signature'] = self::createOAuthSignature();

        $header = 'OAuth ';
        $headerParams = [];
        foreach ($this->parameters as $key => $value) {
            $headerParams[] = $key . '="' . $value . '"';
        }
        $header .= implode(', ', $headerParams);

        return $header;
    }

    /**
     * Create the OAuth signature based on HMAC-SHA1 algorithm.
     *
     * @return string
     */
    private function createOAuthSignature(): string
    {
        $finalUrl = strtoupper($this->method) . '&' . rawurlencode(self::getUrlCall()) . '&';

        $paramsString = rawurlencode(http_build_query($this->encodeParameters()));
        $finalUrl .= $paramsString;

        $signatureKey = rawurlencode($this->credentials['application_secret']) . '&' . rawurlencode($this->credentials['access_secret']);
        $rawSignature = hash_hmac('sha1', $finalUrl, $signatureKey, true);

        return base64_encode($rawSignature);
    }

    /**
     * Encode each parameters and sort for the OAuth signature.
     *
     * @return array
     */
    private function encodeParameters(): array
    {
        $encodedParams = [];

        foreach ($this->parameters as $key => $value) {
            if ('realm' !== $key) {
                $encodedParams[rawurlencode((string)$key)] = rawurlencode((string)$value);
            }
        }

        ksort($encodedParams);

        return $encodedParams;
    }

    /**
     * Merge the needed headers params with query string params
     *
     * @return array
     */
    private function computeParameters(): array
    {
        $params = [
          'realm' => self::getUrlCall(),
          'oauth_consumer_key' => $this->credentials['application_token'],
          'oauth_token' => $this->credentials['access_token'],
          'oauth_nonce' => $this->nonce,
          'oauth_timestamp' => $this->timestamp,
          'oauth_signature_method' => $this->signatureMethod,
          'oauth_version' => $this->version,
        ];

        $params = array_merge($params, self::extractQueryParams());

        return $params;
    }

    /**
     * Simple helper to create base query URL without query params.
     *
     * @return string
     */
    private function getUrlCall(): string
    {
        return sprintf('%s://%s%s', $this->parsedURL['scheme'], $this->parsedURL['host'], $this->parsedURL['path']);
    }

    /**
     * Retrieve only query params from given URL.
     *
     * @return array
     */
    private function extractQueryParams(): array
    {
        if (!isset($this->parsedURL['query'])) {
            return [];
        }

        parse_str($this->parsedURL['query'], $queryParams);

        return $queryParams;
    }
}
