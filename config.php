<?php
/****************************************************************\
## FileName: config.php									 
## Author: Brad Riemann										 
## Usage: Includes the config class with any request to avoice
## direct linking of a class to a central script.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

if(isset($_SERVER['HTTP_CF_VISITOR'])){
    $decoded = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
    if($decoded['scheme'] == 'http'){
        // http requests
        $port = 80;
    } else {
        $port = 443;
    }
} else {
    $port = $_SERVER['SERVER_PORT'];
}

// Check to make sure they always use the SSL side of the force.
if($port == '80')
{
	header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit;
}
include("includes/config.class.php");