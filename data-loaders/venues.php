<?php
/**
 * TicketEvolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution
 * @copyright   Copyright (c) 2012 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */


// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array(
    'table' => 'venues',
    'type'  => 'active',
);

require_once 'bootstrap.php';
require_once 'includes/common.php';

// Create the TicketEvolution_Db_Table object
$table = new TicketEvolution_Db_Table_Venues();

for ($currentPage = $options['page']; $currentPage <= $maxPages; $currentPage++) {
    /*******************************************************************************
     * Fetch the JSON to process
     */
    // Set the current page
    $options['page'] = $currentPage;

    // Set the current $tryCount
    if (!isset($tryCount)) {
        $tryCount = 1;
    }

    // Execute the request
    try {
        echo '<p>Trying page: ' . $options['page'] . ' for the ' . $tryCount . ' time.</p>' . PHP_EOL;
        $results = $tevo->listVenues($options);
    } catch(Exception $e) {
        /**
         * In case of API timeout we will decrement the $currentPage and then
         * continue(1) in order to retry the current attempt.
         * Use the $tryCount to keep track of how many attempts and only throw an
         * exception after a total of 3 tries
         */
        if ($e->getCode() == '1000') { // 1000 = timeout
            $tryCount++;

            if ($tryCount > 3) {
                throw new TicketEvolution_Webservice_Exception($e);
            }

            // Decrement the $currentPage as it will be incremented at the top of
            // the loop after we continue()
            $currentPage--;
            continue(1);
        } else {
            throw new TicketEvolution_Webservice_Exception($e);
        }
    }

    // unset the $tryCount
    unset($tryCount);

    // Set the correct $maxPages
    if ($maxPages == $defaultMaxPages) {
        $maxPages = $results->totalPages();
    }

    if ($showStats) {
        $curMem = memory_get_usage(true);
        $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
        echo '<h1>Current memory usage after fetching page ' . $currentPage . ' of ' . $maxPages . ': ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL;
    }

    /*******************************************************************************
     * Process the API results either INSERTing or UPDATEing our table(s)
     */
    foreach ($results AS $result) {
        $data = array(
            'venueId'                   => (int)$result->id,
            'venueName'                 => (string) $result->name,

            'streetAddress'             => (string) $result->address->street_address,
            'extendedAddress'           => (string) $result->address->extended_address,
            'locality'                  => (string) $result->address->locality,
            'region'                    => (string) $result->address->region,
            'postalCode'                => (string) $result->address->postal_code,
            'venueUrl'                  => (string) $result->url,
            'updated_at'                => (string) $result->updated_at,

            'venueStatus'               => (int)    1,
            'lastModifiedDate'          => (string) $startTime->format('c'),
        );
        if (isset($result->address->country_code)) {
            $data['countryCode'] = $result->address->country_code;
        }
        if (isset($result->address->latitude)) {
            $data['latitude'] = $result->address->latitude;
        }
        if (isset($result->address->longitude)) {
            $data['longitude'] = $result->address->longitude;
        }
        if (!empty($result->upcoming_events->first)) {
            // Ensure the timezone is not incorrectly adjusted
            $firstEvent = new DateTime($result->upcoming_events->first);
            $firstEvent->setTimezone($localTZ);

            $data['upcomingEventFirst'] = (string) $firstEvent->format('c');
        }
        if (!empty($result->upcoming_events->last)) {
            // Ensure the timezone is not incorrectly adjusted
            $lastEvent = new DateTime($result->upcoming_events->last);
            $lastEvent->setTimezone($localTZ);

            $data['upcomingEventLast'] = (string) $lastEvent->format('c');
        }

        if ($row = $table->find((int) $result->id)->current()) {
            $row->setFromArray($data);
            $action = 'UPDATE';
        } else {
            $row = $table->createRow($data);
            $action = 'INSERT';
        }

        try {
            $row->save();
            echo '<h1>'
               . htmlentities('Successful ' . $action . ' of ' . $data['venueId'] . ': ' . $data['venueName'] . ' to `tevoVenues`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;

        } catch (Exception $e) {
            $message = 'Error attempting to ' . $action . ' ' . $data['venueId'] . ': ' . $data['venueName'] . ' to `tevoVenues`';

            echo '<h1 class="error">'
               . htmlentities($message, ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;

            continue 1;
        }
        unset($action);
        unset($data);
        unset($row);

    } // End loop through this page of results
    if ($showStats) {
        $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
        echo '<h1>Current memory usage after database work of page ' . $currentPage . ' of ' . $maxPages . ': ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL;
    }
    echo '<h1>Done with page ' . $currentPage . '</h1>' . PHP_EOL;
    @ob_end_flush();
    @ob_flush();
    @flush();
} // End looping through all pages

// Update `tevoDataLoaderStatus` with current info
$statusData['lastRun'] = (string) $startTime->format('c');;
if (isset($statusRow)) {
    $statusRow->setFromArray($statusData);
} else {
    $statusRow = $statusTable->createRow($statusData);
}
$statusRow->save();


echo '<h1>Finished updating `tevo' . $statusData['table'] . '` table</h1>' . PHP_EOL;

if ($showStats) {
    $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
    $peakMem = new Zend_Measure_Binary(memory_get_peak_usage(true), Zend_Measure_Binary::BYTE);
    echo '<h1>Current memory usage at end of script: ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL
       . '<h1>PEAK memory usage: ' . $peakMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL
    ;
}
