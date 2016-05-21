<?php
/****************************************************************\
## FileName: db.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the database class.
## DB Class for integration with redis and mysql
## Copyright 2016 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class DB extends Config
{    
    var $queryStatement;
    var $queryHash;
    var $queryCount;
    var $mysqli;
    var $queryResults;

    public function __construct()
    {
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
        
        $this->queryStatement = $query;
        
        // Check for query type.
        if (substr($query, 0, 11) == "INSERT INTO") {
            // Insert statement
            $this->insert();
        } elseif (substr($query, 0, 6) == "UPDATE") {
            // Update statement
            $this->update();
        } elseif (substr($query, 0, 6) == "SELECT") {
            //Select statement        
            // setting up the query hash
            $this->hashQuery();
            
            if($cacheOverride == false) {
                // check to see if redis has the data.
                $redisOutput = $this->selectRedis();
                // return the data.
                $this->queryResults = $redisOutput['results'];
            } else {
                // We are overriding the ability to get to the cache, so we will query mysql directly.
                $this->queryResults = $this->selectQuery();
            }
        } else {
            die("Query type is unknown in database class.");
        }
    }
    
    private function insert()
    {
        // Open up a connection to the database.
        $this->dbConnect();
        
        try {
            // try to submit the query.
            $result = $this->mysqli->query($this->queryStatement);
            
        } catch (UpdateException $e) {
            // a failure happened.
            return "Error processing the insert function, ${e}";
        }
        
        // clean up the connection
        $this->dbDisconnect();
    }
    
    private function update()
    {
        // Open up a connection to the database.
        $this->dbConnect();
        
        try {
            $result = $this->mysqli->query($this->queryStatement);
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
    
    private function selectQuery($cache = false)
    {
        // Open up a connection to the database.
        $this->dbConnect();
        
        // Query the mysql database.
        $result = $this->mysqli->query($this->queryStatement);
        
        $rows = $result->fetch_assoc();
        
        // clean up the connection
        $this->dbDisconnect();
        
        return $rows;
    }
    
    private function selectRedis()
    {
        // first thing, connect to redis
        $obj_cluster = new RedisCluster(NULL, $this->redisNodes);
        
        // check to see if the hash exists.
        $redisData = $obj_cluster->get($this->queryHash);
        
        if ($redisData == FALSE || (isset($redisData['expire']) && time() > $redisData['expire'])) {
            // no data was available, or it was expired, so we need to readd/add the data to redis, then print it back out.
            $data = $this->selectQuery();
            
            // Add it to redis.
            if ($this->insertIntoRedis($data) == FALSE) {
                // cycle through it again and attempt one more redo of the insert.
                $value = $this->insertIntoRedis($data);
            }
            
            // return the data
            return array('results' => $data);
        } else {
            // There is redis data, give it back.
            return array('results' => $redisData['results']);
        }
    }
    
    // Insert data into redis.
    // We use the hashed value of the query to store the data in redis.
    private function insertIntoRedis($results)
    {
        $current = time();
        $expire = time()+60;
        
        // Create a cluster setting two nodes as seeds
        $obj_cluster = new RedisCluster(NULL, $this->redisNodes);
        
        $result = $obj_cluster->set($this->queryHash, array('timestamp' => $current, 'expire' => $expire, 'results' => $results));
        
        return $result;
    }
    
    private function hashQuery()
    {
        $this->queryHash = md5($this>queryStatement);
    }
}