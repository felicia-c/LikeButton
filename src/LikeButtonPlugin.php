<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller\LikeButtonController;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller\SettingsController;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Service\LikeButtonManager;
use AHWEBDEV\Wordpress\Plugin;

/**
 * FacebookAWD Like Button extension.
 *
 * This file is the base container of the Facebook AWD LikeButton extension
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonPlugin extends Plugin
{
    /**
     * {@ineritdoc}.
     */
    public function boot()
    {
        $likeButtonManager = new LikeButtonManager($this);
        $this->set('manager', $likeButtonManager);

        $settingsController = new SettingsController($this, $this->container->get('admin'), $likeButtonManager);
        $this->set('controller.backend', $settingsController);

        $frontController = new LikeButtonController($this, $likeButtonManager);
        $this->set('controller.like_button', $frontController);
    }

    /**
     * Init the controllers of the plugin.
     */
    public function initControllers()
    {
        $this->get('controller.backend')->init();
        $this->get('controller.like_button')->init();
    }
}
