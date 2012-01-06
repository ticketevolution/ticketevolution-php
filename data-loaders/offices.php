<?php

require_once 'bootstrap.php';
error_reporting (E_ALL);
ini_set('max_execution_time', 2400);

// Set some status data for use in querying/updating the `tevoDataLoaderStatus` table
$statusData = array(
    'table' => 'offices',
    'type'  => 'active',
);

require_once './includes/common.php';

// Create the TicketEvolution_Db_Table object
$table = new TicketEvolution_Db_Table_Offices();

// Create an object for the `tevoOfficeEmails` table too
$emailsTable = new TicketEvolution_Db_Table_OfficeEmails();

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
        $results = $tevo->listOffices($options);
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
            'officeId'          => (int)    $result->id,
            'brokerId'          => (int)    $result->brokerage->id,
            'officeName'        => (string) $result->name,
            'phone'             => (string) $result->phone,
            'fax'               => (string) $result->fax,
            'timezone'          => (string) $result->time_zone,
            'isMain'            => (int)    $result->main,
            'officeUrl'         => (string) $result->url,
            'updated_at'        => (string) $result->updated_at->get(TicketEvolution_Date::ISO_8601),
            'officeStatus'      => (int)    1,
            'lastModifiedDate'  => (string) $startTime->get(TicketEvolution_Date::ISO_8601)
        );
        if (isset($result->address)) {
            $data['streetAddress']      = (string) $result->address->street_address;
            $data['extendedAddress']    = (string) $result->address->extended_address;
            $data['locality']           = (string) $result->address->locality;
            $data['regionCode']         = (string) $result->address->region;
            $data['postalCode']         = (string) $result->address->postal_code;
            $data['countryCode']        = (string) $result->address->country_code;
            $data['latitude']           = (string) $result->address->latitude;
            $data['longitude']          = (string) $result->address->longitude;
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
               . htmlentities('Error attempting to ' . $action . ' ' . $result->id . ': ' . $result->name . ' to `tevoOffices`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
        } else {
            echo '<h1>'
               . htmlentities('Successful ' . $action . ' of ' . $result->id . ': ' . $result->name . ' to `tevoOffices`', ENT_QUOTES, 'UTF-8', false)
               . '</h1>' . PHP_EOL
            ;
        }
        unset($action);
        unset($data);
        unset($row);

        if (isset($result->email[0])) {
            // Initialize an array of emails
            $emailArray = array();

            // Loop through the emails and add them to the `tevoOfficeEmails` table
            foreach ($result->email as $email) {
                $data = array(
                    'officeId'          => (int)    $result->id,
                    'email'             => strtolower((string)$email),
                    'officeEmailStatus' => (int)    1,
                    'lastModifiedDate'  => (string) $startTime->get(TicketEvolution_Date::ISO_8601)
                );

                if ($row = $emailsTable->fetchRow($emailsTable->select()->where("`officeId` = ?", $data['officeId'])->where("`officeEmailStatus` = ?", (int) 0)->where("`email` = ?", $data['email']))) {
                    $row->setFromArray($data);
                } else {
                    $row = $emailsTable->createRow($data);
                }
                $row->save();
                unset($row);
                $emailArray[] = $data['email'];
                unset($data);
            } // End loop through emails for this office
            echo '<p>Saved ' . implode(', ', $emailArray) . ' to `tevoOfficeEmails` for this office</p>' . PHP_EOL;

            // Now set `officeEmailStatus` = 0 for any `tevoOfficeEmails` entries for
            // this office that are not in $emailArray. This will set any emails that
            // were attached to this office but are no longer to false/inactive
            if (isset($emailArray)) {
                $data = array(
                    'officeEmailStatus' => (int)    0,
                    'lastModifiedDate'  => (string) $startTime->get(TicketEvolution_Date::ISO_8601),
                );
                $where = $emailsTable->getAdapter()->quoteInto("`officeId` = ?", $result->id);
                $where .= $emailsTable->getAdapter()->quoteInto(" AND `officeEmailStatus` = ?", (int) 1);
                $where .= $emailsTable->getAdapter()->quoteInto(" AND `email` NOT IN (?)", $emailArray);
                $emailsTable->update($data, $where);
                unset($data);
                unset($where);
                unset($emailArray);
            }
        }
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
