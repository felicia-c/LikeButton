<?php

$testdir = getenv('WP_TESTS');

require_once $testdir . '/tmp/wordpress-tests-lib/includes/functions.php';

function _manually_load_plugin()
{
    //load the facebook awd plugin
    require dirname(__DIR__) . '/../../facebook-awd/boot.php';
    //load the plugin
    require dirname(__DIR__) . '/../boot.php';
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');
require $testdir . '/tmp/wordpress-tests-lib/includes/bootstrap.php';

