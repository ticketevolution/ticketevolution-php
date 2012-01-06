<?php

require_once 'bootstrap.php';
error_reporting (E_ALL);
ini_set('max_execution_time', 2400);

// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array(
    'table' => 'categories',
    'type'  => 'active',
);

require_once './includes/common.php';

// Create the TicketEvolution_Db_Table object
$table = new TicketEvolution_Db_Table_Categories();

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
        $results = $tevo->listCategories($options);
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
            'categoryId'        => (int)    $result->id,
            'categoryName'      => (string) $result->name,
            'categoryUrl'       => (string) $result->url,
            'updated_at'        => (string) $result->updated_at->get(TicketEvolution_Date::ISO_8601),
            'categoryStatus'    => (int)    1,
            'lastModifiedDate'  => (string) $startTime->get(TicketEvolution_Date::ISO_8601),
        );
        if (isset($result->parent->id)) {
            $data['parentCategoryId'] = (int) $result->parent->id;
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
               . htmlentities('Error attempting to ' . $action . ' ' . $result->id . ': ' . $result->name . ' to `tevoCategories`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
        } else {
            echo '<h1>'
               . htmlentities('Successful ' . $action . ' of ' . $result->id . ': ' . $result->name . ' to `tevoCategories`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
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
$statusData['lastRun'] = (string) $startTime->get(Zend_Date::ISO_8601);
if (isset($statusRow)) {
    $statusRow->setFromArray($statusData);
} else {
    $statusRow = $statusTable->createRow($statusData);
}
$statusRow->save();


echo '<h1>Finished updating `tevo' . $statusData['table'] . '` table</h1>' . PHP_EOL;
