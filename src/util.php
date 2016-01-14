<?php

//==============================================================================
// allParamsDefined ()
//==============================================================================
function allParamsDefined($required_params, $given_params) {
    return !array_diff($required_params, array_keys($given_params));
}

//==============================================================================
// responseWithCodeMessage ()
//==============================================================================
function responseWithCodeMessage($response, $code, $message) {
    $response->getBody()->write($message);
    $response = $response->withStatus($code);
    return $response;
}

//==============================================================================
// handleException ()
//==============================================================================
function handleException($e) {
    echo "\n";
    echo 'Exception in ' . $e->getFile() . ':' . $e->getLine() . ' : "' . $e->getMessage() . '"';
}

//==============================================================================
// vincentyGreatCircleDistance ()
//==============================================================================
function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
  //http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
       pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return ($angle * $earthRadius)/1000;
}
