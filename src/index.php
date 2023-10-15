<?php

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/utils.php');

return function ($context) {
    if ($context->req->method === 'GET') {
        // Send a response with the res object helpers
        // `res.send()` dispatches a string back to the client
        return $context->res->send('Checking');
    }
};
