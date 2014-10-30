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
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Manager\LikeButtonManager;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Shortcode\LikeButtonShortcode;
use AHWEBDEV\Wordpress\Plugin\Plugin;

/**
 * FacebookAWD Like Button extension
 *
 * This file is the base container of the Facebook AWD LikeButton extension
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package      FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonPlugin extends Plugin
{

    /**
     * {@ineritdoc}
     */
    public function boot()
    {
        $likeButtonManager = new LikeButtonManager($this);
        $this->set('manager', $likeButtonManager);

        $settingsController = new SettingsController($this, $this->container->get('admin'), $likeButtonManager);
        $this->set('controller.backend', $settingsController);

        $frontController = new FrontController($this, $likeButtonManager);
        $this->set('controller.front', $frontController);

        $likeButtonShortcode = new LikeButtonShortcode($this->getSlug(), $likeButtonManager);
        $this->set('services.like_button_shortcode', $likeButtonShortcode);
    }

    /**
     * Init the controllers of the plugin
     */
    public function initControllers()
    {
        $this->get('controller.backend')->init();
        $this->get('controller.front')->init();
    }

}
