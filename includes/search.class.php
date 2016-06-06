<?php
/****************************************************************\
## FileName: search.class.php
## Author: Brad Riemann
## Usage: Integrates with searching functionality on the site.
## Version: 6.0
## Copyright 2016 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Search extends Config
{
    
    private $db;
    
    public function __construct()
    {
        parent::__construct();
        
        // initialize the database connection, we won't actually use it till we call it up.
        include_once('db.class.php');
        $this->db = new DB($this->dbConnectionInfo());  
    }
    
    public function searchBar()
    {
        /*
        image - seriesimages/1335.jpg
        link - /anime/deadman-wonderland/
        name - deadman wonderland
        byline - 24 episodes, finished. Aired from 01.12.14-04.12.14.
        */
        if (!isset($_POST['q']) || $_POST['q'] == '') {
            // no values for the query, throw them back to the fish.
            echo '<div align"center">Please enter something to search for!</div>';
        } else {
            
            $searchString = $_POST['q'];
            
            if ($this->UserArray['Level_access'] == 1 || $this->UserArray['Level_access'] == 2) {
                // This is a management user.
                $this->db->query("SELECT `value0`, `value1`, `value2`, `value3`, `value4`, `value5`, `value6`, `value7` FROM ((SELECT 'user' AS `value0`, `Username` AS `value1`, `Active` AS `value2`, `Level_access` AS `value3`, `ID` AS `value4`, `avatarActivate` AS `value5`, `avatarExtension` AS `value6`, 'NULL' AS `value7` FROM `users` WHERE (`Username` LIKE '" . $this->db->escape($searchString) . "%' OR `display_name` LIKE '" . $this->db->escape($searchString) . "%') ORDER BY `Username` ASC LIMIT 8) UNION ALL (SELECT 'series' AS `value0`, `fullSeriesName` AS `value1`, `active` AS `value2`, `seoname` AS `value3`, `id` AS `value4`, (SELECT COUNT(id) FROM `episode` WHERE `sid`=`series`.`id` AND `Movie` = 0) AS `value5`, `moviesOnly` AS `value6`, `stillRelease` AS `value7` FROM `series` WHERE `active` = 'yes' AND (`fullSeriesName` LIKE '" . $this->db->escape($searchString) . "%' OR `romaji` LIKE '" . $this->db->escape($searchString) . "%' OR `kanji` LIKE '" . $this->db->escape($searchString) . "%') ORDER BY `fullSeriesName` ASC LIMIT 8)) AS temp_table ORDER BY `value1` ASC LIMIT 8");
            } elseif ($this->UserArray['Level_access'] == 4 || $this->UserArray['Level_access'] == 4 || $this->UserArray['Level_access'] == 5 || $this->UserArray['Level_access'] == 6 || $this->UserArray['Level_access'] == 7) {
                // The rest of staff and AMs
            } elseif ($this->UserArray['Level_access'] == 3) {
                // Basic Members
            } else {
                // users not logged in.
                $this->db->query("SELECT `value0`, `value1`, `value2`, `value3`, `value4`, `value5`, `value6`, `value7` FROM ((SELECT 'user' AS `value0`, `Username` AS `value1`, `Active` AS `value2`, `Level_access` AS `value3`, `ID` AS `value4`, `avatarActivate` AS `value5`, `avatarExtension` AS `value6`, 'NULL' AS `value7` FROM `users` WHERE (`Username` LIKE '" . $this->db->escape($searchString) . "%' OR `display_name` LIKE '" . $this->db->escape($searchString) . "%') ORDER BY `Username` ASC LIMIT 8) UNION ALL (SELECT 'series' AS `value0`, `fullSeriesName` AS `value1`, `active` AS `value2`, `seoname` AS `value3`, `id` AS `value4`, (SELECT COUNT(id) FROM `episode` WHERE `sid`=`series`.`id` AND `Movie` = 0) AS `value5`, `moviesOnly` AS `value6`, `stillRelease` AS `value7` FROM `series` WHERE `active` = 'yes' AND (`fullSeriesName` LIKE '" . $this->db->escape($searchString) . "%' OR `romaji` LIKE '" . $this->db->escape($searchString) . "%' OR `kanji` LIKE '" . $this->db->escape($searchString) . "%') ORDER BY `fullSeriesName` ASC LIMIT 8)) AS temp_table ORDER BY `value1` ASC LIMIT 8");
            }
            
            include_once('template.class.php');
            
            foreach ($this->db->results() as $key => &$row) {
                $searchRow = new Template("templates/search-row.tpl");
                
                $searchRow->set('image',$this->formatImage($row['value0'],$row['value4'],$row['value5'],$row['value6']));
                
                if ($row['value0'] == 'user' ) {
                    $searchRow->set('link','/user/' . $row['value1']);
                    $searchRow->set('name',$row['value1']);
                    $searchRow->set('byline','');
                } elseif ($row['value0'] == 'series') {
                    $searchRow->set('link','/anime/' . $row['value3'] . '/');
                    $searchRow->set('name',stripslashes($row['value1']));
                    
                    // is the series a regular series or movies only series.
                    if ($row['value6'] == 0) {
                        // episodes AND movies
                        $byLine = $row['value5'] . ' episodes, ';
                    } else {
                        // movies only
                        $byLine = 'Movie only Series, ';
                    }
                    
                    // Is the entry still releasing?
                    if ($row['value7'] == 'yes') {
                        $byLine .= 'still airing.';
                    } else {
                        $byLine .= 'finished.';
                    }
                    
                    $searchRow->set('byline',$byLine);
                } elseif ($row['value0'] == 'episode') {
                }
                echo $searchRow->output();
            }
        }
    }
    
    private function formatImage($type,$id,$active,$extension)
    {
        if ($type == 'user') {
            if ($active == 'yes') {
                return 'avatars/50x70/user' . $id . '.' . $extension;
            } else {
                return 'avatars/50x70/default.gif';
            }
        } elseif ($type == 'series') {
            return 'seriesimages/50x70/' . $id . '.jpg';
        } else {
        }
    }
}