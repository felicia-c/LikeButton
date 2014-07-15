<?php

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller\FrontController;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller\SettingsController;
use AHWEBDEV\FacebookAWD\Widget\Widget;
use AHWEBDEV\Framework\Plugin\Plugin;

/**
 * Facebook AWD LikeButton
 *
 * @package facebook-awd
 * @author AHWEBDEV (Alexandre Hermann) [hermann.alexandre@ahwebev.fr]
 */
class LikeButtonPlugin extends Plugin
{

    /**
     * Define container data
     * Register the plugin on facebook AWD
     */
    public function __construct()
    {
        $this->slug = 'facebookawd_likebutton';
        $this->title = 'Facebook AWD Like Button';
        $this->ptd = 'facebookawd_likebutton';
        parent::__construct();
        add_action('facebookawd_register_plugins', array($this, 'init'));
    }

    public function boot()
    {
        $settingsController = new SettingsController($this, $this->container->get('admin'));
        $this->set('backend.controller', $settingsController);
        $frontController = new FrontController($this);
        $this->set('front.controller', $frontController);
    }

    public function initControllers()
    {
        $this->get('backend.controller')->init();
        $this->get('front.controller')->init();
    }

}
