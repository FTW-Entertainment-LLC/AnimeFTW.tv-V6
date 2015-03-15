<?php
/****************************************************************\
## FileName: config.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the configuration class.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Config {
	
	public $UserArray = array(), $PermArray, $ImageHost, $StatsDB, $MainDB, $PageColumns, $rootdir, $currentversion;

	public function __construct()
	{
		$this->rootdir = '';
		$this->currentversion = '6.0.0';
		$this->PageColumns = array("id", "name", "page_title", "seoname", "type", "template", "security");
		
		// Initialize the Database connection!
		$this->DB_Con();
		
		// constructs all of the details about the user.
		$this->array_constructUser(); 
		
		// this is for the usage of the CDN, all images will be there and if its secure we want to use it.
		if($_SERVER['SERVER_PORT'] == 443)
		{
			$this->ImageHost = 'https://d206m0dw9i4jjv.cloudfront.net';
		}
		else
		{
			$this->ImageHost = 'http://img02.animeftw.tv';
			//$this->ImageHost = 'http://d206m0dw9i4jjv.cloudfront.net';
		}
		// temp measure to keep people out..
		if(($this->UserArray['logged-in'] == 0) && $_SERVER['HTTP_HOST'] == 'dev.animeftw.tv')
		{
			echo '
			<script>
				window.location.replace("http://www.animeftw.tv/");
			</script>';
			exit;
		}		
	}
	
	#----------------------------------------------------------------
	# function DB_Con
	# Builds the database connection for use ONLY in the class
	# @private
	#----------------------------------------------------------------
	private function DB_Con()
	{
		
		if($_SERVER['HTTP_HOST'] == 'dev.animeftw.tv')
		{
			$this->StatsDB 	= 'mainaftw_stats'; // declare the stats DB
			$this->MainDB 	= 'devadmin_anime'; // Main DB for everything else
			// this will be for development connections only.
			$dbhost 		= '10.151.1.10';
			$dbuser 		= 'devadmin_anime';
			$dbpass 		= 'L=.zZ76[,TOqwf*&tl';
			$dbname 		= 'devadmin_anime';
		}
		else
		{
			$this->StatsDB 	= 'mainaftw_stats'; // declare the stats DB
			$this->MainDB 	= 'mainaftw_anime'; // Main DB for everything else
			$dbhost 		= '10.151.1.10';
			$dbuser 		= 'mainaftw_anime';
			$dbpass 		= '26V)YPh:|IJG';
			$dbname 		= 'mainaftw_anime';
		}
		
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		$this->mysqli = $mysqli;
		if ($this->mysqli->connect_errno)
		{
			echo "Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
		}
	}
	
	#----------------------------------------------------------------
	# @function array_constructUser
	# @usage: to build all of the configurable options for the users 
	# on the website.
	# @private
	#----------------------------------------------------------------
	private function array_constructUser()
	{
		// We need to check to see if the user logged in is through the website or the api
		if(isset($_GET['token']) || isset($_POST['token']))
		{
			// if the token is set, it will be an api request
			$Token = (isset($_POST['token']) ? $_POST['token'] : $_GET['token']);
			$query = "SELECT `uid` FROM `" . $this->MainDB . "`.`developers_api_sessions` WHERE `session_hash` = '" . $this->mysqli->real_escape_string($Token) . "' LIMIT 0, 1";
			$result = $this->mysqli->query($query) or die('Error : ' . $this->mysqli->error);
			$row = $result->fetch_assoc();
			$UserID = $row['uid'];
		}
		else
		{
			@session_start();
			if(isset($_COOKIE['cookie_id']) || (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == TRUE))
			{
				if(isset($_COOKIE['cookie_id']))
				{
					$UserID = $_COOKIE['cookie_id'];
				} 
				else if(isset($_SESSION['user_id']))
				{
					$UserID = $_SESSION['user_id']; 
				} 
			}
			else
			{
				$UserID = NULL;
			}
		}
		if($UserID != NULL)
		{
			$query = "SELECT * FROM users WHERE ID='" . $this->mysqli->real_escape_string($UserID) . "'";
			$result = $this->mysqli->query($query) or die('Error : ' . $this->mysqli->error);
			$row = $result->fetch_assoc();
			
			// check to see if the cookie or the session is set, if either one are, let them pass!
			if((isset($_COOKIE['authenticate']) && $_COOKIE['authenticate'] == md5($_SERVER['REMOTE_ADDR'] . $row['Password'] . $_SERVER['HTTP_USER_AGENT'])) || isset($_SESSION['user_id']) || isset($Token))
			{
				$this->UserArray['logged-in'] .= 1;
				foreach($row AS $key => $value)
				{
					$this->UserArray[$key] .= $value;
				}
				$this->UserArray['FancyUsername'] .= $this->string_fancyUsername(0,$row['Username'],$row['Active'],$row['Level_access'],$row['advancePreffix'],$row['advanceImage']);
				//they clear the authentication process...
				$this->mysqli->query('UPDATE `' . $this->MainDB . '`.`users` SET `lastActivity` = \''.time().'\' WHERE ID=\'' . $this->mysqli->real_escape_string($UserID) . '\'');
			}
			else 
			{
				// user is not logged in, let's reject everything.
				$this->UserArray['logged-in'] = 0;
				$this->UserArray['Level_access'] = 0;
			}
		}
		else 
		{
			// user is not logged in, let's reject everything...
				$this->UserArray['logged-in'] = 0;
				$this->UserArray['Level_access'] = 0;
		}
	}
	
	public function bool_validatePermission($pid)
	{
		// first, check to make sure the permission is numeric.
		if(is_numeric($pid))
		{
			/*
			# OID of 1, means it is a Group request
			# OID of 2, means it is a single user Request
			*/
			$query = "SELECT `id`, `deny` FROM `" . $this->MainDB . "`.`permissions_objects` WHERE `permission_id` = " . $pid . " AND ((`type` = 1 AND `oid` = ".$this->UserArray['ID'].") OR (`type` = 2 AND `oid` = ".$this->UserArray['Level_access']."))";
			$results = $this->mysqli->query($query);   
			$count = mysqli_num_rows($results);
			if($count > 0)
			{    
                $Deny = 0;
                while($row = $result->fetch_assoc())
                {
                    if($row['deny'] == 1)
                    {
                        $Deny = 1;
                    }
                }
                if($Deny == 1)
                {
                    // if it finds a 1 in the array, its because there is a deny somewhere..
                    return FALSE;
                }
                else
                {
                    // a deny option was not found in the system.. go ahead..
                    return TRUE;
                }
			}
			else 
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	public function string_fancyUsername($ID,$Username = NULL,$Active = NULL, $Level_access = NULL, $advancePreffix = NULL,$advanceImage = NULL,$UsernameOnly = NULL)
	{
		if($ID == 0)
		{
			// if the ID is 0, we need to let them use the supplied credentials
		}
		else
		{
			// ID is supplied, we need to give them the goods.
			$query = 'SELECT `Username`, `display_name`, `Active`, `Level_access`, `advancePreffix`, `advanceImage` FROM `' . $this->MainDB . '`.`users` WHERE `ID` = \'' . $this->mysqli->real_escape_string($ID) . '\'';
			$results = $this->mysqli->query($query);
			$row = $results->fetch_assoc();
			$Username = $row['Username'];
			$display_name = $row['display_name'];
			$Active = $row['Active'];
			$Level_access = $row['Level_access'];
			$advancePreffix = $row['advancePreffix'];
			$advanceImage = $row['advanceImage'];
		}
		
		// Added 8/10/2014 - robotman321
		// If the user has a custom Display_name, we make that the primary username
		if($display_name != $Username && $display_name != NULL)
		{
			// The display name has been setup, lets use that
		}
		else
		{
			$display_name = $Username;
		}
		
		// Added 8/5/2014 - robotman321
		// Enables the use of non link username construction.
		if($UsernameOnly != NULL)
		{
			$fixedUsername = $Username;
		}
		else
		{
			// ADDON:
			// Built so that users built within the Android App do not get redirected away from the app and stay in the app.
			if(stristr($_SERVER['HTTP_USER_AGENT'],'tv.animeftw.android/3.0') || stristr($_SERVER['REQUEST_URI'],'/m/'))
			{
				$link = '<a href="#" onClick="$(\'#content\').load(\'ajax.php?page=profile&username=' . $Username . '\'); return false;">';
			}
			else
			{
				$link = '<a href="/user/' . $Username . '">';
			}
			if($Active == 1)
			{ 
				if ($Level_access != 3)
				{
					if($advancePreffix != NULL || $advancePreffix != '')
					{
						$spanbefore = '<span style="">';
						$spanafter = '</span>';
					}
					else
					{
						$spanbefore = '';
						$spanafter = '';
					}
					if($Level_access == 1)
					{
						$fixedUsername = $spanbefore . '<img src="/images/admin-icon.png" alt="Admin of AnimeFTW.tv" title="AnimeFTW.tv Administrator" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
					}
					else if($Level_access == 2)
					{
						$fixedUsername = $spanbefore . '<img src="/images/manager-icon.png" alt="Group manager of AnimeFTW.tv" title="AnimeFTW.tv Staff Manager" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
					}
					else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
					{
						// /images/staff-icon.png
						$fixedUsername = $spanbefore . '<img src="/images/staff-icon.png" alt="Staff Member of AnimeFTW.tv" title="AnimeFTW.tv Staff Member" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
					}
					else if($Level_access == 7)
					{
						$fixedUsername = $spanbefore . '<img src="/images/advancedimages/' . $advanceImage . '.png" title="AnimeFTW.tv Advanced Member" alt="Advanced User Title" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
					}
					else
					{
						$fixedUsername = $spanbefore . $link . $display_name . '</a>' . $spanafter;
					}
				}
				else
				{
					$fixedUsername = $link . $display_name . '</a>';
				}
			}
			else
			{
				$fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $Username . '"><s>' . $display_name . '</s></a>';
			}
		}
		return $fixedUsername;
	}
	
	// takes a query and a var and retunrs 
	public function SingleVarQuery($query,$var)
	{
		$result = $this->mysqli->query($query) or die('Error : ' . mysql_error());
		$row = $result->fetch_assoc();
		return $row[$var];
	}
	
	// records the mod function right into the database.
	public function ModRecord($type)
	{
		$this->mysqli->query("INSERT INTO modlogs (uid, ip, agent, date, script, request_url) VALUES ('" . $this->UserArray[1] . "', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".time()."', '".$type."', '".mysql_real_escape_string($_SERVER['REQUEST_URI'])."')");
	}
	
	// we dont know what it does.. it just looks cool.
	public function Build($var1,$var2,$Type = NULL){
		$sarray = array( 
			'a' => '$2a$10$m5eebjaxijtnafbhqt863n$',
			'b' => '$2a$10$1rdche03z0y65yuirbx9j2$', 
			'c' => '$2a$10$w58kxl7rgj4h47rujjkgw2$', 
			'd' => '$2a$10$1mwo8ykqm89s4mgbq6eftg$', 
			'e' => '$2a$10$7opxsns435g60bitirv5g2$', 
			'f' => '$2a$10$i6qmrb5bd2j2y2evs8v4xr$',
			'g' => '$2a$10$tbzoqkirdj267u7lw6t64m$', 
			'h' => '$2a$10$dy40suy5eeg7rforo8b4bg$',
			'i' => '$2a$10$6fwfgsg30neqin81jzbs4a$', 
			'j' => '$2a$10$ac0y5ebdgt82v0hwzomdyr$', 
			'k' => '$2a$10$dn7xhvqunhv89wtxhfucpp$', 
			'l' => '$2a$10$2yaocsfe83lhva9hq132zp$', 
			'm' => '$2a$10$u2uxxmb0vujcd0w04dgyrv$', 
			'n' => '$2a$10$j7dh66ex6a2cu4v34jtdv7$', 
			'o' => '$2a$10$809qcxw7df2ror8355hwby$', 
			'p' => '$2a$10$wowii9akv7q5pee3eqtsiq$', 
			'q' => '$2a$10$aqhfns3hvo94hdsd6rd8xb$', 
			'r' => '$2a$10$chjsfo8w0k3pahal5jjukl$', 
			's' => '$2a$10$xhromb9gw55u84mew26iqm$', 
			't' => '$2a$10$zend8794gsmihxnvn4hr89$', 
			'u' => '$2a$10$83q8psnll2orz8gjibphqy$', 
			'v' => '$2a$10$8exwykcd97v3fbp26gqe3b$', 
			'w' => '$2a$10$cmueo47hk4rdpdozx6sb3r$', 
			'x' => '$2a$10$wqjavr92fq7kn1kh8tb27x$',
			'y' => '$2a$10$6rzgtbmuxpodbnfmgs3gk9$',
			'z' => '$2a$10$1kvphqm78zdqoeqmfuf6g3$',
			'0' => '$2a$10$xtjha3kw75l05y53kli9rc$', 
			'1' => '$2a$10$iloqaoeqpu4o47nmvv4cj6$',
			'2' => '$2a$10$ngiv7kq9nbro9xxqdwedup$',
			'3' => '$2a$10$5ikgle3duc5su9jk78j108$',
			'4' => '$2a$10$254b10z996dviqliffkng0$',
			'5' => '$2a$10$e04cbsiin8lwc8n20qw3id$',
			'6' => '$2a$10$u1vodloj7l2xtuy3c9hq4x$',
			'7' => '$2a$10$dypumh5ep81ndi3qkf41u2$',
			'8' => '$2a$10$bj939q6rjvgzfqzfct0tfq$',
			'9' => '$2a$10$yfy65x2fucihnce0722m9s$'
		);
		if($Type == 'md5')
		{
			$final = md5($var1);
		}
		else			
		{
			$var2 = substr(strtolower($var2), 0, 1);
			$final = crypt($var1, $sarray[$var2]);
		}
		return $final;
	}
	
	//Paging function for the management pages, version two
	public function pagingV1($DivID,$count,$perpage,$start,$link)
	{
		$num = $count;
		$per_page = $perpage; // Number of items to show per page
		$showeachside = 4; //  Number of items to show either side of selected page
		if(empty($start)){$start = 0;}  // Current start position
		else{$start = $start;}
		$max_pages = ceil($num / $per_page); // Number of pages
		$cur = ceil($start / $per_page)+1; // Current page number
		$front = "<span>$max_pages Pages</span>&nbsp;";
		if(($start-$per_page) >= 0){
			$next = $start-$per_page;
			$startpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($next>0?("&page=").$next:"") . '\');return false;">&lt;</a>';
		}
		else {$startpage = '';}
		if($start+$per_page<$num){
			$endpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.'&page='.max(0,$start+1) . '\');return false;">&gt;</a>';
		}
		else {
			$endpage = '';
		}
		$eitherside = ($showeachside * $per_page);
		if($start+1 > $eitherside){
			$frontdots = " ...";
		}
		else {$frontdots = '';}
		$pg = 1;
		$middlepage = '';
		for($y=0;$y<$num;$y+=$per_page)
		{
			$class=($y==$start)?"pageselected":"";
			if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
			{
				$middlepage .= '<a id="'.$class.'" href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($y>0?("&page=").$y:"") . '\');return false;">'.$pg.'</a>&nbsp;';
			}
			$pg++;
		}
		if(($start+$eitherside)<$num){
			$enddots = "... ";
		}
		else {$enddots = '';}
		
		echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
	}
	
	public function array_validateAPIUser($username,$password)
	{
		if((filter_var($username, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $username)) == TRUE)
		{
			$query = "SELECT ID FROM `users` WHERE `Email` = '" . $this->mysqli->real_escape_string($username) . "' AND Password = '" . md5($password) . "'";
		}
		else
		{
			$query = "SELECT ID FROM `users` WHERE `Username` = '" . $this->mysqli->real_escape_string($username) . "' AND Password = '" . md5($password) . "'";
		}
		$result = $this->mysqli->query($query);
		
		$count = mysqli_num_rows($result);
		
		if($count > 0)
		{
			// we found a row
			$row = $result->fetch_assoc();
			$returnArray = array(TRUE,$row['ID']);
		}
		else
		{
			$returnArray = array(FALSE,"0");
		}		
		return $returnArray;
	}
	
	public function stringRandomizer($type = 'alnum',$count = 10)
	{
		switch($type)
		{
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			
					switch($type)
					{
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'numeric'	:	$pool = '0123456789';
							break;
						case 'nozero'	:	$pool = '123456789';
							break;
					}
	
					$str = '';
					for($i=0;$i<$count;$i++)
					{
						$str .= substr($pool, mt_rand(0,strlen($pool)-1),1);
					}
					return $str;
			break;
			case 'unique' : return md5(uniqid(mt_rand()));
			break;
		}
	}
}