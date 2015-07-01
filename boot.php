<?php

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\LikeButtonPlugin;

/**
  Plugin Name: Facebook AWD Like Button
  Plugin URI: http://facebook-awd.ahwebdev.fr
  Description: Facebook AWD Like button adds facebook like button on your site
  Version: 2.0
  Author: Alexandre Hermann (AHWEBDEV) <hermann.alexandre@ahwebdev.fr>
  Author URI: http://www.ahwebdev.fr
  License: Copywrite AHWEBDEV
  Text Domain: FacebookAWD
  Last modification: 22/05/2014
 */
$loader = include dirname(__DIR__) . '/facebook-awd/vendor/autoload.php';
$loader->addPsr4('AHWEBDEV\\FacebookAWD\\Plugin\\LikeButton\\', __DIR__ . "/src");
$loader->register(true);
//$facebookAWDLikeButton = new LikeButtonPlugin(__FILE__);
