<?php 

/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */
namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller\FrontController;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller\SettingsController;
use AHWEBDEV\Framework\Plugin\Plugin;

/**
 * FacebookAWD Like Button
 *
 * This file is the base container of the Facebook AWD LikeButton extension
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package   FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
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

    /**
     * {@ineritdoc}
     */
    public function boot()
    {
        $settingsController = new SettingsController($this, $this->container->get('admin'));
        $this->set('backend.controller', $settingsController);
        $frontController = new FrontController($this);
        $this->set('front.controller', $frontController);
    }

    /**
     * Init the controllers of the plugin
     */
    public function initControllers()
    {
        $this->get('backend.controller')->init();
        $this->get('front.controller')->init();
    }

}