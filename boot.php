<?php

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

require_once dirname(__DIR__) . '/facebook-awd/vendors/autoload/autoload.php';
require_once dirname(__DIR__) . '/facebook-awd/vendors/autoload/ClassLoader.php';
use Composer\Autoload\ClassLoader;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\LikeButtonPlugin;

$loader = new ClassLoader();
$loader->add('AHWEBDEV\\FacebookAWD\\Plugin\\LikeButton', __DIR__."/src");
$loader->register(true);

$facebookAWDLikeButton = new LikeButtonPlugin(__FILE__);