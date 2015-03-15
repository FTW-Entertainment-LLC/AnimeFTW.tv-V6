<?php
/****************************************************************\
## FileName: series.class.php									 
## Author: Brad Riemann										 
## Usage: Version 6.0 of the series class.
## Copyright 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Series Extends Config  {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function buildSeriesInformation()
	{
		return TRUE;
	}
	
	public function showAnimeListings()
	{
		return '
		<div class="news-article">
			<div class="news-article-header">
				<div class="news-article-title-information pad-ten-left">
					<div class="news-article-title">
						Available Anime
					</div>
					<div class="news-article-date">
						Please keep these in mind when you utilize the site.
					</div>
				</div>
			</div>
			<div class="news-article-body">
				<div class="news-article-content">
				</div>
			</div>
		</div>';
	}
}