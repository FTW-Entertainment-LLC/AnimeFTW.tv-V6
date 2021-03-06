<?php
/****************************************************************\
## FileName: content.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the content class.
## Produces the ancillary content for the site.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Content extends Config {
	
    private $DB;
    
	public function __construct()
	{
		parent::__construct();
        
        // initialize the database connection, we won't actually use it till we call it up.
        include_once('db.class.php');
        $this->DB = new DB($this->dbConnectionInfo());
	}
	
	public function buildHeaderDetails()
	{
		$returndata = '
			<div id="header-top">
				<div id="share-icons">
					<div>
						<a href="#"><img src="/images/themes/default/share-facebook.png" alt="Share on Facebook" /></a>
					</div>
					<div>
						<a href="#"><img src="/images/themes/default/share-twitter.png" alt="Share on Twitter" /></a>
					</div>
					<div>
						<a href="#"><img src="/images/themes/default/share-googleplus.png" alt="Share on Google+" /></a>
					</div>
				</div>
				<div id="logo">
					<div class="logo-image">
						<a href="#"><img src="/images/themes/default/logo.png" alt="" /></a>
					</div>
				</div>
			</div>
			<div id="header-bottom">
				<div id="nav-wrapper">
					<div class="header-nav">
						<div class="left-nav-button active" id="button-profile">
							<a href="#"><div class="login-button">&nbsp;</div></a>
							<div id="user-nav-wrapper">
								<div class="white-top-arrow"></div>
								<div class="user-nav">
									<div class="login-header economica bolded">Log In</div>
									<div class="login-row">
										<div class="login-subheader opensans">Username</div>
										<div class="login-form">
											<input type="text" name="username" id="username" class="user-nav-text-input" />
										</div>
									</div>
									<div class="login-row">
										<div class="login-subheader opensans">Password</div>
										<div class="login-form">
											<input type="password" name="password" id="password" class="user-nav-text-input" />
										</div>
									</div>
									<div class="login-row">
										<div align="left">
											<input type="checkbox" name="remember-me" id="remember-me" class="user-nav-checkbox" />&nbsp; <label class="css-label opensans fourteenfont" for="remember-me">Remember Me</label>
										</div>
									</div>
									<div class="login-row">
										<div class="submit-button-wrapper">
											<a href="#"><span class="button-medium">Log In</span></a>
										</div>
									</div>
									<div class="login-row opensans align-center">
										<div class="twelvefont">Don\'t have an account?</div>
										<div class="twelvefont"><a href="#">Register now!</a></div>
										<div class="twelvefont"><a href="#">Forgot password</a></div>
									</div>
								</div>
							</div>
						</div>';
					$returndata .= '
						<div class="nav-button dropdown-enabled" id="button-home">
							<a href="/" class="nav-button-link"><div class="button">Home</div></a>
							<div class="dropdown-wrapper" id="home-dropdown">
								<div class="dark-top-arrow"></div>';
					$this->DB->query("SELECT `page_title`, `seoname` FROM `page` WHERE `parent` = 1 AND `type` = 0 ORDER BY `page_title` ASC");
					foreach ($this->DB->results() as $key => &$row) {
						$returndata .= '
						<div class="dropdown-row-top">
							<div class="nav-button-text opensans eighteenfont">
								<a href="/' . $row['seoname'] . '">' . stripslashes($row['page_title']) . '</a>
							</div>
						</div>';						
					}
					$returndata .= '
							</div>
						</div>
						<div class="nav-button">
							<a href="/anime" class="nav-button-link"><div class="button">Anime</div></a>
						</div>
						<div class="nav-button">
							<a href="/calendar" class="nav-button-link"><div class="button">Calendar</div></a>
						</div>
						<div class="nav-button">
							<a href="/store" class="nav-button-link"><div class="button">Store</div></a>
						</div>
						<div class="nav-button">
							<a href="/forum" class="nav-button-link"><div class="button">Forum</div></a>
						</div>
					</div>
				</div>
				<div id="search-wrapper">
					<div class="header-search">
						<form name="slimsearch" method="GET" action="/search">
							<input type="hidden" name="sid" value="66762a384bd4f2735c25f77a990a7160" />
							<div class="search">
								<div style="min-width:230px;">
									<input type="text" name="q" id="q" class="search-box" value="Search" autocomplete="off" />
								</div>
								<div style="width:40px;float:right;z-index:20;position:relative;margin:-50px 4px 0 0;">
										<img src="/images/themes/default/search-button.png" alt="" onClick="document.slimsearch.submit()" style="cursor:pointer;" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>';
			return $returndata;
	}
	
	public function buildFooterDetails()
	{
		return '
		<div class="top-footer-wrapper"></div>
			<div class="main-footer-wrapper">
				<div class="content-table opensans">
					<div class="content-row pad-five height-44">
						<div class="content-column three-wide footer-column">
							<div class="single-footer-wrapper">
								' . $this->footerColumnOne() . '
							</div>							
						</div>
						<div class="content-column three-wide footer-column">
							<div class="single-footer-wrapper">
								' . $this->footerColumnTwo() . '
							</div>	
						</div>
						<div class="content-column two-wide footer-column">
							<div class="single-footer-wrapper">
								' . $this->footerColumnThree() . '
							</div>	
						</div>
						<div class="content-column two-wide">
							<div style="height:305px;width:225px;float:right;margin-top:-68px;">
								<img src="//img03.animeftw.tv/chibi-fay.png" alt="" />
							</div>
						</div>
					</div>
				</div>
			</div>';
	}
	
	private function footerColumnOne()
	{
        if ($this->UserArray['Level_access'] == 0) {
            // users not logged in.
            $contentLimits = ' AND `license` = 0 AND `aonly` = 0';
        } elseif ($this->UserArray['Level_access'] == 3) {
            // basic members
            $contentLimits = ' `aonly` <= 1';
        } else {
            // everyone else, which should be staff and AMs
            $contentLimits = '';
        }
        
        $returnData = '
                                <div class="footer-header economica">
									Latest News
								</div>
								<div class="footer-body">';
                                
        if ($this->UserArray['Level_acccess'] == 1 || $this->UserArray['Level_acccess'] == 2 || $this->UserArray['Level_acccess'] == 4 || $this->UserArray['Level_acccess'] == 5 || $this->UserArray['Level_acccess'] == 6) {
			$addonquery = ' OR t.tfid=12 OR t.tfid=14';
		} elseif ($this->UserArray['Level_acccess'] == 7) {
			$addonquery = ' OR t.tfid=14';
		} else {
			$addonquery = '';
		}
		$this->DB->query("SELECT `tid`, `ttitle`, `tdate`, `fseo` FROM forums_threads as t, forums_forum as f WHERE (t.tfid='1' OR t.tfid='2' OR t.tfid='9'" . $addonquery . ") AND f.fid=t.tfid ORDER BY t.tid DESC LIMIT 0, 5");
		foreach ($this->DB->results() as $key => &$row) {
            $returnData .= '
                                    <div class="footer-entry">
										<a href="/forums/' . $row['fseo'] . '/topic-' . $row['tid'] . '/s-0" title="Posted on ' . date('l, F jS, Y @ h:ma',$this->timeZoneChange($row['tdate'],$this->UserArray['timeZone'])) . '">' . stripslashes($row['ttitle']) . '</a>
									</div>';
        }
		$returnData .= '
								</div>';
        return $returnData;
	}
	
	private function footerColumnTwo()
	{
        if ($this->UserArray['Level_access'] == 0) {
            // users not logged in.
            $contentLimits = ' AND `license` = 0 AND `aonly` = 0';
        } elseif ($this->UserArray['Level_access'] == 3) {
            // basic members
            $contentLimits = ' `aonly` <= 1';
        } else {
            // everyone else, which should be staff and AMs
            $contentLimits = '';
        }
        
        $returnData = '
                                <div class="footer-header economica">
									5 Random Anime
								</div>
								<div class="footer-body">';
        
		$this->DB->query("SELECT `fullSeriesName`, `seoname` FROM `series` WHERE active='yes'" . $contentLimits . " ORDER BY RAND() LIMIT 5",TRUE);
		foreach ($this->DB->results() as $key => &$row) {
            $returnData .= '
                                    <div class="footer-entry">
										<a href="/anime/' . $row['seoname'] . '/">' . stripslashes($row['fullSeriesName']) . '</a>
									</div>';
        }
		$returnData .= '
								</div>';
        return $returnData;
	}
	
	private function footerColumnThree()
	{
		$this->DB->query("SELECT `page_title`, `seoname` FROM `page` WHERE `footer` > 0 ORDER BY `footer` ASC");
        
		$returndata = '
		<div class="footer-header economica">
			Sitemap
		</div>
		<div class="footer-body">';
		foreach ($this->DB->results() as $key => &$row) {
			$returndata .= '
			<div class="footer-entry">
				<a href="/' . $row['seoname'] . '">' . stripslashes($row['page_title']) . '</a>
			</div>';
		}
		$returndata .= '
		</div>';
		return $returndata;
	}
	
	public function showHighlightBox()
	{
		return '
			<div class="highlight-box">
				<div class="highlight-wrapper">
					<div class="highlight-overlay">
						<div class="left-highlight-text">HIGHLIGHTS</div>
						<div class="right-highlight-text"></div>
					</div>
				</div>
			</div>';
	}
	
	public function buildRightColumnContent()
	{
        # Begin the top 10 series listing.
		$data = '
					<div class="table-wrapper">
						<div class="table-row">
							<div class="table-column-100 column-left right-content-header">Top 10 Anime</div>
						</div>
						<div class="right-content-data">
							<div class="right-content-overlay">';
        if($this->UserArray['logged-in'] == 0) {
            
            # Only allow logged in member
            $this->DB->query("SELECT `site_topseries`.`seriesID`, `site_topseries`.`lastPosition`, `site_topseries`.`currentPosition`, `site_topseries`.`seriesName`, `series`.`moviesOnly`, `series`.`seoname`, `series`.`stillRelease`, (SELECT COUNT(*) FROM `episode` WHERE `sid`=`series`.`id`) as `totalEpisodes` FROM `site_topseries` INNER JOIN `series` ON `series`.`id`=`site_topseries`.`seriesID` ORDER BY `site_topseries`.`currentPosition` ASC LIMIT 0, 10");

            $i=1;
            foreach ($this->DB->results() as $key => &$row) {
                # is this a movies or episode based series
                if($row['moviesOnly'] == 1) {
                    $videosType = 'movies';
                } else {
                    $videosType = 'episodes';
                }            
                # is the series still releasing?
                if($row['stillRelease'] == 'yes' || $row['stillRelease'] == 1) {
                    $seriesStatus = 'airing';
                } else {
                    $seriesStatus = 'finished';
                }
                $data .= '
								<div class="table-row">
									<div class="table-column-8 right-content-list-number">' . $i . '</div>
									<div class="table-column-15 right-content-list-image">
										<div class="circle-container">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/50x70/' . $row['seriesID'] . '.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
									</div>
									<div class="table-column-70">
										<div class="right-content-list-link normal-weight">
											<a class="list-tag" href="/anime/' . $row['seoname'] . '/" data-node="/scripts.php?view=profiles&show=tooltips&id=' . $row['seriesID'] . '">' . $row['seriesName'] . '</a>
										</div>
										<div class="right-content-list-details">';
                if($row['lastPosition'] < $row['currentPosition']) {
                    // the previous rank is lower than the existing rank, so it went up..
                    $data .= $row['totalEpisodes'] . ' ' . $videosType . ', ' . $seriesStatus . '. <div class="rank-down-arrow" title="Rank Went Down, Previous Rank: ' . $row['lastPosition'] . '"></div>';
                }
                else if($row['lastPosition'] == $row['currentPosition']) {
                    // Current rank is the same as the previous one, thus unchanged.
                    $data .= $row['totalEpisodes'] . ' ' . $videosType . ', ' . $seriesStatus . '. <div class="rank-unchanged-arrow" title="Rank Unchanged, Previous Rank: ' . $row['lastPosition'] . '"></div>';
                }
                else {
                    // by default the rank went up.
                    $data .= $row['totalEpisodes'] . ' ' . $videosType . ', ' . $seriesStatus . '. <div class="rank-up-arrow" title="Rank Went Up, Previous Rank: ' . $row['lastPosition'] . '"></div>';
                }
                $data .= '							
										</div>
									</div>
								</div>';
                $i++;
            }
		$data .= '			
								<div class="table-row right-content-list-row">
									<div class="table-column-100">
										<div class="view-more-button">
											<a href="#"><span>Top 100 Anime</span></a>
										</div>
									</div>
								</div>';
        } else {
            $data .= '
                                <div>
                                    <div align="center" style="padding:5px;">You must be <a href="/login">logged</a> in  to see the Top Series Listing.</div>
                                </div>';
        }
        $data .= '
							</div>
						</div>
					</div>
					<div class="right-content-wrapper">
						<div class="right-content-header">Site Statistics</div>
						<div class="right-content-data">
							<div class="right-content-overlay">
								<div class="content-table opensans">
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 1298</div>
										<div class="content-column fourteenfont seven-wide">Series.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 22933</div>
										<div class="content-column fourteenfont seven-wide">Episodes Online.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 161633</div>
										<div class="content-column fourteenfont seven-wide">Registered users.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 170750</div>
										<div class="content-column fourteenfont seven-wide">Episodes Tracked.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 36472</div>
										<div class="content-column fourteenfont seven-wide">Episode Comments.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide" style="font-family:font-family: \'Open Sans\', sans-serif;">- 687990</div>
										<div class="content-column fourteenfont seven-wide">Minutes of video.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 11466.5</div>
										<div class="content-column fourteenfont seven-wide">hours of videos.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 2911 GB</div>
										<div class="content-column fourteenfont seven-wide">of video.</div>
									</div>
									<div class="content-row pad-ten-left">
										<div class="content-column bolded fourteenfont three-wide">- 412</div>
										<div class="content-column fourteenfont seven-wide">Status Changes.</div>
									</div>
									<div class="content-row pad-ten-left pad-ten-bottom">
										<div class="content-column three-wide">&nbsp;</div>
										<div class="content-column seven-wide elevenfont">Stats updated Every 2 hours.</div>
									</div>
								</div>
							</div>
						</div>
					</div>';
		$data .= '
					<div class="right-content-wrapper">
						<div class="right-content-header">Check Us On...</div>
						<div class="right-content-data">
							<div class="right-content-overlay">
								<div class="text-align-center">
									<a href="http://www.animeftw.tv/download/AnimeFTW.tv.apk"><img src="//img03.animeftw.tv/android-logo-transparent.png" alt="On Android!" style="width:225px;" /></a><br /><br />
									<a href="#"><img src="//img03.animeftw.tv/themes/default/kodi-image.png" /></a><br /><br />
									<a href="#"><img src="//img03.animeftw.tv/themes/default/windows-phone-logo.png" /></a>
								</div>
							</div>
						</div>
					</div>
                    <div class="table-wrapper">
						<div class="table-row">
							<div class="table-column-100 column-left right-content-header">Recently Added Series</div>
						</div>
						<div class="right-content-data">
							<div class="right-content-overlay">';
        $this->DB->query("SELECT `id`, `fullSeriesName`, `seoname`, `stillRelease`, (SELECT COUNT(id) FROM `episode` WHERE `episode`.`sid`=`series`.`id`) as `numrows` FROM `series` WHERE `active` = 'yes' AND `license` = 0 ORDER BY `id` DESC LIMIT 0, 5");

		$i=1;
		foreach ($this->DB->results() as $key => &$row) {
			$airing = 'finished';
			if($row['stillRelease'] == 'yes'){
				$airing = 'airing';
			}
			$data .= '
								<div class="table-row pad-five">
									<div class="table-column-15 right-content-list-image">
										<div class="circle-container">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/' . $row['id'] . '.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
									</div>
									<div class="table-column-80">										
										<div class="twelvefont normal-weight">
											<a class="list-tag" href="/anime/' . $row['seoname'] . '/" data-node="/scripts.php?view=profiles&show=tooltips&id=' . $row['id'] . '">' . $row['fullSeriesName'] . '</a>
										</div>
										<div class="italic twelvefont eight-wide">
											' . $row['numrows'] . ' videos, ' . $airing . '.
										</div>
									</div>
								</div>';
		}
		
		$data .= '			
								<div class="table-row pad-five height-44">
									<div class="table-column-100">
										<div class="view-more-button">
											<a href="#"><span>View All Anime</span></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>';
		$data .= '
					<div class="right-content-wrapper">
						<div class="right-content-header">Latest Airing Episodes</div>
						<div class="right-content-data">
							<div class="right-content-overlay">
								<div class="content-table opensans">
									<div class="content-row pad-ten height-44">
										<div class="content-column two-wide margin-top-three">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide">
											<div class="twelvefont bolded">
												<a href="#">Ep #: Assassination Classroom (2015)</a>
											</div>
											<div class="twelvefont bolded">
												Baseball Time
											</div>
											<div class="twelvefont italic">
												Added on: Jan 25 2015, 08:52 AM
											</div>
										</div>
									</div>
									<div class="content-row pad-ten height-44">
										<div class="content-column two-wide margin-top-three">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide">
											<div class="twelvefont bolded">
												<a href="#">Ep #: Assassination Classroom (2015)</a>
											</div>
											<div class="twelvefont bolded">
												Assassination Time
											</div>
											<div class="twelvefont italic">
												Added on: Jan 25 2015, 08:52 AM
											</div>
										</div>
									</div>
									<div class="content-row pad-ten height-44">
										<div class="content-column two-wide margin-top-three">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide">
											<div class="twelvefont bolded">
												<a href="#">Ep #: Durarara!! 2nd Season</a>
											</div>
											<div class="twelvefont bolded">
												Harmony Is the Greatest of Virtues
											</div>
											<div class="twelvefont italic">
												Added on: Jan 25 2015, 08:48 AM
											</div>
										</div>
									</div>
									<div class="content-row pad-ten height-44">
										<div class="content-column two-wide margin-top-three">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide">
											<div class="twelvefont bolded">
												<a href="#">Ep #: Durarara!! 2nd Season</a>
											</div>
											<div class="twelvefont bolded">
												A Picture Is Worth a Thousand Words
											</div>
											<div class="twelvefont italic">
												Added on: Jan 25 2015, 08:48 AM
											</div>
										</div>
									</div>
									<div class="content-row pad-ten height-44">
										<div class="content-column two-wide margin-top-three">
											<div class="small-circular" style="background:url(\'//img03.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide">
											<div class="twelvefont bolded">
												<a href="#">Ep #: Garo: The Animation</a>
											</div>
											<div class="twelvefont bolded">
												Geste
											</div>
											<div class="twelvefont italic">
												Added on: Jan 25 2015, 07:09 AM
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>';
					return $data;
	}
	
	public function buildLeftColumnContent()
    {
        $data = '
		<div class="left-column-wrapper">';
        
        if ($this->UserArray['Level_acccess'] == 1 || $this->UserArray['Level_acccess'] == 2 || $this->UserArray['Level_acccess'] == 4 || $this->UserArray['Level_acccess'] == 5 || $this->UserArray['Level_acccess'] == 6) {
			$addonquery = "'1','2','9', '14'";
		} elseif ($this->UserArray['Level_acccess'] == 7) {
			$addonquery = "'1','2','9', '14'";
		} else {
			$addonquery = "'1','2','9'";
		}
        $query = "SELECT `tid`, `ttitle`, `tpid`, `tfid`, `tdate`, `ftitle`, `fseo`, `pbody`, `Username`, `display_name`, `avatarActivate`, `avatarExtension` FROM `forums_threads` INNER JOIN `forums_post` ON `forums_post`.`ptid`=`forums_threads`.`tid` INNER JOIN `forums_forum` ON `forums_forum`.`fid`=`forums_threads`.`tfid` INNER JOIN `users` ON `users`.`ID`=`forums_threads`.`tpid` WHERE `forums_threads`.`tfid` in (" . $addonquery . ") ORDER BY `tid` DESC LIMIT 0, 8";
        $this->DB->query($query,TRUE);
        foreach ($this->DB->results() as $key => &$row) {
            if ($row['avatarActivate'] == 'yes') {
                $avatar = '//img03.animeftw.tv/avatars/50x70/user' . $row['tpid'] . '.' . $row['avatarExtension'];
            } else {
                $avatar = '//img03.animeftw.tv/avatars/50x70/default.gif';
            }
            if ($row['display_name'] != NULL) {
                $Username = $row['display_name'];
            } else {
                $Username = $row['Username'];
            }
            $data .= '
			<div class="news-article">
				<div class="news-article-header">
                    <div class="news-article-header-left" style="display:inline-block;">
                        <div class="small-circular" style="background:url(\'' . $avatar . '\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
                    </div>
                    <div class="news-article-header-right" style="display:inline-block;">
                        <div class="news-article-title">
                            <a href="/forums/' . $row['fseo'] . '/topic-' . $row['tid'] . '/s-0">' . stripslashes($row['ttitle']) . '</a>
                        </div>
                        <div class="news-article-title-information">
                            Posted on ' . date('m.d.y', $row['tdate']) . ' by <a href="/user/' . $row['Username'] . '">' . $Username . '</a>
                        </div>
                    </div>
				</div>
				<div class="news-article-body twelvefont opensans">
					' . $row['pbody'] . ' 
				</div>
			</div>';
        }
		$data .= '
        </div>';
		return $data;
        unset($data);
	}
}