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
    $data = [
        'from' => $_ENV['VONAGE_WHATSAPP_NUMBER'],
        'to' => $context->req->body['from'],
        'message_type' => 'text',
        'text' => 'Hi there! You sent me: ' . $context->req->body['text'],
        'channel' => 'whatsapp'
    ];

    $url = 'https://messages-sandbox.nexmo.com/v1/messages';

    $client = new Client();
    try {
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'auth' => [$_ENV['VONAGE_API_KEY'], $_ENV['VONAGE_API_SECRET']],
            'json' => $data
        ]);

        if ($response->getStatusCode() === 200) {
            return $context->res->json(['ok' => true]);
        } else {
            return $context->error("Error: Unexpected status code - " . $response->getStatusCode(), 500);
        }
    } catch (Exception $e) {
        return $context->error('Caught exception: ' . $e->getMessage() . "\n");
    }

    return $context->res->empty();
};
