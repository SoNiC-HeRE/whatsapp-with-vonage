<?php

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/utils.php');

use GuzzleHttp\Client;

return function ($context) {
    throw_if_missing($_ENV, [
        'VONAGE_API_KEY',
        'VONAGE_API_SECRET',
        'VONAGE_API_SIGNATURE_SECRET',
        'VONAGE_WHATSAPP_NUMBER',
    ]);

    if ($context->req->method === 'GET') {
        return $context->res->send(get_static_file('index.html'), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
    $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];
    
    $data = [
        'from' => $YOUR_VONAGE_WHATSAPP_NUMBER,
        'to' => $RECIPIENT_NUMBER,
        'message_type' => 'text',
        'text' => 'check',
        'channel' => 'whatsapp'
    ];
    
    $url = 'https://messages-sandbox.nexmo.com/v1/messages';
    
    $client = new Client();
    
    try {
        $response = $client->post($url, [
            'headers' => $headers,
            'auth' => [$API_KEY, $API_SECRET],
            'json' => $data
        ]);
    
        // Print the response
        echo $response->getBody();
    } catch (\Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
};
