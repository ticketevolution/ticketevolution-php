<?php

require_once 'bootstrap.php';
error_reporting (E_ALL);
ini_set('max_execution_time', 1200);

// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array((string)'table' => 'offices');

require_once './includes/common.php';

// Create the Ticketevolution_Db_Table object
$table = new Ticketevolution_Db_Table_Offices();

// Create an object for the `tevoOfficeEmails` table too
$eTable = new Ticketevolution_Db_Table_Officeemails();

for($currentPage = $options['page']; $currentPage <= $maxPages; $currentPage++) {
    /*******************************************************************************
     * Fetch the JSON to process
     */
    // Set the current page
    $options['page'] = $currentPage;
    
    // Execute the request
    try{
        $results = $tevo->listOffices($options);
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
            'officeId' => (int)$result->id,
            'brokerId' => (int)$result->brokerage->id,
            'officeName' => (string)$result->name,
            'streetAddress' => (string)$result->address->street_address,
            'extendedAddress' => (string)$result->address->extended_address,
            'locality' => (string)$result->address->locality,
            'regionCode' => (string)$result->address->region,
            'postalCode' => (string)$result->address->postal_code,
            'countryCode' => (string)$result->address->country_code,
            'phone' => (string)$result->phone,
            'fax' => (string)$result->fax,
            'timezone' => (string)$result->time_zone,
            'latitude' => (string)$result->address->latitude,
            'longitude' => (string)$result->address->longitude,
            'isMain' => (int)$result->main,
            'officeUrl' => (string)$result->url,
            'updated_at' => (string)$result->updated_at->get(Zend_Date::ISO_8601),
            'officeStatus' => (int)1,
            'lastModifiedDate' => (string)$now->get(Zend_Date::ISO_8601)
        );

        if($row = $table->fetchRow($table->select()->where('officeId = ?', $data['officeId']))) {
            $row->setFromArray($data);
        } else {
            $row = $table->createRow($data);
        }
        if(!$row->save()) {
            echo '<h1 class="error">Error attempting to save ' . tohtmlentities($data['officeId'] . ': ' . $data['officeName']) . ' to `tevoOffices`</h1>' . PHP_EOL;
        } else {
            echo '<h1>Saved ' . tohtmlentities($data['officeId'] . ': ' . $data['officeName']) . ' to `tevoOffices`</h1>' . PHP_EOL;
        }
        unset($data);
        unset($row);

        if(isset($result->email[0])) {
            // Set a list of emails we can append to
            $emailList = (string)'';
            
            // Loop through the emails and add them to the `tevoOfficeEmails` table
            foreach($result->email as $email) {
                $data = array(
                    'officeId' => (int)$result->id,
                    'email' => strtolower((string)$email),
                    'officeEmailStatus' => (int)1,
                    'lastModifiedDate' => (string)$now->get(Zend_Date::ISO_8601)
                );
    
                if($row = $eTable->fetchRow($eTable->select()->where("`officeId` = ?", (int)$result->id)->where("`officeEmailStatus` = ?", (int)0)->where("`email` = ?", (string)$email))) {
                    $row->setFromArray($data);
                } else {
                    $row = $eTable->createRow($data);
                }
                $row->save();
                unset($row);
                $emailArray[] = (string)$email;
                $emailList .= (string)$email . ', ';
                unset($data);
            } // End loop through emails for this office
            echo '<p>Saved ' . substr($emailList, 0, -2) . ' to `tevoOfficeEmails` for this office</p>' . PHP_EOL;
            unset($emailList);
            
            // Now set `officeEmailStatus` = 0 for any `tevoOfficeEmails` entries for 
            // this office that are not in $emailList. This will set any emails that 
            // were attached to this office but are no longer to false/inactive
            if(isset($emailList)) {
                $data = array(
                    'officeEmailStatus' => (int)0,
                    'lastModifiedDate' => (string)$now->get(Zend_Date::ISO_8601));
                $where = $eTable->getAdapter()->quoteInto("`officeId` = ?", $result->id);
                $where .= $eTable->getAdapter()->quoteInto(" AND `officeEmailStatus` = (?)", (int)1);
                $where .= $eTable->getAdapter()->quoteInto(" AND `email` NOT IN (?)", $emailArray);
                $epTable->update($where);
                unset($emailArray);
            }
        }
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