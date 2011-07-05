Welcome to the Ticket Evolution Framework for PHP! 

RELEASE INFORMATION
---------------
Ticket Evolution Framework for PHP.

July 5, 2011
Bug fixes.
In the configuration data-loader the venueId column was being populated with 
the configurationId.
ResultSet was returning all results as Ticketevolution_Searchresults objects
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

Ticket Evolution Framework for PHP requires PHP 5.2.4 or later and Zend Framework.
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
