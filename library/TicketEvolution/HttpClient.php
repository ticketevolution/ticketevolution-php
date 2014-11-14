<?php

/**
 * Ticket Evolution PHP Client Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD (3-Clause) License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://choosealicense.com/licenses/bsd-3-clause/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@ticketevolution.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice
 * @copyright   Copyright (c) 2014 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace TicketEvolution;

use Zend_Http_Client;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Webservice
 * @copyright   Copyright (c) 2014 Ticket Evolution, Inc. (http://www.ticketevolution.com)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 * @link        http://developer.ticketevolution.com/
 */
class HttpClient extends Zend_Http_Client
{
    /**
     * Add a POST data encoding method for application/json
     */
    const ENC_JSON   = 'application/json';


    /**
     * Perform a POST or PUT
     *
     * Performs a POST or PUT request. Any data provided is set in the HTTP
     * client. String data is pushed in as raw POST data; array or object data
     * is pushed in as POST parameters.
     *
     * @param mixed $method
     * @param mixed $data
     * @return Zend_Http_Response
     */
    protected function _performPost($method, $data = null)
    {
        $client = self::getHttpClient();
        if (is_string($data)) {
            $client->setRawData($data, Zend_Http_Client::ENC_JSON);
        } elseif (is_array($data) || is_object($data)) {
            $client->setParameterPost((array) $data);
        }
        return $client->request($method);
    }


}
