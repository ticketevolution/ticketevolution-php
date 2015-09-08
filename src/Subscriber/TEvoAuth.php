<?php namespace TicketEvolution\Subscriber;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Query;

/**
 * Ticket Evolution API signature plugin.
 *
 * @link http://docs.guzzlephp.org/en/latest/clients.html#custom-authentication-schemes Guzzle Custom Authentication Schemes
 * @link https://ticketevolution.atlassian.net/wiki/display/API/Signing Ticket Evolution X-Signature Documentation
 */
class TEvoAuth implements SubscriberInterface
{
    /**
     * API Token
     */
    private $_apiToken;

    /**
     * API Secret
     */
    private $_apiSecret;


    /**
     * Create a new TEvoAuth plugin.
     *
     * @param string $apiToken  API Token.
     * @param string $apiSecret API Secret.
     */
    public function __construct($apiToken, $apiSecret)
    {
        $this->_apiToken = $apiToken;
        $this->_apiSecret = $apiSecret;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The returned array keys MUST map to an event name. Each array value
     * MUST be an array in which the first element is the name of a function
     * on the EventSubscriber. The second element in the array is optional, and
     * if specified, designates the event priority.
     *
     * For example:
     *
     *  - ['eventName' => ['methodName']]
     *  - ['eventName' => ['methodName', $priority]]
     *
     * @return array
     */
    public function getEvents()
    {
        return ['before' => ['sign', RequestEvents::SIGN_REQUEST]];
    }


    /**
     * Check if the requested authentication type matches and set the
     * required X-Token and X-Signature headers
     *
     * @param BeforeEvent $event
     */
    public function sign(BeforeEvent $event)
    {
        if ($event->getRequest()->getConfig()['auth'] == 'tevoauth') {
            $request = $event->getRequest();

            $request->setHeader('X-Token', $this->_apiToken);
            $request->setHeader('X-Signature', $this->getSignature($request));
        }
    }


    /**
     * Calculate signature for request
     *
     * @param RequestInterface $request Request to generate a signature for
     *
     * @return string
     */
    public function getSignature(RequestInterface $request)
    {
        // For POST|PUT set the JSON body string as the params
        if ($request->getMethod() == 'POST' || $request->getMethod() == 'PUT') {
            $params = $request->getBody()->__toString();

            /**
             * If you don't seek() back to the beginning then attempting to
             * send a JSON body > 1MB will probably fail.
             *
             * @link http://stackoverflow.com/q/32359664/99071
             * @link https://groups.google.com/forum/#!topic/guzzle/vkF5druf6AY
             */
            $request->getBody()->seek(0);

            // Make sure to remove any other query params
            $request->setQuery([]);
        } else {
            $params = Query::fromString($request->getQuery(), Query::RFC1738)->toArray();

            $params = $this->prepareParameters($params);

            // Re-Set the query to the properly ordered query string
            $request->setQuery($params);
            $request->getQuery()->setEncodingType(Query::RFC1738);
        }

        $baseString = $this->createBaseString(
            $request,
            $params
        );

        return base64_encode($this->sign_HMAC_SHA256($baseString));
    }


    /**
     * Creates the Signature Base String.
     *
     * The Signature Base String is a consistent reproducible concatenation of
     * the request elements into a single string. The string is used as an
     * input in hashing or signing algorithms.
     *
     * @param RequestInterface $request Request being signed
     * @param array            $params  HTTP Request parameters
     *
     * @return string Returns the base string
     */
    protected function createBaseString(RequestInterface $request, $params = [])
    {
        // Remove query params from URL.
        $url = preg_replace('/https:\/\/|\?.*/', '', $request->getUrl());

        if (is_array($params)) {
            $query = http_build_query($params, '', '&', PHP_QUERY_RFC1738);
        } else {
            $query = $params;
        }

        return strtoupper($request->getMethod())
        . ' ' . $url
        . '?' . $query;
    }


    /**
     * Sorts the array and unsets null parameters
     *
     * @param array $data Data array
     *
     * @return array
     */
    private function prepareParameters($data)
    {
        // Parameters are sorted by name, using lexicographical byte value ordering.
        uksort($data, 'strcmp');

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        return $data;
    }


    /**
     * Perform the HMAC SHA256 signing using the $apiSecret
     *
     * @param $baseString
     *
     * @return string
     */
    private function sign_HMAC_SHA256($baseString)
    {
        return hash_hmac('sha256', $baseString, $this->_apiSecret, true);
    }
}
