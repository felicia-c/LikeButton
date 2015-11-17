<?php

/**
 * Facebook AWD connect assets js config.
 *
 * @author PopCode (Alexandre Hermann) [hermann.alexandre@ahwebev.fr]
 */
$configs = array(
    '__' => array(
        'src' => '/js/init.js',
        'deps' => array('facebookawd', '__likeButton'),
        'footer' => true,
    ),
    '__likeButton' => array(
        'src' => '/js/likeButton.js',
        'deps' => array('__connect')
    ),
    '__admin' => array(
        'deps' => array('facebookawd_admin'),
        'src' => '/js/admin/admin.js',
    ),
    '__admin-init' => array(
        'src' => '/js/admin/init.js',
        'deps' => array('facebookawd_admin-init'),
        'footer' => true,
    ),
);

$path = plugins_url('/public', __DIR__);
return $this->applyPrefixPath($configs, $path);
