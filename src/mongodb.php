<?php

// TODO: Move in separate file
Class Config {
    const mongo_host = 'localhost:27017';
    const mongo_dbname = 'reportdb';
    const mongo_user = 'user';
    const mongo_pass = 'pass';
    const report_col  = 'reports';
};

// TODO: Make class

//==============================================================================
// connectMongo ()
//==============================================================================
function connectMongo() {  // TODO: private
    try {
        $host = Config::mongo_host;
        $dbname = Config::mongo_dbname;
        $user = Config::mongo_user;
        $pass = Config::mongo_pass;
        // TODO: auth to mongo using user/pass
        //$url = "mongodb://$user:$pass@$host/$dbname";
        $url = "mongodb://$host/$dbname";
        $client = new MongoClient($url);
        $db = $client->$dbname;
        return $db;
    } catch (MongoException $e) {
        throw $e;
    }
}

//==============================================================================
// mongodbFind ()
//==============================================================================
function mongodbFind($collection, $query, &$cursor) {  // TODO: private
    try {
        $db = connectMongo();
    } catch (MongoException $e) {
        handleException($e);
        return false;
    }

    try {
        $cursor = $db->$collection->find($query)->limit(0);
        return true;
    } catch (MongoCursorException $e){
        handleException($e);
        return false;
    }
}

//==============================================================================
// mongodbFindOne ()
//==============================================================================
function mongodbFindOne($collection, $query, &$doc) {  // TODO: private
    try {
        $db = connectMongo();
    } catch (MongoException $e) {
        handleException($e);
        return false;
    }

    try {
        $doc = $db->$collection->findOne($query);
        return true;
    } catch (MongoCursorException $e){
        handleException($e);
        return false;
    }
}

//==============================================================================
// ensureIndexExistsInDB ()
//==============================================================================
function ensureIndexExistsInDB($collection, $field) {  // TODO: private
    try {
        $db = connectMongo();
    } catch (MongoException $e) {
        handleException($e);
        return false;
    }

    $db->$collection->ensureIndex(array($field => 1));
    return true;
}

//==============================================================================
// getReportsFromDB ()
//==============================================================================
function getReportsFromDB($longitude, $latitude, $max_distance, $timestamp, $past_window, &$result) {
    $collection = Config::report_col;
    $start_time = $timestamp - (60 * $past_window);  // past_window is in minutes
    $end_time = $timestamp;
    $query = array(
        'time' => array(
            '$gte' => (string)$start_time,
            '$lte' => (string)$end_time,
         ),
         'RealTF' => array('$ne' => 'false'),
    );
    $sort_filter = array("time" => -1);
    if (!mongodbFind($collection, $query, $cursor)) {
        return false;
    }

    $result = array();
    foreach ($cursor as $doc) {
        $dist = vincentyGreatCircleDistance($latitude, $longitude, $doc['reportLatitude'], $doc['reportLongitude']);
        if ($dist <= $max_distance){
            $report = array(
                'reportLatitude' => (float)$doc['reportLatitude'],
                'reportLongitude' => (float)$doc['reportLongitude'],
                'address' => $doc['address'],
                'type' => $doc['type'],
                'severity' => $doc['severity'],
                'time' => $doc['time'],
                'comments' => $doc['comments'],
                'reliability' => (1 - (float)$doc['ProbabilityRealTF']),
//                'userId' => $doc['userId'],
//                'userLatitude' => $doc['userLatitude'],
//                'userLongitude' => $doc['userLongitude'],
//                '_id' => $doc['_id'],
//                'accuracy' => $doc['accuracy'],
//                'heading' => $doc['heading'],
//                'speed' => $doc['speed'],
//                'mode' => $doc['mode'],
//                'ProbabilityRealTF' => $doc['ProbabilityRealTF'],
//                'ProbabilityTF' => $doc['ProbabilityTF'],
//                'RealTF' => $doc['RealTF'],
//                'TF' => $doc['TF'],
//                'judgements' => $doc['judgements'], // array() of arrays ('userId', 'button, 'time')
            );
            $result[] = $report;
        }
    }

    return true;
}
