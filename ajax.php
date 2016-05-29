<?php

include_once('config.php');

if (isset($_GET['action']) && $_GET['action'] == 'search') {
    include_once('includes/search.class.php');
    $Search = new Search();
    $Search->searchBar();
} else {
}
