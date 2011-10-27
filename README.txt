Welcome to the Ticket Evolution Framework for PHP!

RELEASE INFORMATION
---------------
Ticket Evolution Framework for PHP.

Oct 27, 2011
- Fixed issue #30 with createShipment()

Oct 18, 2011
- Added automatic retry capability (up to 3 attempts) to Data Loaders in case of timeout

Sept 20, 2011 Act the Third
- Added payment type and some important documentation to createOrderCustomer() in the demo app.

Sept 20, 2011 Act II
- Added name to credit card in createCreditCard case in demo app
- Added some extra comments for createCreditCard case in demo app based upon new information from the API developers
- Fixed constant name used in Webservice.php when using listClientCreditCards() in demo app

Sept 20, 2011
Removed stats measurements from data-loaders.

Sept 13, 2011
Fixed some more capitalization issues.

Sept 9, 2011
** This update contains changes that are NOT backwards comaptible **
- Added https API URL support (Switch now. http will be turned off soon)
- Added new Client Credit Card methods (Not all are active yet)
- Renamed some files to adjust capitalization
    library/TicketEvolution/Db/Table/Ticketgroups.php -> library/TicketEvolution/Db/Table/TicketGroups.php
    library/TicketEvolution/Db/Table/Ticketgroupseats.php -> library/TicketEvolution/Db/Table/TicketGroupSeats.php
    library/TicketEvolution/Db/Table/Officeemails.php -> library/TicketEvolution/Db/Table/OfficeEmails.php
    library/TicketEvolution/Db/Table/Eventperformers.php -> library/TicketEvolution/Db/Table/EventPerformers.php
    library/TicketEvolution/Db/Table/Dataloaderstatus.php -> library/TicketEvolution/Db/Table/DataLoaderStatus.php

- Renamed some classes to adjust capitalization (Same as above files)
    TicketEvolution_Db_Table_Ticketgroups -> TicketEvolution_Db_Table_TicketGroups
    TicketEvolution_Db_Table_Ticketgroupseats -> TicketEvolution_Db_Table_TicketGroupSeats
    TicketEvolution_Db_Table_Officeemails -> TicketEvolution_Db_Table_OfficeEmails
    TicketEvolution_Db_Table_Eventperformers -> TicketEvolution_Db_Table_EventPerformers
    TicketEvolution_Db_Table_Dataloaderstatus -> TicketEvolution_Db_Table_DataLoaderStatus

- Renamed some methods in Webservice.php to adjust capitalization
    listTicketgroups() -> listTicketGroups()
    showTicketgroup() -> showTicketGroup()
    listEvopaytransactions() -> listEvoPayTransactions()
    showEvopaytransactions() -> showEvoPayTransactions()

- Removed @Version from DocBlocks


Aug 30, 2011 Part III: Return of the RedEye
- Cleaned up/corrected some more documentation in Webservice.php
- Moved TicketEvolution_ClientAddress to TicketEvolution_Address_Client
- Moved TicketEvolution_ClientEmailAddress to TicketEvolution_EmailAddress_Client
- Moved TicketEvolution_ClientPhoneNumber to TicketEvolution_PhoneNumber_Client

Aug 30, 2011 Part Deux
- Corrected some documentation. You can create multiple client addresses/emails/phone numbers
  in a single API call
- Corrected demo app to show example of creating two client addresses at once

Aug 30, 2011
This is the big update you've been waiting for.
- Added new API features through v8 including client and order methods
- Much improved "Demo App"
  - Now shows exact PHP code used
  - Method selector re-ordered to match API documentation
  - Methods that affect data are disabled if you do not use the Sandbox API
- Much easier to override how the data is returned from API calls. Specify your
  own ResultSet and Result classes if you like as well as and "Post-Processing"
  of the JSON returned
- Code now passes Zend CodeSniffer tests with no errors

July 25, 2011
Fixed a bug in buildRawSignature() (in Webservices.php) in which parameter names
were not being urlencoded (only the values were) which resulted in 401 Unauthorized
errors when attempting to listEvents() using 'performances[performer_id]='.

Also cleaned up some of the code in Webservices.php to make it pass CodeSniffer
using the Zend sniffs.

July 5, 2011
Bug fixes.
In the configuration data-loader the venueId column was being populated with
the configurationId.
ResultSet was returning all results as TicketEvolution_Searchresults objects
instead of teh ones specififc to their type of result.

May 22, 2011
Added ability to store seating chart urls in the tevoConfigurations table. In
order to do so you will need to run the update_Tevo_tables.mysql script in
the scripts folder.


May 20, 2011
Added data-loaders which is a handy app for caching the data locally in MySQL.
To use it you will need to run the create_tables.mysql script in scripts folder.

Added sortResults() method to ResultSet to allow for sorting of results.

Added exclusiveResults() and excludeResults() methods to resultSet which allow
you to easily show only TicketGroups from a specified array of brokers OR to
exclude TicketGroups from a specified array of brokers. Handy if you prefer to
only show your own inventory for certain events or if you wish to exclude
someone's inventory.

Added "search" endpoint which searches venues and performers

Many general improvements

May 17, 2011
Updated endpoint in Webservice.php from "ticket-groups" to "ticket_groups"
to reflect change in API.


May 9, 2011.
Initial Release

NEW FEATURES
------------

* First release of all components, contributed by J Cobb and Jeff Churchill
  of Team One Tickets & Sports Tours, Inc.


SYSTEM REQUIREMENTS
-------------------

Ticket Evolution Framework for PHP requires PHP 5.3 or later and Zend Framework.
Please see our reference guide for more detailed system requirements:

https://github.com/ticketevolution/ticketevolution-php/wiki

INSTALLATION
------------

Please see INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at
https://github.com/ticketevolution/ticketevolution-php/wiki

LICENSE
-------

The files in this archive are released under the new BSD license.
You can find a copy of this license in LICENSE.txt.
