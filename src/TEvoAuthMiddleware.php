<?php

namespace TicketEvolution;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TEvoAuthMiddleware
{
    /**
     *  API Token
     */
    protected $apiToken;

    /**
     *  API Secret
     */
    protected $apiSecret;


    /**
     * @param string $apiToken
     * @param string $apiSecret
     */
    public function __construct($apiToken, $apiSecret)
    {
        $this->apiToken = $apiToken;
        $this->apiSecret = $apiSecret;
    }


    /**
     * Called when the middleware is handled.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {

            $request = $this->signRequest($request);

            $promise = function (ResponseInterface $response) use ($request) {
                return $response;
            };

            return $handler($request, $options)->then($promise);
        };
    }


    /**
     * Signs the request with the appropriate headers.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function signRequest(RequestInterface $request): RequestInterface
    {
        $request = $this->getRequestWithSortedParameters($request);
        $request = $this->getRequestWithXToken($request);
        $request = $this->getRequestWithXSignature($request);

        return $request;
    }


    /**
     * Signs the request with the appropriate headers.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequestWithSortedParameters(RequestInterface $request): RequestInterface
    {
        $sortedParams = $this->prepareParameters($this->getParametersFromRequest($request));

        // Re-Set the query to the properly ordered query string
        if (method_exists('\GuzzleHttp\Psr7\Query','build')) {
            // GuzzleHttp\Psr7 version 2+
            $query = \GuzzleHttp\Psr7\Query::build($sortedParams, PHP_QUERY_RFC1738);
        } else {
            // GuzzleHttp\Psr7 version 1
            $query = \GuzzleHttp\Psr7\build_query($sortedParams, PHP_QUERY_RFC1738);
        }
        $uri = $request->getUri()->withQuery($query);
        $request = $request->withUri($uri);

        return $request;
    }


    /**
     * Adds the X-Token header to the request.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequestWithXToken(RequestInterface $request): RequestInterface
    {
        $request = $request->withHeader('X-Token', $this->apiToken);

        return $request;
    }


    /**
     * Signs the request with the X-Signature header.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequestWithXSignature(RequestInterface $request): RequestInterface
    {
        $request = $request->withHeader('X-Signature', $this->getSignature($request));

        return $request;
    }


    /**
     * Calculate signature for request
     *
     * @param RequestInterface $request Request to generate a signature for
     *
     * @return string
     */
    public function getSignature(RequestInterface $request): string
    {
        $stringToSign = $this->getStringToSign($request);

        return base64_encode($this->signHmacSha256($stringToSign));

    }


    /**
     * Calculate signature for request
     *
     * @param RequestInterface $request Request to generate a signature for
     *
     * @return string
     */
    public function getStringToSign(RequestInterface $request): string
    {
        // For POST|PUT set the JSON body string as the params
        if ($request->getMethod() == 'POST' || $request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') {
            $data = $request->getBody()->__toString();
        } else {
            $data = $this->getParametersFromRequest($request);
        }

        $baseString = $this->createBaseString(
            $request,
            $data
        );

        return $baseString;
    }


    /**
     * Creates the Signature Base String.
     *
     * The Signature Base String is a consistent reproducible concatenation of
     * the request elements into a single string. The string is used as an
     * input in hashing or signing algorithms.
     *
     * @param RequestInterface $request Request being signed
     * @param array|string     $data    HTTP Request parameters
     *
     * @return string Returns the base string
     */
    protected function createBaseString(RequestInterface $request, $data = []): string
    {
        // Remove query params from URL.
        $url = preg_replace('/https:\/\/|\?.*/', '', $request->getUri());

        if (is_array($data)) {
            $query = http_build_query($data, '', '&', PHP_QUERY_RFC1738);
        } else {
            $query = $data;
        }

        return strtoupper($request->getMethod())
            . ' ' . $url
            . '?' . $query;
    }


    /**
     * Sorts the array and removes null parameters
     *
     * @param array $params Data array
     *
     * @return array
     */
    private function prepareParameters($params): array
    {
        // Parameters are sorted by name, using lexicographical byte value ordering.
        uksort($params, 'strcmp');

        // Unset any parameters with null values
        foreach ($params as $key => $value) {
            if ($value === null) {
                unset($params[$key]);
            }
        }

        return $params;
    }


    /**
     * Perform the HMAC SHA256 signing using the $apiSecret
     *
     * @param $baseString
     *
     * @return string
     */
    private function signHmacSha256($baseString): string
    {
        return hash_hmac('sha256', $baseString, $this->apiSecret, true);
    }


    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    protected function getParametersFromRequest(RequestInterface $request): array
    {
        $uri = $request->getUri();
        if (method_exists('\GuzzleHttp\Psr7\Query','parse')) {
            // GuzzleHttp\Psr7 version 2+
            $params = \GuzzleHttp\Psr7\Query::parse($uri->getQuery(), PHP_QUERY_RFC1738);
        } else {
            // GuzzleHttp\Psr7 version 1
            $params = \GuzzleHttp\Psr7\parse_query($uri->getQuery(), PHP_QUERY_RFC1738);
        }

        return $params;
    }
}
