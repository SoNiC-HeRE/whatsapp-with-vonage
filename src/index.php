<?php

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/utils.php');


use \Firebase\JWT\JWT;

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

    try {
        throw_if_missing($body, ["from","text"]);
    } catch (Exception $e) {
        return $context->res->json([
            'ok'=> FALSE,
            'error'=> $e
        ], 400)
    }

    $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];
    
    $data = [
        'from' => $_ENV['VONAGE_WHATSAPP_NUMBER'],
        'to' => $context->req->body['from'],
        'message_type' => 'text',
        'text' => 'Hi there, you sent me: ' . $context->req->body['text'],
        'channel' => 'whatsapp'
    ];
    
    $url = 'https://messages-sandbox.nexmo.com/v1/messages';
    
    $ch = curl_init();
    
    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json'
    );
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERPWD, $_ENV['VONAGE_API_KEY'] . ':' . $_ENV['VONAGE_API_SECRET']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    try {
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        $context->log($response);
    } catch (Exception $e) {
        $context->error('Caught exception: ', $e->getMessage(), "\n");
    }
    
    curl_close($ch);
};
