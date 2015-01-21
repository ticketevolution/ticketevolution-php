<?php namespace TicketEvolution\Subscriber;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;

/**
 */
class RequestTimer implements SubscriberInterface
{
    /**
     * Microtime of just before the request is signed
     *
     * @var float
     */
    protected $_timeStart;


    /**
     * Microtime of when the request is complete
     *
     * @var float
     */
    protected $_timeEnd;


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
        return [
            'before'   => ['startTimer', RequestEvents::SIGN_REQUEST - 1],
            'complete' => ['endTimer'],
        ];
    }


    /**
     * Set the start time
     *
     * @param BeforeEvent $event
     */
    public function startTimer(BeforeEvent $event)
    {
        $this->_timeStart = microtime(true);
        $this->_timeEnd = null;
    }


    /**
     * Set the completed time
     *
     * @param CompleteEvent $event
     */
    public function endTimer(CompleteEvent $event)
    {
        $this->_timeEnd = microtime(true);
    }


    /**
     * Return the elapsed time of the request
     *
     * @return bool|float
     */
    public function getElapsedTime()
    {
        try {
            return $this->_timeEnd - $this->_timeStart;
        } catch (\Exception $e) {
            return null;
        }
    }
}
