<?php

return [
    'live'            => env('PESAPAL_LIVE', true),
    'consumer_key'    => env('PESAPAL_CONSUMER_KEY'),
    'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),
    'ipn_id'          => env('PESAPAL_IPN_ID'),
    'base_url'        => env('PESAPAL_LIVE', true)
        ? 'https://pay.pesapal.com/v3'
        : 'https://cybqa.pesapal.com/pesapalv3',
];
