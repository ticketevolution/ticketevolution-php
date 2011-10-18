<?php

require_once 'bootstrap.php';
error_reporting (E_ALL);
ini_set('max_execution_time', 1200);

// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array((string)'table' => 'configurations');

require_once './includes/common.php';

// Create the TicketEvolution_Db_Table object
$table = new TicketEvolution_Db_Table_Configurations();

for($currentPage = $options['page']; $currentPage <= $maxPages; $currentPage++) {
    /*******************************************************************************
     * Fetch the JSON to process
     */
    // Set the current page
    $options['page'] = $currentPage;

    // Set the current $tryCount
    if(!isset($tryCount)) {
        $tryCount = 1;
    }

    // Execute the request
    try{
        $results = $tevo->listConfigurations($options);
    } catch(Exception $e) {
        /**
         * In case of API timeout we will decrement the $currentPage and then
         * continue(1) in order to retry the current attempt.
         * Use the $tryCount to keep track of how many attempts and only throw an
         * exception after a total of 3 tries
         */
        if($e->getCode() == '1000') { // 1000 = timeout
            $tryCount++;

            if($tryCount > 3) {
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
    if($maxPages == $defaultMaxPages) {
        $maxPages = $results->totalPages();
    }

    /*******************************************************************************
     * Process the API results either INSERTing or UPDATEing our table(s)
     */
    foreach($results AS $result) {
        $data = array(
            'configurationId' => (int)$result->id,
            'venueId' => (int)$result->venue->id,
            'configurationName' => (string)$result->name,
            'isPrimary' => (int)$result->primary,
            'fanvenuesKey' => (string)$result->fanvenues_key,
            'capacity' => (string)$result->capacity,
            'isGeneralAdmission' => (int)$result->general_admission,
            'configurationUrl' => (string)$result->url,
            'updated_at' => (string)$result->updated_at->get(Zend_Date::ISO_8601),
            'configurationStatus' => (int)1,
            'lastModifiedDate' => (string)$now->get(Zend_Date::ISO_8601),
            'urlSeatingChartMedium' => NULL,
            'urlSeatingChartLarge' => NULL,
        );
        if(!empty($result->seating_chart->medium)) {
            $data['urlSeatingChartMedium'] = (string)$result->seating_chart->medium;
        }
        if(!empty($result->seating_chart->large)) {
            $data['urlSeatingChartLarge'] = (string)$result->seating_chart->large;
        }

        if($row = $table->fetchRow($table->select()->where('configurationId = ?', $data['configurationId']))) {
            $row->setFromArray($data);
        } else {
            $row = $table->createRow($data);
        }
        if(!$row->save()) {
            echo '<h1 class="error">Error attempting to save ' . htmlentities($data['configurationId'] . ': ' . $data['configurationName'], ENT_QUOTES, 'UTF-8', false) . ' to `tevoConfigurations`</h1>' . PHP_EOL;
        } else {
            echo '<h1>Saved ' . htmlentities($data['configurationId'] . ': ' . $data['configurationName'], ENT_QUOTES, 'UTF-8', false) . ' to `tevoConfigurations`</h1>' . PHP_EOL;
        }
        unset($data);
        unset($row);

    } // End loop through this page of results

    echo '<h1>Done with page ' . $currentPage . '</h1>' . PHP_EOL;
    @ob_end_flush();
    @ob_flush();
    @flush();
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
