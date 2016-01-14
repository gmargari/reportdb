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
function getReportsFromDB($longitude, $latitude, $max_distance, $start_time, $past_window, &$result) {
    //$past_window = $start_time - (60 * $minutes);
    $collection = Config::report_col;
    $query = array(
//        'time' => array('$gte' => (string)$past_window),
//        'RealTF' => array('$ne' => 'false')
    );
    $sort_filter = array("time" => -1);
    if (!mongodbFind($collection, $query, $cursor)) {
        return false;
    }

    $result = array();
    foreach ($cursor as $doc) {
        $dist = vincentyGreatCircleDistance($latitude, $longitude, $doc['reportLatitude'], $doc['reportLongitude']);
        if ($dist <= $max_distance){
            $result[] = $doc;
        }
    }

    return true;
}
