<?php

/*
|--------------------------------------------------------------------------
| Ticket Evolution Config
|--------------------------------------------------------------------------
|
| This file is for storing the credentials for the Ticket Evolution API.
|
| You should publish this configuration to your config directory using
|   php artisan vendor:publish
|
| It is recommended that you store your credentials in your .env file
| that is not committed to your repo and load them here using env().
|
*/

return [
    'baseUrl'    => env('TICKETEVOLUTION_API_BASEURL') ?: 'https://api.ticketevolution.com/v9',
    'apiVersion' => env('TICKETEVOLUTION_API_VERSION') ?: 'v9',
    'apiToken'   => env('TICKETEVOLUTION_API_TOKEN'),
    'apiSecret'  => env('TICKETEVOLUTION_API_SECRET'),
];
