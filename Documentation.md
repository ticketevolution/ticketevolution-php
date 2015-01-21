# Ticket Evolution PHP Client v2

## Introduction
The Ticket Evolution PHP Client is an open source framework created to simplify working with the [Ticket Evolution API web service](http://api.ticketevolution.com/). We created it to enable you to quickly implement the Ticket Evolution API web service on your own site whether you are creating a new site or switching from a different provider. We released it as open source so that you could make changes and improvements as you see fit. When you do we hope that you will give these changes back to the community by contributing them back to the project.

## Installation
The Ticket Evolution Framework for PHP requires PHP 5.3.3 We recommend the most current release of PHP for critical security and performance enhancements.

The recommended way to install is through [Composer](https://getcomposer.org/).

    # Install Composer
    curl -sS https://getcomposer.org/installer | php

    # Add TicketEvolution as a dependency
    php composer.phar require ticketevolution/ticketevolution-php:~2.1

After installing, you need to require Composer's autoloader:

    require '../vendor/autoload.php';

## Notes On Switching From A Different Data or XML Feed Provider
The Ticket Evolution API web service will look familiar to anyone who has previously worked with a different data/XML feed provider before, but there will be some obvious differences as well.

### Switching From Event Inventory’s XML Feed
If you are switching from Event Inventory’s XML feed there are a few differences in data structure and naming you will want to familiarize yourself with. The first has to do with naming of resources.

<table>
<tr><th>Ticket Evolution Name</th><th>Event Inventory Name</th></tr>
<tr><td>Performer</td><td>Event</td></tr>
<tr><td>Event</td><td>Production</td></tr>
<tr><td>Venue</td><td>Venue</td></tr>
</table>

Another major difference is that an Event Inventory `Production` relates to one or two Events via their unique IDs. The headliner or home team is referred to via the `EventID` for a Production, while an opening act or away team is referred to via the `OpponentEventID`.

Ticket Evolution has the ability to attach an infinite number of Performers to an Event. (Imagine having the entire lineup of Coachella performers being attached to the Coachella event, or all 8 teams of a March basketball tournament being attached to the single event.)

As such, Ticket Evolution’s Events may return a list of Performers for a single Event. Home teams or headliners are denoted by having the `is_primary` value set to true.

## Ticketevolution\Webservice
The `Ticketevolution\Webservice` is a simple client for using Ticket Evolution’s API web service. It has been designed to make utilizing the web service extremely simple.

`Ticketevolution\Webservice` enables developers to retrieve information from all endpoints of the Ticket Evolution API. 

Examples include:

* Brokerage information such as
    * Brokerages
    * Offices
    * Users
* Catalog Resources such as
    * Categories
    * Events
    * Performers
    * Venues
    * Venue Configurations
* Inventory information such as
    * Ticket Groups
    * Orders
    * Quotes
* EvoPay information such as
    * Accounts
    * Transactions

As the API grows to include even more options, `Ticketevolution_Webservice` will be updated to include all new options.

In order to use `Ticketevolution\Webservice`, you should already have a Ticket Evolution *API token* as well as a *secret key*. To get a key and for more information, please visit the [API Credentials section](https://settings.ticketevolution.com/brokerage/credentials) of the Ticket Evolution Settings app.

>#### Attention
>Your Ticket Evolution API token and secret keys are linked to your Ticket Evolution identity, so take appropriate measures to keep them private. 

### Example #1 Search For A Performer

In this example, we search for performers with ‘Chris’ in the name.

    $cfg['params']['apiToken'] = (string) 'TEVO_API_TOKEN';
    $cfg['params']['secretKey'] = (string) 'TEVO_SECRET_KEY';
    
    $tevo = new Ticketevolution\Webservice($cfg['params']);
    
    $options = array('page' => 1,
                     'per_page' => 10);
    
    $results = $tevo->searchPerformers('Chris', $options);
    
    foreach ($results as $performer) {
        echo $performer->name . '<br />' . PHP_EOL;
    }
> Note:
>Note: You may instantiate the `Ticketevolution\Webservice` object by passing an associative array of options as shown above or by passing a `Zend_Config` object. 

### Types of Methods
Currently there are three main types of methods in `Ticketevolution\Webservice`: `List`, `Search` and `Show`. You will find that each method name is a combination of the method type and the resource you are querying such as `listBrokers()`, `searchEvents()` and `showVenue()`.

Refer to the [Ticket Evolution API documentation](http://developer.ticketevolution.com/documentation) to see what possible key/values are returned for each operation.

Any results which include a date or datetime will return that value as a `Ticketevolution\Date` object which extends the standard [`Zend_Date`](http://framework.zend.com/manual/en/zend.date.html) object, making it very simple to display or compare dates and times in any format.

#### list*() Methods
`List*()` methods generally return multiple results based upon the specific method and return results based upon the parameters supplied. See the [Ticket Evolution API documentation]([http://api.ticketevolution.com/)](http://developer.ticketevolution.com/documentation) to see what parameters are valid for each method.

##### Example #2 List Venues
In this example, we return a list of venues. We return 25 venues starting with the 51st venue available.

    $tevo = new Ticketevolution\Webservice($cfg['params']);
    
    $options = array('page' => 3,
                     'per_page' => 25);
    
    $results = $tevo->listVenues($options);
    
    foreach ($results as $venue) {
        echo $venue->name . '<br />' . PHP_EOL;
    }

#### search*() Methods
`search*()` methods are used to search for a result matching the query string and may return zero to many results. Not all resources that have `list*()` or `show*()` methods have a `search()` method. See the [Ticket Evolution API documentation](http://api.ticketevolution.com/) to see which resources allow searching.

#####Example #3 Search For Events
In this example, we search for any events with ‘World Series’ in the name.

    $tevo = new Ticketevolution\Webservice($cfg['params']);
    
    $options = array('page' => (int)1,
                     'per_page' => (int)10);
    
    $results = $tevo->searchEvents('World Series', $options);
    
    foreach ($results as $event) {
        echo $event->name . '<br />' . PHP_EOL;
    }

#### show*() Methods
`show*()` methods are used to display a single item based upon its unique ID. `show*()` methods do not return multiple items and have no required or optional options.

##### Example #4 Show a Performer by ID
In this example, we retrieve a single performer (Rolling Stones) based upon their unique ID (9688).

    $tevo = new Ticketevolution\Webservice($cfg['params']);
    
    $results = $tevo->showPerformer(9688);
    
    foreach ($results as $performer) {
        echo $performer->name . '<br />' . PHP_EOL;
    }

#####Example #5 Exception Handling
As of version 2.1.0 `Exception` handling has been improved to return different types of `Exception`s depending on what the problem is. in **ANY** place where you make an API call you should be catching and handling `Exception`s. Here’s an example:

    $tevo = new Ticketevolution\Webservice($cfg['params']);
    
    $options = array(
        'page' => (int) 1,
        'per_page' => (int) 10
    );
    
    try {
        $results = $tevo->searchEvents('World Series', $options);
    } catch(\TicketEvolution\ApiInvalidRequestException $e) {
        // Invalid parameters were supplied

        // Put something here to gracefully handle this situation

    } catch (\TicketEvolution\ApiAuthenticationException $e) {
        // Authentication with the API failed

        // Put something here to gracefully handle this situation

    } catch (\TicketEvolution\ApiConnectionException $e) {
        // Network communication failed

        // Put something here to gracefully handle this situation

    } catch (\TicketEvolution\ApiException $e) {
        // Something went wrong with the request

        // Put something here to gracefully handle this situation

    } catch (\Exception $e) {
        // Something else happened, probably unrelated to TicketEvolution

        // Put something here to gracefully handle this situation

    }

    
    foreach ($results as $event) {
        echo $event->name . '<br />' . PHP_EOL;
    }


## Sorting Results
If you wish to sort your results after you have retrieved them you want to use the `sortResults()` method of `TicketEvolution\Webservice\ResultSet`. You can sort the results on multiple properties such as by section, then by row then by price.

### Example #1 Sort ticket_groups results

    $tevo = new TicketEvolution\Webservice($cfg['params']);

    $options = array('event_id' => 136957);

    $results = $tevo->listTicketgroups($options);

    $sortOptions = array(
        'section', // Defaults to SORT_ASC if neither is specified
        'row' => SORT_DESC,
        'retail_price' => SORT_ASC
    );
    $results->sortResults($sortOptions);

## Removing Query Results
There are two methods available for removing results. Both `excludeResults()` and `exclusiveResults()` take an array defining what you wish to remove or keep. `excludeResults()` can be used to remove **ticket_groups** from specific brokers or offices while `exclusiveResults()` will remove all results except the specified brokerages or offices. `exclusiveResults()` is very handy if you are heavily stocked for a specific event and you wish to only show your own inventory or perhaps yours plus that of another broker or two.

Using these methods completely removes those results from your `ResultSet`. If you wish to just use a portion of the `ResultSet` without losing the others look below for how to filter your results (added in v1.9.3).



### Example #2 Remove ticket_groups based on brokerage_id

    $results = $tevo->listTicketgroups($options);

    $excludeBrokerages = array(
        389,
        691,
        117
    );
    $results->excludeResults($excludeBrokerages, 'brokerage');




### Example #3 Keep only ticket_groups from two specific offices based on office_id

    $results = $tevo->listTicketgroups($options);

    $exclusiveBrokerages = array(
        223,
        154
    );
    $results->exclusiveResults($exclusiveBrokerages, 'office');


## Filtering Query Results (v1.9.3+)
In some instances you may wish to filter your `ResultSet`. Unlike, `excludeResults()` and `exclusiveResults()` above filtering your results does not remove the results.

One example of when you might wish to filter your results instead of `excludeResults()` might be when listing `ticket_groups` you might wish to first show only those `ticket_groups` marked as `featured`, and then later show the `ticket_groups` that are *not* featured.

### Example #4 Show *only* `ticket_groups` that are `featured` then ones that are not

    $results = $tevo->listTicketgroups($options);

    $featuredResults = new TicketEvolution\Webservice\ResultSet\Filter\TicketGroups\Featured($results, true);

    foreach ($featuredResult as $ticketGroup) {
        // Display your FEATURED results here
    }

    $nonFeaturedResults = new TicketEvolution\Webservice\ResultSet\Filter\TicketGroups\Featured($results, false);

    foreach ($nonFeaturedResults as $ticketGroup) {
        // Display your NON-FEATURED results here
    }


These filters are also “stackable” allowing you to combine multiple filters at once.


### Example #5 Show only `ticket_groups` that are `in_hand` and `eticket`

    $results = $tevo->listTicketgroups($options);


    $inHandResults = new TicketEvolution\Webservice\ResultSet\Filter\TicketGroups\InHand($results, true);
    $inHandEticketResults = new TicketEvolution\Webservice\ResultSet\Filter\TicketGroups\ETicket($inHandResults, true);

    foreach ($inHandEticketResults as $ticketGroup) {
        // Display your IN HAND, ETICKET results here
    }

If you are using PHP >=5.4 you can create you own complex filters on the fly using [CallbackFilterIterator](http://us.php.net/manual/en/class.callbackfilteriterator.php).


## TicketEvolution\DateTime
`TicketEvolution\DateTime` is an extension of PHP's [`DateTime`](http://php.net/manual/en/class.datetime.php). In addition to all the awesome date handling and comparison features of `DateTime` we have added a few handy constants and most importantly, the ability to easily handle displaying of dates for events which do not have a time and are considered “TBA.”

Upon instantiating a `TicketEvolution\DateTime` object the time is checked for known “TBA” times such as '23:59:59' and '23:59:20'. If the time is considered to be TBA then it is set to '00:00:00', the time that Ticket Evolution uses to indicate TBA.

Additionally, `TicketEvolution\DateTime` via its extension of `DateTime` makes it very easy to display dates and times in nearly any format. An additional method in `TicketEvolution\DateTime`, `formatTbaSafe()` will ensure that times that are considered TBA do not get displayed as '00:00:00'. Instead, “TBA” will be returned as the time portion of your requested date/time format.

### Example #1 formatTbaSafe() - Output a “TBA Safe” Date
In this example we will output the date of a TBA event.

    // An example Date & Time that might be considered TBA in another system
    $eventDate = '2011-09-09 23:59:59';
    
    $date = new TicketEvolution\DateTime($eventDate);
    
    // Output a “TBA safe” date formatted for readability
    print $date->formatTbaSafe(TicketEvolution\DateTime::DATETIME_FULL_NOTZ);
    
    // Friday, September 9, 2011 TBA