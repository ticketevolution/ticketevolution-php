# Changelog

## 4.3.0 (March 9, 2022)
- Add new `completeShipment()` and `updateShipment()` methods that are most often used when fulfilling mobile transfer sales.

## 4.2.10 (December 16, 2021)
- Add new `createTaxQuote()` method in preparation of the coming-soon [Tax Quotes / Create](https://ticketevolution.atlassian.net/wiki/spaces/API/pages/2900459521/Tax+Quotes) endpoint.

## 4.2.9 (December 8, 2021)
- Add new `listings()` method to use the new faster and more efficient [Listings / Index](https://ticketevolution.atlassian.net/wiki/spaces/API/pages/2853797930/Listings+Index) endpoint.
- Add new `showListing()` method to use the new faster and more efficient [Listings / Show](https://ticketevolution.atlassian.net/wiki/spaces/API/pages/2895052808/Listings+Show) endpoint.

## 4.2.8 (November 29, 2021)
- Add new `deleteClientEmailAddress()` method.

## 4.2.7 (September 20, 2021)
- Add new `updateOrder()` method.

## 4.2.6 (September 9, 2021)
- Do not include `min_and_max_price` in `listEventsDeleted()`.

## 4.2.5 (September 7, 2021)
- Allow PHP 8.0

## 4.2.4 (September 7, 2021)
- Support `GuzzleHttp\Psr7` 2.0 in addition to 1.x.

## 4.2.3 (April 22, 2020)
- Allow usage of `symfony/config` ^5.0 in addition to ^4.2. Fixes the inability to install alongside newer versions of Laravel.

## 4.2.2 (November 4, 2019)
- Ensure that when `min_and_max_price` is used with `listEvents()` it is sent as a boolean-string because the comparison in the API requires `true` and not just a truthy value.

## 4.2.1 (April 22, 2019)
- Added `deleteOfficeCreditCard()`.

## 4.2.0 (March 31, 2019)
- Replace `gimler/guzzle-description-loader` with local version. This resolves the conflict with Symfony files that was preventing this project from being used with Laravel 5.8.

## 4.1.3 (March 29, 2019)
- Correct location of `recipients` parameter for `emailAirbillForShipment()`.
- Fix `rejection_notes` parameter for `rejectOrder()`.
- Make `q` parameter optional for `searchEvents()`.

## 4.1.2 (February 11, 2019)
- Ensure `include_tevo_section_mappings` is sent as a string when used with `listTicketGroups()`. (Thanks to @zimm0r)

## 4.1.1 (January 2, 2019)
- Fix an issue with deploying [/jwcobb/tevo-harvester](https://github.com/jwcobb/tevo-harvester) by removing typehints in `__constructor()` of `TEvoAuthMiddleware`.

## 4.1.0 (February 20, 2018)
- Added `/v10/orders` endpoint for `createOrders()`.

## 4.0.5 (February 20, 2018)
- Expanded acceptable `states` for `listOrders()`.

## 4.0.4 (June 19, 2017)
- Added `uploadAirbillForShipment()` for uploading a base-64 encoded PDF airbill for a `ProvidedAirbill` shipment.
- Correct documentation for adding a logger middleware under Advanced Usage.

## 4.0.3 (June 9, 2017)
- Use `PUT` for `setTicketProperties()`.

## 4.0.2 (June 9, 2017)
- Allow `string` or `int` `barcode` for `setTicketProperties()`.

## 4.0.1 (June 9, 2017)
- Fix validation of value `type` where the value is formatted as `boolean-string` because of [change made in `guzzle/guzzle-services` in 1.1.0](https://github.com/guzzle/guzzle-services/pull/130/commits/13f2abc948901a8f108c5bf4daafeb2a137b853b).
- Support Laravel 5.5 [Package Auto-Discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518).

## 4.0.0 (May 24, 2017)
- Fix issue with parameters for `listCategories`.

## 4.0.0RC1 (March 20, 2017)
- Upgrade to [Guzzle 6.2](https://github.com/guzzle/guzzle).
- Create new `TEvoAuthMiddleware` to handle API authentication since the old Subscriber method no longer works.

## 3.0.8 (October 19, 2016)
- Correct the allowed `type`s for `listTicketGroups()`.

## 3.0.7 (October 19, 2016)
- Allow strings to be used in addition to integers for `showPerformer()` and `showVenue()` so that the slugs may be used.

## 3.0.6 (August 17, 2016)
- Update `search()` to use a comma-separated string for the `types` value instead of an array now that the corresponding endpoint allows that.

## 3.0.5 (June 24, 2016)
- Update `listSearchSuggestions()` to use a comma-separated string for the `entities` value instead of an array now that the corresponding endpoint allows that. This fixes #130.

## 3.0.4 (March 26, 2016)
- Update `bindShared()` to `singleton()` in `TEvoServiceProvider` because `bindShared()` was deprecated in Laravel 5.1 and removed in Laravel 5.2.
- Update installation instructions for Laravel.

## 3.0.3 (October 14, 2015)
- Correct HTTP method for `cancelShipment`.
- Correct `composer require` statements in some [Documentation](https://github.com/thephpleague/skeleton/blob/master/Documentation.md) examples.

## 3.0.2 (September 8, 2015)
- Make sure to not send `Expect: 100` header.

## 3.0.1 (September 8, 2015)
- Fixed an issue in the `TEvoAuth` subscriber that caused uploads > 1MB to fail.
- Fixed an issue with the `httpMethod` specified for some shipments-related resources.
- Updated Documentation.

## 3.0.0 (April 30, 2015)
- No changes from 3.0.0-rc.1

## 3.0.0-rc.1 (January 21, 2015)
- Complete rewrite breaking all backwards compatibility

## 2.2.2 (January 21, 2015)
- Moved Wiki information to [Documentation.md](https://github.com/thephpleague/skeleton/blob/master/Documentation.md)
- Updated README.md to include info about upcoming 3.0 release
- Moved LICENSE.txt to LICENSE.md

## 2.2.1 (November 2, 2014)
- Improved code formatting
- Updated docblock `@link`s to related API documentation for all methods
- Changed `searchClients()` to use `/searches/suggestions` endpoint
- Removed `updateShipment()` and `updateOrder()`
- Added methods: `cancelShipment()`, `generateAirbill()`, `getAirbill()`, `emailAirbill()`, `getShipmentSugestion()`, `createQuotes()`

## 2.2.0 (November 14, 2014)
- Close #113 by extending `Zend_Rest_Client` and `Zend_Http_Client` to add the ability to set the proper `Content-Type` header.
- Close #111 by adding the `ticketsSetProperties()` method.

## 2.1.5 (November 14, 2014)
- Close #110 by adding a `$options` parameter to `showTicketGroup()`.
- Close #112 by renaming `rejection_reason` to `reason` and adding support for `rejection_notes`.

## 2.1.4 (August 1, 2014)
- Add `printEtickets()` method for retrieving etickets for an order. Thanks to @tapmodo.

## 2.1.3 (June 4, 2014)
- Account for a bug in some API responses where `total_entries` is being returned as `NULL`, thus causing `Webservice.php` to not return a `ResultSet` as is expected.

## 2.1.2 (January 28, 2014)
- Added the ability to time the API call & response using getElapsedTime(). This only times the actual call & response and does not include any of the setup time.

## 2.1.1 (January 21, 2014)
- Added searchEvents() for performing [full text search of events](https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9470096)
- Added createOrdersFromJson() for [creating orders](https://ticketevolution.atlassian.net/wiki/pages/viewpage.action?pageId=9994275) from raw JSON
- Removed @author attributions
- Updated @copyright dates

## 2.1 (September 20, 2013)
- Added some more specific Exception classes to make it easier to catch different types
- Moved classes specific to the [DataLoader project](https://github.com/jwcobb/ticketevolution-php-dataloaders) to that project.
- Cleaned up some inline documentation in `Webservice.php`
- Reduced redundant code in most methods in `Webservice.php`
- The copyright has been transferred from [Team One Tickets & Sports Tours, Inc](http://www.teamonetickets.com) to [Ticket Evolution, Inc.](http://www.ticketevolution.com/). A copyright transfer agreement is on file with Ticket Evolution, Inc.


## 2.0.6 (August 9, 2013)
- Exception thrown in `TicketEvolution\Webservice::_cleanAndValidateCreditCardNumber()` with invalid card numbers was not obvious. Removed card validation as the API will return a message about invalid card numbers.


## 2.0.5 (June 25, 2013)
- Fixed bug (#99) with DataLoaders where some values would not be properly deleted on update.


## 2.0.4 (June 24, 2013)
- Fixed bug with TicketGroup filters
- Added new TicketGroup Filters
 - `TicketEvolution\Webservice\ResultSet\Filter\TicketGroups\ViewType`
 - `TicketEvolution\Webservice\ResultSet\Filter\TicketGroups\Wheelchair`


## 2.0.3 (June 12, 2013)
- Made some small improvements to DataLoader classes that make extending them easier.
- Moved this list from README.md to CHANGELOG.md


## 2.0.2 (May 28, 2013)
- Remove reference to `owned_by_office` in `TicketEvolution\DataLoaders\Events` because that isn't a field that should be cached.


## 2.0.1 (May 21, 2013)
- Fixed some column names in the DataLoader classes that did not match the column names as specified in the [DataLoader project](https://github.com/TeamOneTickets/ticket-evolution-dataloaders)


## 2.0.0 (May 17, 2013)
*This update contains changes that are NOT backwards compatible*

- The Demo has been split out to a [separate repository](https://github.com/TeamOneTickets/ticket-evolution-php-library-demo).
- The DataLoaders have been split out to a [separate repository](https://github.com/TeamOneTickets/ticket-evolution-dataloaders).
- Added [Composer](http://getcomposer.org/) support.
- Added more proper [namespace](http://php.net/manual/en/language.namespaces.rationale.php) support
- Require autoloading by removing all `require_once` statements for requiring classes
- Removed some methods that were previously deprecated


## 1.9.14 (December 10, 2012)
- Squashed bugs in the deprecated methods `createClientCompany()`, `createClientAddress()`, `createClientPhoneNumber()`, `createClientEmailAddress()`, `createClientCreditCard()`, and `createOrder()` thanks to a report from tonyguo2010


## 1.9.13 (October 24, 2012)
- The parameters to list events by the primary performer has changed from `primary_performer_id` to instead use `performer_id` and add the boolean `primary_performer`. Updated the Demo app to match.


## 1.9.12 (October 15, 2012)
- Fixed bug (#83) with `createOrders()`


## 1.9.11 (October 8, 2012)
- Fixed bug (#81) with `createClientEmailAddresses()`


## 1.9.10 (September 27, 2012)
- Remover leftover debug


## 1.9.9 (September 27, 2012)
- Fixes #76 by correcting typo in `TicketEvolution/Db/Table/Users.php`
- Fixes a couple bugs in `/scripts/update_Tevo_tables-2012-09-26.mysql`
- Improves handling of `showMemory` and `showProgress` options for DataLoaders
- Better handling of an oddity in results from `/categories` endpoints when no results are returned


## 1.9.8 (September 26, 2012)
- Fixes missing table name in `/scripts/update_Tevo_tables-2012-09-26.mysql` Thanks to @kylegato for reporting the bug.


## 1.9.7 (September 26, 2012)
*This release has a bug in `/scripts/update_Tevo_tables-2012-09-26.mysql`. Use 1.9.8 instead.*

*This update contains changes that are NOT backwards compatible*

*Be sure to apply `/scripts/update_Tevo_tables-2012-09-26.mysql` to update your tables as necessary*

- Full rewrite of the Demo app
- Full rewrite of the Data Loaders for caching the API data locally. They are now classes, which means you can easily extend them to add additional features or tweaks as necessary.
- Deprecated some methods in `TicketEvolution_Webservice`. Deprecated methods will trigger an `E_USER_DEPRECATED` error. This was done in order to make the naming more consistent with TEvo naming and with the functionality of the methods.
- Added some new methods for new API features such as Companies and shipment settings.
- Changed some column names for clarity.


## 1.9.6 (June 8, 2012)
- Fixed bug in `TicketEvolution_Webservice::createOrder()` where an array should have been used. Thanks to @aaronwp for finding the bug.


## 1.9.5 (May 22, 2012)
- Added support for new [/companies endpoint](http://developer.ticketevolution.com/endpoints/companies)


## 1.9.4 (April 26, 2012)
- Use `deleted_at` instead of `updated_at` in the data-loaders for deleted endpoints


## 1.9.3 (March 27, 2012)
*This update contains changes that are NOT backwards compatible*

- Huge improvements in both speed and memory usage. See [Issue #46](https://github.com/ticketevolution/ticketevolution-php/issues/46) for some details.
    - Dates are no longer returned as `Zend_Date` objects
    - Currencies are no longer converted to `Zend_Currency` objects
- Also added some new filters that can be used to filter ticket groups when displaying a list of them. Look for updates in [the Wiki](https://github.com/ticketevolution/ticketevolution-php/wiki) soon explaining how to use these.


## Feb 29, 2012
*This update contains changes that are NOT backwards compatible*

- Updated things to work with API v9. API versions <9 will not work with this release
- Both data-loaders and the demo now require the user to copy `config.sample.php` to `config.php` and set your credentials there


## Feb 28, 2012
- Added a database searching method "getByParameters()" to DB/Table/Abstract


## Feb 9, 2012
- Improvements to API demo app:
    - If an exception is thrown the actual request and response will be output for debugging purposes
    - Made it easier to switch between API environments (Sandbox|Production)
    - Changes reflect new changes to API
- In the data-loaders change the `$lastUpdated` timezone to UTC before using it with `updated_at.gte` to account for a bug in the API that currently ignores the datetime portion if it is not specified as UTC.


## Jan 5, 2012
- Added support for new “deleted” endpoints:
    - `/events/deleted`
    - `/performers/deleted`
    - `/venues/deleted`
    - `/categories/deleted`
- Added support for using persistent connections, which now default to on. I have seen a considerable speedup with data loaders using persistent connections.
- No longer appends `?` to end of URL for API calls that have no parameters. The API used to require it be there regardless, but now it does not.
- Removed methods for checking if API URL was https now that https is required
- Markdown-ified the README


### Data Loaders
- Can now pass `?startPage=xx` to URLs to start at a specific page of the loop
- Support and scripts for new `deleted` endpoints
- Be sure to run the newest SQL update script in `scripts` first


## Oct 27, 2011
- Fixed issue #30 with `createShipment()` method


## Oct 18, 2011
- Added automatic retry capability (up to 3 attempts) to Data Loaders in case of timeout


## Sept 20, 2011
- Added payment type and some important documentation to `createOrderCustomer()` in the demo app.
- Added name to credit card in `createCreditCard()` case in demo app
- Added some extra comments for `createCreditCard()` case in demo app based upon new information from the API developers
- Fixed constant name used in `Webservice.php` when using `listClientCreditCards()` in demo app
- Removed stats measurements from data-loaders.


## Sept 13, 2011
- Fixed some more capitalization issues.


## Sept 9, 2011
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



## Aug 30, 2011
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


## July 25, 2011
- Fixed a bug in `buildRawSignature()` (in `Webservices.php`) in which parameter names were not being urlencoded (only the values were) which resulted in `401 Unauthorized` errors when attempting to `listEvents()` using `'performances[performer_id]='`.
- Cleaned up some of the code in `Webservices.php` to make it pass CodeSniffer using the Zend sniffs.


## July 5, 2011
*Bug fixes*
- In the configuration data-loader the `venueId` column was being populated with the `configurationId`.
- `ResultSet` was returning all results as `TicketEvolution_Searchresults` objects instead of the ones specific to their type of result.


## May 22, 2011
- Added ability to store seating chart URls in the `tevoConfigurations` table. In order to do so you will need to run the `update_Tevo_tables.mysql` script in the scripts folder.


## May 20, 2011
- Added data-loaders which is a handy app for caching the data locally in MySQL. To use it you will need to run the `create_tables.mysql` script in scripts folder.
- Added `sortResults()` method to `ResultSet` to allow for sorting of results.
- Added `exclusiveResults()` and `excludeResults()` methods to `ResultSet` which allow you to easily show only TicketGroups from a specified array of brokers OR to exclude TicketGroups from a specified array of brokers. Handy if you prefer to only show your own inventory for certain events or if you wish to exclude someone's inventory.
- Added `search()` endpoint which searches venues and performers
- Many general improvements


## May 17, 2011
- Updated endpoint in `Webservice.php` from `ticket-groups` to `ticket_groups` to reflect change in API.


## May 9, 2011.
Initial Release
