<?php

declare(strict_types=1);

namespace TicketEvolution;

use Closure;
use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TEvoAuthMiddleware
{
    public function __construct(protected string $apiToken, protected string $apiSecret)
    {
    }

    /**
     * Called when the middleware is handled.
     */
    public function __invoke(callable $handler): Closure
    {
        return function ($request, array $options) use ($handler) {
            $request = $this->signRequest($request);

            $promise = function (ResponseInterface $response) {
                return $response;
            };

            return $handler($request, $options)->then($promise);
        };
    }

    /**
     * Signs the request with the appropriate headers.
     */
    private function signRequest(RequestInterface $request): RequestInterface
    {
        return $this->getRequestWithXSignature(
            $this->getRequestWithXToken(
                $this->getRequestWithSortedParameters($request)
            )
        );
    }

    private function getRequestWithSortedParameters(RequestInterface $request): RequestInterface
    {
        $sortedParams = $this->prepareParameters($this->getParametersFromRequest($request));

        $query = Query::build($sortedParams, PHP_QUERY_RFC1738);

        $uri = $request->getUri()->withQuery(
            Query::build(
                params: $this->prepareParameters($this->getParametersFromRequest($request)),
                encoding: PHP_QUERY_RFC1738
            )
        );

        return $request->withUri(
            $request->getUri()->withQuery(
                Query::build(
                    $this->prepareParameters(
                        params: $this->getParametersFromRequest($request)),
                    encoding: PHP_QUERY_RFC1738
                )
            )
        );
    }

    /**
     * Adds the X-Token header to the request.
     */
    private function getRequestWithXToken(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('X-Token', $this->apiToken);
    }

    /**
     * Signs the request with the X-Signature header.
     */
    private function getRequestWithXSignature(RequestInterface $request): RequestInterface
    {
        return $request->withHeader('X-Signature', $this->getSignature($request));
    }

    /**
     * Compute the signature for request
     */
    private function getSignature(RequestInterface $request): string
    {
        return base64_encode($this->signHmacSha256($this->getStringToSign($request)));
    }

    private function getStringToSign(RequestInterface $request): string
    {
        $data = match ($request->getMethod()) {
            'POST', 'PUT', 'PATCH' => $request->getBody()->__toString(),
            default => $this->getParametersFromRequest($request),
        };

        return $this->createBaseString(
            $request,
            $data
        );
    }

    /**
     * Creates the Signature Base String.
     *
     * The Signature Base String is a consistent reproducible concatenation of
     * the request elements into a single string. The string is used as an
     * input in hashing or signing algorithms.
     */
    private function createBaseString(RequestInterface $request, $data = []): string
    {
        // Remove query params from URL.
        $url = preg_replace(
            pattern: '/https:\/\/|\?.*/',
            replacement: '',
            subject: $request->getUri()->__toString()
        );

        if (is_array($data)) {
            $query = http_build_query(
                data:$data,
                arg_separator: '&'
            );
        } else {
            $query = $data;
        }

        return strtoupper($request->getMethod())
            .' '.$url
            .'?'.$query;
    }

    /**
     * Sorts the array and removes null parameters
     */
    private function prepareParameters(array $params): array
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
     */
    private function signHmacSha256($baseString): string
    {
        return hash_hmac(
            algo: 'sha256',
            data: $baseString,
            key: $this->apiSecret,
            binary: true
        );
    }

    private function getParametersFromRequest(RequestInterface $request): array
    {
        return Query::parse($request->getUri()->getQuery(), PHP_QUERY_RFC1738);
    }
}
