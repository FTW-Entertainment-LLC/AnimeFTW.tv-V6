<?php
/****************************************************************\
## FileName: content.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the content class.
## Produces the ancillary content for the site.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Content extends Config {
	
	public function __construct()
	{
		parent::__construct();
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
					$query = "SELECT `page_title`, `seoname` FROM `page` WHERE `parent` = 1 AND `type` = 0 ORDER BY `page_title` ASC";
					$result = $this->mysqli->query($query) or die('Error : ' . $this->mysqli->error);
					while($row = $result->fetch_assoc())
					{
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
								<img src=" 	https://d206m0dw9i4jjv.cloudfront.net/chibi-fay.png" alt="" />
							</div>
						</div>
					</div>
				</div>
			</div>';
	}
	
	private function footerColumnOne()
	{
		return '
								<div class="footer-header economica bolded">
									Latest News
								</div>
								<div class="footer-body">
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
								</div>';
	}
	
	private function footerColumnTwo()
	{
		return '
								<div class="footer-header economica">
									5 Random Anime
								</div>
								<div class="footer-body">
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
									<div class="footer-entry">
										<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
									</div>
								</div>
		';
	}
	
	private function footerColumnThree()
	{
		$query = "SELECT `page_title`, `seoname` FROM `page` WHERE `footer` > 0 ORDER BY `footer` ASC";
		$result = $this->mysqli->query($query) or die('Error : ' . $this->mysqli->error);
		$returndata = '
		<div class="footer-header economica">
			Sitemap
		</div>
		<div class="footer-body">';
		while($row = $result->fetch_assoc())
		{
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
		return '
		<div class="right-content-wrapper">
						<div class="right-content-header">Top 10 Anime</div>
						<div class="right-content-data">
							<div class="right-content-overlay" style="padding-top:5px;">
								<div class="right-content-list">
									<div class="right-content-list-row">
										<div class="right-content-list-number">1</div>
										<div class="right-content-list-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="right-content-list-content">
											<div class="right-content-list-link">
												<a class="side tooltip-overlay" href="/anime/rage-of-bahamut-genesis/" data-node="/scripts.php?view=profiles&show=tooltips&id=1335">Rage of Bahamut: Genesis</a>
											</div>
											<div class="right-content-list-details">
												X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_none.gif" title="Rank Unchanged, Previous Rank: 1" alt="" />
											</div>
										</div>
									</div>
								</div>
								<div class="right-content-list">
									<div class="right-content-list-row">
										<div class="right-content-list-number">2</div>
										<div class="right-content-list-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="right-content-list-content">
											<div class="right-content-list-link">
												<a class="side tooltip-overlay" href="/anime/death-billiards/" data-node="/scripts.php?view=profiles&show=tooltips&id=1283">Death Billiards</a>
											</div>
											<div class="right-content-list-details">
												X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_none.gif" title="Rank Unchanged, Previous Rank: 2" alt="" />
											</div>
										</div>
									</div>
								</div>
								<div class="right-content-list">
									<div class="right-content-list-row">
										<div class="right-content-list-number">3</div>
										<div class="right-content-list-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="right-content-list-content">
											<div class="right-content-list-link">
												<a class=\'side tooltip-overlay\' href=\'/anime/maken-ki-two-takeru-nyotaika-minami-no-shima-de-supoon/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1293" title="Maken-ki! Two: Takeru Nyotaika!? Minami no Shima de Supoon">Maken-ki! Two: Takeru Nyotaika!? Mina..</a>
											</div>
											<div class="right-content-list-details">
												X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_none.gif" title="Rank Unchanged, Previous Rank: 3" alt="" />
											</div>
										</div>
									</div>
								</div>
								<div class="right-content-list">
									<div class="right-content-list-row">
										<div class="right-content-list-number">4</div>
										<div class="right-content-list-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="right-content-list-content">
											<div class="right-content-list-link">
												<a class=\'side tooltip-overlay\' href=\'/anime/psycho-pass-2/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1296">Psycho-Pass 2</a>
											</div>
											<div class="right-content-list-details">
												X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 28" />
											</div>
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="right-content-list-number">5</div>
									<div class="right-content-list-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
									</div>
									<div class="right-content-list-content">
										<div class="right-content-list-link">
											<a class=\'side tooltip-overlay\' href=\'/anime/wolf-girl-black-prince/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1324">Wolf Girl &amp; Black Prince</a>
										</div>
										<div class="right-content-list-details">
											X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 31" />
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="right-content-list-number">6</div>
									<div class="right-content-list-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
									</div>
									<div class="right-content-list-content">
										<div class="right-content-list-link">
											<a class=\'side tooltip-overlay\' href=\'/anime/black-bullet/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1130">Black Bullet</a>
										</div>
										<div class="right-content-list-details">
											X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 11" />
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="right-content-list-number">7</div>
									<div class="right-content-list-image">
										<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
									</div>
									<div class="right-content-list-content">
										<div class="right-content-list-link">
											<a class=\'side tooltip-overlay\' href=\'/anime/log-horizon/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1146">Log Horizon</a>
										</div>
										<div class="right-content-list-details">
											X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 34" />
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="right-content-list-number">8</div>
									<div class="right-content-list-image">
										<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
									</div>
									<div class="right-content-list-content">
										<div class="right-content-list-link">
											<a class=\'side tooltip-overlay\' href=\'/anime/lord-marksman-and-vanadis/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1270">Lord Marksman and Vanadis</a>
										</div>
										<div class="right-content-list-details">
											X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 12" />
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="right-content-list-number">9</div>
									<div class="right-content-list-image">
										<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
									</div>
									<div class="right-content-list-content">
										<div class="right-content-list-link">
											<a class=\'side tooltip-overlay\' href=\'/anime/deadman-wonderland/\' data-node="/scripts.php?view=profiles&show=tooltips&id=457">Deadman Wonderland</a>
										</div>
										<div class="right-content-list-details">
											X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 19" />
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="right-content-list-number">10</div>
									<div class="right-content-list-image">
										<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
									</div>
									<div class="right-content-list-content">
										<div class="right-content-list-link">
											<a class=\'side tooltip-overlay\' href=\'/anime/no-game-no-life/\' data-node="/scripts.php?view=profiles&show=tooltips&id=1139">No Game No Life</a>
										</div>
										<div class="right-content-list-details">
											X Episodes, finished. <img src="http://img02.animeftw.tv/arrow_up.gif"  alt="" title="Rank Went up, Previous Rank: 130" />
										</div>
									</div>
								</div>
								<div class="right-content-list-row">
									<div class="view-more-button">
										<a href="#"><span>Top 100 Anime</span></a>
									</div>
								</div>
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
					</div>
					<div class="right-content-wrapper">
						<div class="right-content-header">Check Us On...</div>
						<div class="right-content-data">
							<div class="right-content-overlay">
								<div class="text-align-center">
									<a href="http://www.animeftw.tv/download/AnimeFTW.tv.apk"><img src="https://d206m0dw9i4jjv.cloudfront.net/android-logo-transparent.png" alt="On Android!" style="width:225px;" /></a>
								</div>
							</div>
						</div>
					</div>
					<div class="right-content-wrapper">
						<div class="right-content-header">Latest Series</div>
						<div class="right-content-data">
							<div class="right-content-overlay">
								<div class="content-table opensans">
									<div class="content-row pad-five height-44">
										<div class="content-column two-wide latest-series-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide margin-top-six">
											<div class="twelvefont bolded">
												<a href="/anime/chou-denji-machine-voltes-v/">Chou Denji Machine Voltes V</a>
											</div>
											<div class="content-column italic twelvefont eight-wide">
												12 episodes, finished.
											</div>
										</div>
									</div>
									<div class="content-row pad-five height-44">
										<div class="content-column two-wide latest-series-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide margin-top-six">
											<div class="twelvefont bolded">
												<a href="/anime/golgo-13/">Golgo 13</a>
											</div>
											<div class="content-column italic twelvefont eight-wide">
												12 episodes, finished.
											</div>
										</div>
									</div>
									<div class="content-row pad-five height-44">
										<div class="content-column two-wide latest-series-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide margin-top-six">
											<div class="twelvefont bolded">
												<a href="/anime/marchen-awakens-romance/">Marchen Awakens Romance</a>
											</div>
											<div class="content-column italic twelvefont eight-wide">
												12 episodes, finished.
											</div>
										</div>
									</div>
									<div class="content-row pad-five height-44">
										<div class="content-column two-wide latest-series-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide" margin-top-six>
											<div class="twelvefont bolded">
												<a href="/anime/rage-of-bahamut-genesis/">Rage of Bahamut: Genesis</a>
											</div>
											<div class="content-column italic twelvefont eight-wide">
												12 episodes, finished.
											</div>
										</div>
									</div>
									<div class="content-row pad-five height-44">
										<div class="content-column two-wide latest-series-image">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
										</div>
										<div class="content-column eight-wide margin-top-six">
											<div class="twelvefont bolded">
												<a href="/anime/tales-of-zestiria-dawn-of-a-shepherd/">Tales of Zestiria: Dawn of the Monk</a>
											</div>
											<div class="content-column italic twelvefont eight-wide">
												12 episodes, finished.
											</div>
										</div>
									</div>
									<div class="content-row pad-five height-44">
										<div class="view-more-button">
											<a href="#"><span>View All Anime</span></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="right-content-wrapper">
						<div class="right-content-header">Latest Airing Episodes</div>
						<div class="right-content-data">
							<div class="right-content-overlay">
								<div class="content-table opensans">
									<div class="content-row pad-ten height-44">
										<div class="content-column two-wide margin-top-three">
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
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
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
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
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
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
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
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
											<div class="small-circular" style="background:url(\'http://img02.animeftw.tv/seriesimages/1335.jpg\') no-repeat;background-position: center center;margin:5px 0 0 5px;"></div>
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
	}
}