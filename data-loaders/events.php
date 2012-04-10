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
    'table' => 'events',
    'type'  => 'active',
);

require_once 'bootstrap.php';
require_once 'includes/common.php';

// Create the TicketEvolution_Db_Table object
$table = new TicketEvolution_Db_Table_Events();

// Create an object for the `tevoEventPerformers` table too
$epTable = new TicketEvolution_Db_Table_EventPerformers();

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
        $results = $tevo->listEvents($options);
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
        echo '<h2>Current memory usage after fetching page ' . $currentPage . ' of ' . $maxPages . ': ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h2>' . PHP_EOL;
    }

    /*******************************************************************************
     * Process the API results either INSERTing or UPDATEing our table(s)
     */
    foreach ($results AS $result) {
        // Ensure the timezone is not incorrectly adjusted
        $occursAt = preg_replace('/[Z]/i', '', $result->occurs_at);

        $data = array(
            'eventId'           => (int)    $result->id,
            'eventName'         => (string) $result->name,
            'eventDate'         => (string) $occursAt,
            'venueId'           => (int)    $result->venue->id,
            'categoryId'        => (int)    $result->category->id,
            'productsCount'     => (int)    $result->products_count,
            'eventUrl'          => (string) $result->url,
            'updated_at'        => (string) $result->updated_at,
            'eventStatus'       => (int)    1,
            'eventState'        => (string) $result->state,
            'lastModifiedDate'  => (string) $startTime->format('c'),
        );
        if (isset($result->configuration->id)) {
            $data['configurationId'] = (int) $result->configuration->id;
        }

        if ($row = $table->find((int) $result->id)->current()) {
            $row->setFromArray($data);
            $action = 'UPDATE';
        } else {
            $row = $table->createRow($data);
            $action = 'INSERT';
        }

        if (!$row->save()) {
            echo '<h1 class="error">'
               . htmlentities('Error attempting to ' . $action . ' ' . $result->id . ': ' . $result->name . ' to `tevoEvents`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
        } else {
            echo '<h1>'
               . htmlentities('Successful ' . $action . ' of ' . $result->id . ': ' . $result->name . ' to `tevoEvents`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
        }
        unset($action);
        unset($data);
        unset($row);

        // Set a list of performers we can append names to
        $performerList = (string)'';

        // Loop through the performers and add them to the `tevoEventPerformers` table
        foreach ($result->performances as $performance) {
            /**
             * Amazingly, some events have no performances. Account for someone
             * else's stupidity by skipping
             */
            if (isset($performance->performer->id)) {
                $data = array(
                    'eventId'               => (int)    $result->id,
                    'performerId'           => (int)    $performance->performer->id,
                    'isPrimary'             => (int)    $performance->primary,
                    'lastModifiedDate'      => (string) $startTime->format('c'),
                    'eventPerformersStatus' => (int)    1,
                );

                if ($row = $epTable->find($data['eventId'], $data['performerId'])->current()) {
                    $row->setFromArray($data);
                } else {
                    $row = $epTable->createRow($data);
                }
                $row->save();
                unset($row);

                $performerArray[] = (int) $performance->performer->id;
                if ($data['isPrimary']) {
                    $performerList .= '<b>' . $performance->performer->id . '</b>, ';
                } else {
                    $performerList .= $performance->performer->id . ', ';
                }
                unset($data);
            }
        } // End loop through performers for this event
        echo '<p>Saved ' . substr($performerList, 0, -2) . ' to `tevoEventPerformers` for this event</p>' . PHP_EOL;
        unset($performerList);

        // Now delete any `tevoEventPerformers` entries for any performers not in
        // $performerList. This will remove any performers that were attached
        // to this event but are no longer
        if (isset($performerArray)) {
            $where = $epTable->getAdapter()->quoteInto("`eventId` = ?", $result->id);
            $where .= $epTable->getAdapter()->quoteInto(" AND `performerId` NOT IN (?)", $performerArray);
            $epTable->delete($where);
            unset($performerArray);
        }
    } // End loop through this page of results
    if ($showStats) {
        $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
        echo '<h2>Current memory usage after database work of page ' . $currentPage . ' of ' . $maxPages . ': ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h2>' . PHP_EOL;
    }
    echo '<h1>Done with page ' . $currentPage . '</h1>' . PHP_EOL;
    @ob_end_flush();
    @ob_flush();
    @flush();
} // End looping through all pages

// Update `tevoDataLoaderStatus` with current info
$statusData['lastRun'] = (string) $startTime->format('c');
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
