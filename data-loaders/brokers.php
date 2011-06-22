<?php

require_once 'application.php';
error_reporting (E_ALL);
ini_set('max_execution_time', 1200);

// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array((string)'table' => 'brokers');

require_once './includes/common.php';

// Create the Ticketevolution_Db_Table object
$table = new Ticketevolution_Db_Table_Brokers();

for($currentPage = $options['page']; $currentPage <= $maxPages; $currentPage++) {
    /*******************************************************************************
     * Fetch the JSON to process
     */
    // Set the current page
    $options['page'] = $currentPage;
    
    // Execute the request
    try{
        $results = $tevo->listBrokers($options);
    } catch(Ticketevolution_Webservice_Exception $e) {
        //continue;
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
            'brokerId' => (int)$result->id,
            'brokerName' => (string)$result->name,
            'brokerAbbreviation' => (string)$result->abbreviation,
            'natbMember' => (int)$result->natb_member,
            'brokerUrl' => (string)$result->url,
            'updated_at' => (string)$result->updated_at->get(Zend_Date::ISO_8601),
            'brokerStatus' => (int)1,
            'lastModifiedDate' => (string)$now->get(Zend_Date::ISO_8601),
        );

        if($row = $table->fetchRow($table->select()->where('`brokerId` = ?', $data['brokerId']))) {
            $row->setFromArray($data);
        } else {
            $row = $table->createRow($data);
        }
        if(!$row->save()) {
            echo '<h1 class="error">Error attempting to save ' . tohtmlentities($data['brokerId'] . ': ' . $data['brokerName']) . ' to `tevoBrokers`</h1>' . PHP_EOL;
        } else {
            echo '<h1>Saved ' . tohtmlentities($data['brokerId'] . ': ' . $data['brokerName']) . ' to `tevoBrokers`</h1>' . PHP_EOL;
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
$statusData['lastRun'] = (string)$now->get(Onyx_Date::ISO_8601);
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