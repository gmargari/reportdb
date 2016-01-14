<?php
// TODO: remove in production mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//==============================================================================
// Mapping of routes to functions
//==============================================================================
$app->get('/test', 'testFunc');

//==============================================================================
// testFunc ()
//==============================================================================
function testFunc($request, $response, $args) {
    $response->getBody()->write("OK");
    $response = $response->withStatus(200);
    return $response;
}

