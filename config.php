<?php
/****************************************************************\
## FileName: config.php									 
## Author: Brad Riemann										 
## Usage: Includes the config class with any request to avoice
## direct linking of a class to a central script.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

// Check to make sure they always use the SSL side of the force.
if($_SERVER['SERVER_PORT'] == '80')
{
	header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit;
}
include("includes/config.class.php");