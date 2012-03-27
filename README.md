# Welcome to the Ticket Evolution Framework for PHP!

## RELEASE INFORMATION

### March 27, 2012 v1.9.3
*This update contains changes that are NOT backwards compatible*

- Huge improvements in both speed and memory usage. See [Issue #46](https://github.com/ticketevolution/ticketevolution-php/issues/46) for some details.
- Also added some new filters that can be used to filter ticket groups when displaying a list of them. Look for updates in [the Wiki](https://github.com/ticketevolution/ticketevolution-php/wiki) soon explaining how to use these.

### Feb 29, 2012
*This update contains changes that are NOT backwards compatible*

- Updated things to work with API v9. API versions <9 will not work with this release
- Both data-loaders and the demo now require the user to copy `config.sample.php` to `config.php` and set your credentials there

### Feb 28, 2012
- Added a database searching method "getByParameters()" to DB/Table/Abstract

### Feb 9, 2012
- Improvements to API demo app:
    - If an exception is thrown the actual request and response will be output for debugging purposes
    - Made it easier to switch between API environments (Sandbox|Production)
    - Changes reflect new changes to API
- In the data-loaders change the `$lastUpdated` timezone to UTC before using it with `updated_at.gte` to account for a bug in the API that currently ignores the datetime portion if it is not specified as UTC.

### Jan 5, 2012
- Added support for new “deleted” endpoints:
    - `/events/deleted`
    - `/performers/deleted`
    - `/venues/deleted`
    - `/categories/deleted`
- Added support for using persistent connections, which now default to on. I have seen a considerable speedup with data loaders using persistent connections.
- No longer appends `?` to end of URL for API calls that have no parameters. The API used to require it be there regardless, but now it does not.
- Removed methods for checking if API URL was https now that https is required
- Markdown-ified the README

#### Data Loaders
- Can now pass `?startPage=xx` to URLs to start at a specific page of the loop
- Support and scripts for new `deleted` endpoints
- Be sure to run the newest SQL update script in `scripts` first

### Oct 27, 2011
- Fixed issue #30 with `createShipment()` method

### Oct 18, 2011
- Added automatic retry capability (up to 3 attempts) to Data Loaders in case of timeout

### Sept 20, 2011
- Added payment type and some important documentation to `createOrderCustomer()` in the demo app.
- Added name to credit card in `createCreditCard()` case in demo app
- Added some extra comments for `createCreditCard()` case in demo app based upon new information from the API developers
- Fixed constant name used in `Webservice.php` when using `listClientCreditCards()` in demo app
- Removed stats measurements from data-loaders.

### Sept 13, 2011
- Fixed some more capitalization issues.

### Sept 9, 2011
*This update contains changes that are NOT backwards compatible*
- Added https API URL support (Switch now. http will be turned off soon)
- Added new Client Credit Card methods (Not all are active yet)
- Renamed some files to adjust capitalization
    - `library/TicketEvolution/Db/Table/Ticketgroups.php` -> `library/TicketEvolution/Db/Table/TicketGroups.php`
    - `library/TicketEvolution/Db/Table/Ticketgroupseats.php` -> `library/TicketEvolution/Db/Table/TicketGroupSeats.php`
    - `library/TicketEvolution/Db/Table/Officeemails.php` -> `library/TicketEvolution/Db/Table/OfficeEmails.php`
    - `library/TicketEvolution/Db/Table/Eventperformers.php` -> `library/TicketEvolution/Db/Table/EventPerformers.php`
    - `library/TicketEvolution/Db/Table/Dataloaderstatus.php` -> `library/TicketEvolution/Db/Table/DataLoaderStatus.php`
- Renamed some classes to adjust capitalization (Same as above files)
    - `TicketEvolution_Db_Table_Ticketgroups` -> `TicketEvolution_Db_Table_TicketGroups`
    - `TicketEvolution_Db_Table_Ticketgroupseats` -> `TicketEvolution_Db_Table_TicketGroupSeats`
    - `TicketEvolution_Db_Table_Officeemails` -> `TicketEvolution_Db_Table_OfficeEmails`
    - `TicketEvolution_Db_Table_Eventperformers` -> `TicketEvolution_Db_Table_EventPerformers`
    - `TicketEvolution_Db_Table_Dataloaderstatus` -> `TicketEvolution_Db_Table_DataLoaderStatus`
- Renamed some methods in `Webservice.php` to adjust capitalization
    - `listTicketgroups()` -> `listTicketGroups()`
    - `showTicketgroup()` -> `showTicketGroup()`
    - `listEvopaytransactions()` -> `listEvoPayTransactions()`
    - `showEvopaytransactions()` -> `showEvoPayTransactions()`
- Removed `@version` from DocBlocks


### Aug 30, 2011
- Cleaned up/corrected some more documentation in `Webservice.php`
- Moved `TicketEvolution_ClientAddress` to `TicketEvolution_Address_Client`
- Moved `TicketEvolution_ClientEmailAddress` to `TicketEvolution_EmailAddress_Client`
- Moved `TicketEvolution_ClientPhoneNumber` to `TicketEvolution_PhoneNumber_Client`
- Corrected some documentation. You can create multiple client addresses/emails/phone numbers in a single API call
- Corrected demo app to show example of creating two client addresses at once
- Added new API features through v8 including client and order methods
- Much improved "Demo App"
    - Now shows exact PHP code used
    - Method selector re-ordered to match API documentation
    - Methods that affect data are disabled if you do not use the Sandbox API
- Much easier to override how the data is returned from API calls. Specify your own ResultSet and Result classes if you like as well as and "Post-Processing" of the JSON returned
- Code now passes Zend CodeSniffer tests with no errors

### July 25, 2011
- Fixed a bug in `buildRawSignature()` (in `Webservices.php`) in which parameter names were not being urlencoded (only the values were) which resulted in `401 Unauthorized` errors when attempting to `listEvents()` using `'performances[performer_id]='`.
- Cleaned up some of the code in `Webservices.php` to make it pass CodeSniffer using the Zend sniffs.

### July 5, 2011
*Bug fixes*
- In the configuration data-loader the `venueId` column was being populated with the `configurationId`.
- `ResultSet` was returning all results as `TicketEvolution_Searchresults` objects instead of the ones specific to their type of result.

### May 22, 2011
- Added ability to store seating chart URls in the `tevoConfigurations` table. In order to do so you will need to run the `update_Tevo_tables.mysql` script in the scripts folder.


### May 20, 2011
- Added data-loaders which is a handy app for caching the data locally in MySQL. To use it you will need to run the `create_tables.mysql` script in scripts folder.
- Added `sortResults()` method to `ResultSet` to allow for sorting of results.
- Added `exclusiveResults()` and `excludeResults()` methods to `ResultSet` which allow you to easily show only TicketGroups from a specified array of brokers OR to exclude TicketGroups from a specified array of brokers. Handy if you prefer to only show your own inventory for certain events or if you wish to exclude someone's inventory.
- Added `search()` endpoint which searches venues and performers
- Many general improvements

### May 17, 2011
- Updated endpoint in `Webservice.php` from `ticket-groups` to `ticket_groups` to reflect change in API.


### May 9, 2011.
Initial Release

----

## SYSTEM REQUIREMENTS
Ticket Evolution Framework for PHP *requires PHP 5.3 or later* and Zend Framework. If you are using PHP 5.2 you should realize that it is no longer supported by The PHP Group and you should update your server or switch to a host that uses current, secure versions.

Please see [the wiki](https://github.com/ticketevolution/ticketevolution-php/wiki) for more detailed system requirements:


## INSTALLATION
Please see `INSTALL.txt`.

## QUESTIONS AND FEEDBACK
Online documentation can be found at
https://github.com/ticketevolution/ticketevolution-php/wiki

## LICENSE
The files in this archive are released under the new BSD license.
You can find a copy of this license in `LICENSE.txt`.
