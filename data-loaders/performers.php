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
    'table' => 'performers',
    'type'  => 'active',
);

require_once 'bootstrap.php';
require_once 'includes/common.php';

// Create the TicketEvolution_Db_Table object
$table = new TicketEvolution_Db_Table_Performers();

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
        $results = $tevo->listPerformers($options);
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

    /*******************************************************************************
     * Process the API results either INSERTing or UPDATEing our table(s)
     */
    foreach ($results AS $result) {
        $data = array(
            'performerId'                   => (int)    $result->id,
            'performerName'                 => (string) $result->name,
            'performerUrl'                  => (string) $result->url,
            'updated_at'                    => (string) $result->updated_at->get(TicketEvolution_Date::ISO_8601),
            'performerStatus'               => (int)    1,
            'lastModifiedDate'              => (string) $startTime->get(TicketEvolution_Date::ISO_8601)
        );
        if (isset($result->venue->id)) {
            $data['venueId'] = (int) $result->venue->id;
        }
        if (!empty($result->upcoming_events->first)) {
            $data['upcomingEventFirst'] = (string) $result->upcoming_events->first->get(TicketEvolution_Date::ISO_8601);
        }
        if (!empty($result->upcoming_events->last)) {
            $data['upcomingEventLast'] = (string) $result->upcoming_events->last->get(TicketEvolution_Date::ISO_8601);
        }
        if (isset($result->category->id)) {
            $data['categoryId'] = (int) $result->category->id;
        }

        try {
            $row = $table->find($data['performerId'])->current();
        } catch (Exception $e) {
            $message = 'Failed attempting to see if `performerId` ' . $akaData['performerId'] . ' exists in `tevoPerformers` so we can determine if we need to INSERT or UPDATE.';
            echo '<h1 class="error">'
               . htmlentities($message, ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;

            continue 1;
        }
        if (!empty($row)) {
            // This means there was an existing row and therefore, this is an UPDATE
            $row->setFromArray($data);
            $action = 'UPDATE';
        } else {
            $row = $table->createRow($data);
            $action = 'INSERT';
        }

        try {
            $row->save();
            echo '<h1>'
               . htmlentities('Successful ' . $action . ' of ' . $data['performerId'] . ': ' . $data['performerName'] . ' to `tevoPerformers`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
        } catch(Exception $e) {
            $message = 'Error attempting to ' . $action . ' ' . $data['performerId'] . ': ' . $data['performerName'] . ' to `tevoPerformers`';

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

    echo '<h1>Done with page ' . $currentPage . '</h1>' . PHP_EOL;
    @ob_end_flush();
    @ob_flush();
    @flush();
} // End looping through all pages

// Update `tevoDataLoaderStatus` with current info
$statusData['lastRun'] = (string) $startTime->get(TicketEvolution_Date::ISO_8601);
if (isset($statusRow)) {
    $statusRow->setFromArray($statusData);
} else {
    $statusRow = $statusTable->createRow($statusData);
}
$statusRow->save();


echo '<h1>Finished updating `tevo' . $statusData['table'] . '` table</h1>' . PHP_EOL;
