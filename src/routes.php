<?php
// TODO: remove in production mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//==============================================================================
// Mapping of routes to functions
//==============================================================================
$app->get('/report/getByLoc', 'getReportByLoc');

require __DIR__ . '/../src/mongodb.php';
require __DIR__ . '/../src/util.php';

//==============================================================================
// getReportByLoc ()
//==============================================================================
function getReportByLoc($request, $response, $args) {
    $params = $request->getParams();
    $required_params = array('longitude', 'latitude', 'max_distance', 'timestamp', 'past_window');
    if (!allParamsDefined($required_params, $params)) {
        return responseWithCodeMessage($response, 400, 'Not all required parameters are defined');
    }

    $longitude = (string)$params['longitude'];
    $latitude = (string)$params['latitude'];
    $timestamp = (string)$params['timestamp'];
    $past_window = (string)$params['past_window'];
    $max_distance = (string)$params['max_distance'];

    $result = array();
    if (getReportsFromDB($longitude, $latitude, $max_distance, $timestamp, $past_window, $result)) {
        $result = json_encode($result);
        $response = $response->withHeader('Content-type', 'application/json');
        return responseWithCodeMessage($response, 200, $result);
    } else  {
        return responseWithCodeMessage($response, 500, 'Could not retrieve from db');
    }
}
