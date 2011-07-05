<?php

require_once 'bootstrap.php';
error_reporting (E_ALL);
ini_set('max_execution_time', 1200);

// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array((string)'table' => 'venues');

require_once './includes/common.php';

// Create the Ticketevolution_Db_Table object
$table = new Ticketevolution_Db_Table_Venues();

for($currentPage = $options['page']; $currentPage <= $maxPages; $currentPage++) {
    /*******************************************************************************
     * Fetch the JSON to process
     */
    // Set the current page
    $options['page'] = $currentPage;
    
    // Execute the request
    try{
        $results = $tevo->listVenues($options);
    } catch(Exception $e) {
        throw new Ticketevolution_Webservice_Exception($e);
    }
    
    // Set the correct $maxPages
    if($maxPages == $defaultMaxPages) {
        $maxPages = $results->totalPages();
    }

    if($showStats) {
        $curMem = memory_get_usage(true);
        $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
        echo '<h1>Current memory usage after fetching page ' . $currentPage . ' of ' . $maxPages . ': ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL;
    }
    
    /*******************************************************************************
     * Process the API results either INSERTing or UPDATEing our table(s)
     */
    foreach($results AS $result) {
        $data = array(
            'venueId' => (int)$result->id,
            'venueName' => (string)$result->name,
            'streetAddress' => (string)$result->address->street_address,
            'extendedAddress' => (string)$result->address->extended_address,
            'locality' => (string)$result->address->locality,
            'regionCode' => (string)$result->address->region,
            'postalCode' => (string)$result->address->postal_code,
            'countryCode' => (string)$result->address->country_code,
            'venueUrl' => (string)$result->url,
            'updated_at' => (string)$result->updated_at->get(Zend_Date::ISO_8601),
            'venueStatus' => (int)1,
            'lastModifiedDate' => (string)$now->get(Zend_Date::ISO_8601)
        );
        if(isset($result->address->countryCode)) {
            $data['countryCode'] = (int)$result->address->countryCode;
        }
        if(!empty($result->upcoming_events->first)) {
            $data['upcomingEventFirst'] = (string)$result->upcoming_events->first->get(Zend_Date::ISO_8601);
        }
        if(!empty($result->upcoming_events->last)) {
            $data['upcomingEventLast'] = (string)$result->upcoming_events->last->get(Zend_Date::ISO_8601);
        }

        if($row = $table->fetchRow($table->select()->where('venueId = ?', $data['venueId']))) {
            $row->setFromArray($data);
        } else {
            $row = $table->createRow($data);
        }
        if(!$row->save()) {
            echo '<h1 class="error">Error attempting to save ' . tohtmlentities($data['venueId'] . ': ' . $data['venueName']) . ' to `tevoVenues`</h1>' . PHP_EOL;
        } else {
            echo '<h1>Saved ' . tohtmlentities($data['venueId'] . ': ' . $data['venueName']) . ' to `tevoVenues`</h1>' . PHP_EOL;
        }
        unset($data);
        unset($row);

    } // End loop through this page of results
    if($showStats) {
        $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
        echo '<h1>Current memory usage after database work of page ' . $currentPage . ' of ' . $maxPages . ': ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL;
    }
    echo '<h1>Done with page ' . $currentPage . '</h1>' . PHP_EOL;
    sleep(1);
} // End looping through all pages

// Update `tevoDataLoaderStatus` with current info
$statusData['lastRun'] = (string)$now->get(Zend_Date::ISO_8601);
if(isset($statusRow)) {
    $statusRow->setFromArray($statusData);
} else {
    $statusRow = $statusTable->createRow($statusData);
}
$statusRow->save();


echo '<h1>Finished updating `tevo' . $statusData['table'] . '` table</h1>' . PHP_EOL;

if($showStats) {
    $allTimer->endTimer();
    $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
    $peakMem = new Zend_Measure_Binary(memory_get_peak_usage(true), Zend_Measure_Binary::BYTE);
    echo '<p class="codetimer">Time to complete everything: ' . $allTimer->getElapsedTime() . '</p>' . PHP_EOL
       . '<h1>Current memory usage at end of script: ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL
       . '<h1>PEAK memory usage: ' . $peakMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</h1>' . PHP_EOL
    ;
}