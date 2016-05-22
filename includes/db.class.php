<?php
/****************************************************************\
## FileName: db.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the database class.
## DB Class for integration with redis and mysql
## Copyright 2016 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class DB
{
    var $queryHash;
    var $queryCount;
    var $mysqli;
    var $queryResults;
    var $dbhost, $dbuser, $dbpass, $dbname, $redisNodes;

    public function __construct($databaseInformation)
    {
        // build out the database values.
        $this->dbhost = $databaseInformation['host'];
        $this->dbuser = $databaseInformation['user'];
        $this->dbpass = $databaseInformation['pass'];
        $this->dbname = $databaseInformation['db'];
        $this->redisNodes = $databaseInformation['redisNodes'];
    }
    
    private function dbConnect()
    {
        // define the mysqli connection, we will keep this open for each page that the database class is pulled from.
        $mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $this->mysqli = $mysqli;
        if ($this->mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
            exit;
        }
    }
    
    private function dbDisconnect()
    {
        $this->mysqli->close();
    }
    
    public function query($query, $cacheOverride = false)
    {
        // First, check to see if redis has the data we are looking for.
        // Second, if redis does not exist we get the data from MySQL
        // Third, verify if we need to perform an update to the data, the redis data should have a timestamp, expiration and results.
        // each query will "expire" one hour after it was submitted. Now, we CAN override this functionality by setting the cache override.
        // we will want certain functions that are administrative in nature to be always on. Cached queries for regular members is fine.
        
        // Check for query type.
        if (substr($query, 0, 11) == "INSERT INTO") {
            // Insert statement
            $this->insert($query);
        } elseif (substr($query, 0, 6) == "UPDATE") {
            // Update statement
            $this->update($query);
        } elseif (substr($query, 0, 6) == "SELECT") {
            //Select statement
            if($cacheOverride == false) {
                // check to see if redis has the data.
                $redisOutput = $this->selectRedis($query);
                // return the data.
                $this->queryResults = $redisOutput['results'];
            } else {
                // We are overriding the ability to get to the cache, so we will query mysql directly.
                $this->queryResults = $this->selectQuery($query);
            }
        } else {
            die("Query type is unknown in database class.");
        }
    }
    
    private function insert($query)
    {
        // Open up a connection to the database.
        $this->dbConnect();
        
        try {
            // try to submit the query.
            $result = $this->mysqli->query($query);
            
        } catch (UpdateException $e) {
            // a failure happened.
            return "Error processing the insert function, ${e}";
        }
        
        // clean up the connection
        $this->dbDisconnect();
    }
    
    private function update($query)
    {
        // Open up a connection to the database.
        $this->dbConnect();
        
        try {
            $result = $this->mysqli->query($query);
        } catch (UpdateException $e) {
            return "Error processing the update function, ${e}";
        }
        
        // clean up the connection
        $this->dbDisconnect();        
    }
    
    public function results()
    {
        return $this->queryResults;
    }
    
    private function selectQuery($query,$cache = false)
    {
        // Open up a connection to the database.
        $this->dbConnect();
        
        // Query the mysql database.
        $result = $this->mysqli->query($query);
        
        $data = array();
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // clean up the connection
        $this->dbDisconnect();
        
        return $data;
    }
    
    private function selectRedis($query)
    {
        // first thing, connect to redis
        $obj_cluster = new RedisCluster(NULL, $this->redisNodes);
        
        // build the query hash
        $hash = $this->hashQuery($query);
        
        // check to see if the hash exists.
        $redisData = $obj_cluster->get($hash);
        
        if ($redisData == FALSE || (isset($redisData['expire']) && time() > $redisData['expire'])) {
            echo '<!-- mysql hit, Query: ' . $query . ', Hash: ' . $hash . ' -->';
            // no data was available, or it was expired, so we need to readd/add the data to redis, then print it back out.
            $data = $this->selectQuery($query);
            
            // Add it to redis.
            $redisInsert = $this->insertIntoRedis($data,$hash);
            
            if ($redisInsert == FALSE) {
                // cycle through it again and attempt one more redo of the insert.
                $value = $this->insertIntoRedis($data);
            }
            
            // return the data
            return array('results' => $data);
        } else {
            // There is redis data, give it back.
            // first, decode the json to an array
            $data = json_decode($redisData, true);
            echo '<!-- redis hit, Hash: ' . $hash . ' -->';
            // return the data.
            return array('results' => $data['results']);
        }
        unset($hash);
    }
    
    // Insert data into redis.
    // We use the hashed value of the query to store the data in redis.
    private function insertIntoRedis($results,$hash)
    {
        $current = time();
        $expire = time()+60;
        
        // Create a cluster setting two nodes as seeds
        $obj_cluster = new RedisCluster(NULL, $this->redisNodes);
        
        $result = $obj_cluster->set($hash, json_encode(array('timestamp' => $current, 'expire' => $expire, 'results' => $results)));
        
        return $result;
    }
    
    private function hashQuery($query)
    {
        return md5($query);
    }
    
    public function escape($data)
    {
        // this is a manual way to escape strings.. while we want a connection to do this, we also want to keep the same hash across redis so that its not ever changing.
        $replacements = array(
            "\x00"=>'\x00',
            "\n"=>'\n',
            "\r"=>'\r',
            "\\"=>'\\\\',
            "'"=>"\'",
            '"'=>'\"',
            "\x1a"=>'\x1a'
        );
        
        // escapes the input.
        return strtr($data,$replacements);;
    }
}