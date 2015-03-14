<?php
/****************************************************************\
## FileName: page-setup.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the page setup script
## this will parse data supplied to it, and render the page content
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class PageSetup extends Config {

	var $page, $content, $ptype, $pname, $pseoname, $mysqli, $currentversion, $rootdir, $PageColumns, $PageArray, $UserArray;
	
	#----------------------------------------------------------------
	# function __construct
	# Initial function that calls when the Class is called.
	# @public
	#----------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
		
		// Parse the bad out of the URl.
		$this->array_parsePageDetails();
		
		// We construct an array so that everyone gets what they want.
		$this->buildPages();
		
		// Now we parse the pages data against what is requested to generate magic
		$this->generatePage();
	}
	
	#----------------------------------------------------------------
	# function array_parsePageDetails
	# Built to be called after the construct function.
	# 	to build details about the page that is being requested
	# @private
	#----------------------------------------------------------------
	
	private function array_parsePageDetails()
	{
		$ReqURL = $_SERVER['REQUEST_URI']; // Requested URL we have to Parse
		$trimmed = trim($ReqURL,"/");
		if(substr($trimmed,0,3) == 'v6/')
		{
			$trimmed = substr($trimmed,3);
		}
		$this->page = $trimmed;
	}
	
	#----------------------------------------------------------------
	# function buildPages
	# Puts together a multi dimensional array for all of the pages.
	# @private
	#----------------------------------------------------------------
	
	private function buildPages()
	{
		// SQL Query
		$sql = "SELECT " . implode(', ', $this->PageColumns) . " FROM `page` ORDER BY `id`";
		
		// if anything happens, we bail.
		if(!$result = $this->mysqli->query($sql))
		{
			die('There was an error running the query [' . $this->mysqli->error . ']');
		}
		
		$pagearray = array();
		$i = 0;
		while($row = $result->fetch_assoc())
		{
			for($r=0; $r < count($this->PageColumns); $r++)
			{
				$pagearray[$row['seoname']][$this->PageColumns[$r]] = $row[$this->PageColumns[$r]];				
			}
			$i++;
		}
		$this->PageArray = $pagearray;
	}
	
	#----------------------------------------------------------------
	# function generatePage
	# Generates the page based on the input.
	# @private
	#----------------------------------------------------------------
	
	private function generatePage()
	{
		// we need to parse the page out, to give them back the proper data.
		$page = array();
		if($this->page == '')
		{
			$this->page 		= 'home';
			$page['seoname'] 	= 'home';
			$page['details'] 	= array('home');
		}
		else
		{
			$page['seoname'] 	= $this->page;
			$page['details'] 	= explode("/",$this->page);
		}
		
		// check the page array, to see if there is something available, if not, give a 404 error.
		if(!array_key_exists($page['seoname'],$this->PageArray) && !isset($page['details'][1]))
		{
			// there are no rows for this page, we need to give a 404 error
			$basepage  = new Template("templates/two-column.tpl");
			echo '404 ' . $page['seoname'];
		}
		else
		{
			/*
				We will first need to make sure the user has access, but we will need to query the database to 
				make sure there are no restrictions on a series or thread..
			*/
			include_once("content.class.php");
			$Content = new Content();
			$basepage  = new Template("templates/" . $this->PageArray[$page['details'][0]]['template']);
			
			// set constants and root level options.
			$basepage->set('header',$Content->buildHeaderDetails());
			$basepage->set('footer',$Content->buildFooterDetails());
			$basepage->set('highlight',$Content->showHighlightBox());
			
			if($page['details'][0] == 'anime')
			{
				if(!isset($page['details'][1]) && !isset($page['details'][2]))
				{
					$pageinfo = $this->PageArray['anime'];
					$basepage  = new Template("templates/404.tpl");
					// this is the series page
					echo 'Anime Listing page ' . print_r($page['details']);;
				}
				else if(isset($page['details'][1]) && !isset($page['details'][2]))
				{
					include_once("series.class.php");
					$Series = new Series();
					
					$pageinfo = $this->PageArray['anime/%seoname%'];
					if(isset($SeriesInfo['accesslevels']) && strpos($SeriesInfo['accesslevels'],$this->UserArray['Level_access']) !== FALSE)
					{
						// the series exists, and the user has access, let's start deploying information.
					}
					else
					{
						// the user does not have rights or there is no such series, give them a 404 error.
					}
					// this is the series page
					echo 'Series Details ' . print_r($page['details']);;
				}
				else
				{
					include_once("series.class.php");
					$Series = new Series();
					$SeriesInfo = $Series->buildSeriesInformation($page['details'][1]);
					if(isset($SeriesInfo['accesslevels']) && strpos($SeriesInfo['accesslevels'],$this->UserArray['Level_access']) !== FALSE)
					{
						include_once("video.class.php");
						$Video = new Video();
						
						if(substr($page['details'][2],0,3) == 'ep-')
						{
							$pageinfo = $this->PageArray['anime/%seoname%/ep-%epnumber%'];
							$VideoInfo = $Video->buildVideoInformation($SeriesInfo['id'],substr($page['details'][2],3),0);
							// this is an episode
						}
						else if(substr($page['details'][2],0,6) == 'movie-')
						{
							$pageinfo = $this->PageArray['anime/%seoname%/movie-%movienumber%'];
							$VideoInfo = $Video->buildVideoInformation($SeriesInfo['id'],substr($page['details'][2],6),1);
							// this is a movie
						}
						else
						{
							// 404 page is needed since this request is invalid.
						}
					}
					else
					{
						// the user does not have access to the series, give them a 404 page.
					}
					// this is an episode or a movie
					echo 'Single Episode ';
					print_r($page['details']);
				}
				$basepage->set('title',$this->PageArray[$page['details'][0]]['page_title']);
			}
			else if($page['details'][0] == 'forums')
			{
				if(isset($page['details'][1]) && !isset($page['details'][2]) &&  !isset($page['details'][3]))
				{
					// this is the thread view for a forum
					echo 'Forum view with threads listing ' . print_r($page['details']);;
				}
				else if(isset($page['details'][1]) && isset($page['details'][2]) &&  !isset($page['details'][3]))
				{
					// the first page of the topic
					echo 'page one details of the thread ' . print_r($page['details']);;
				}
				else
				{
					// any secondary pages for the thread
					echo '2+ page view of threads ' . print_r($page['details']);;
				}
				$basepage->set('title',$this->PageArray[$page['details'][0]]['page_title']);
			}
			else
			{
				if(in_array($this->UserArray['Level_access'], explode(',', $this->PageArray[$page['details'][0]]['security'])) !== FALSE)
				{
					// check to see if the group is present for security purposes
					// query the content database for everything necessary.
					$query = "SELECT `content` FROM `page_content` WHERE `page_id` = " . $this->PageArray[$page['details'][0]]['id'] . " AND `name` = 'content'";
					$result = $this->mysqli->query($query) or die('Error : ' . $this->mysqli->error);
					$row = $result->fetch_assoc();
					//print_r($this->PageArray[$page['details'][0]]);
					$basepage->set('title','AnimeFTW.tv - ' . $this->PageArray[$page['details'][0]]['page_title']);
					$basepage->set('left-column',$row['content']);
				}
				else
				{
					$basepage->set('title','AnimeFTW.tv - 404 Error, the page was not found!!');
					//$basepage->set('left-column',);
				}
				$basepage->set('right-column',$Content->buildRightColumnContent());
			}
			// check if they have access to this page..
		}
		echo $basepage->output();
	}
}

// 1 2 3 GO!
$PageSetup = new PageSetup();