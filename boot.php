<?php

/**
  Plugin Name: Facebook AWD Like Button
  Plugin URI: http://facebook-awd.ahwebdev.fr
  Description: Facebook AWD Like button adds facebook like button on your site
  Version: 2.0-dev
  Author: AHWEBDEV
  Author URI: http://www.ahwebdev.fr
  License: Copywrite AHWEBDEV
  Text Domain: FacebookAWD
  Last modification: 22/05/2014
 */
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\LikeButtonPlugin;

//initialize the Extension
$facebookAWDLikeButton = new LikeButtonPlugin();

//extra debug tool
//$facebookAWDLikeButton->debug(true);
