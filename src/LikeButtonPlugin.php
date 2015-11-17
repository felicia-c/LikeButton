<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace FacebookAWD\Plugin\LikeButton;

use FacebookAWD\Plugin\LikeButton\Controller\FrontController;
use FacebookAWD\Plugin\LikeButton\Controller\Settings\LikeButtonGeneratorController;
use FacebookAWD\Plugin\LikeButton\Controller\Settings\LikeButtonPostTypeController;
use FacebookAWD\Plugin\LikeButton\Controller\Settings\SettingsController;
use FacebookAWD\Plugin\LikeButton\Service\LikeButtonManager;
use PopCode\Wordpress\Asset\AssetManager;
use PopCode\Wordpress\Plugin;

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
    public function getParent()
    {
        return 'facebookawd';
    }

    /**
     * {@ineritdoc}.
     */
    public function boot()
    {
        //register this plugin on the facebook awd container
        \getFacebookAWD()->addPlugin($this->getSlug(), $this);

        $this->createServices();
        $this->createControllers();

        $this->get('controller.like_button_generator')->init();
        $this->get('controller.posttype')->init();
        $this->get('controller.backend')->init();
    }

    /**
     * Create the controller
     */
    public function createControllers()
    {
        $likeButtonManager = $this->get('services.manager.like_button');
        
        $likeButtonGeneratorController = new LikeButtonGeneratorController($this, $likeButtonManager);
        $this->set('controller.like_button_generator', $likeButtonGeneratorController);
        
        $likeButtonContentController = new LikeButtonPostTypeController($this, $likeButtonManager);
        $this->set('controller.posttype', $likeButtonContentController);
        
        $settingsController = new SettingsController($this, $this->container->get('admin'));
        $this->set('controller.backend', $settingsController);

        $frontController = new FrontController($this, $likeButtonManager);
        $this->set('controller.front', $frontController);
    }

    /**
     * Create the services
     */
    public function createServices()
    {
        $jsm = new AssetManager($this->slug, $this->getRootPath('Resources', 'config', 'js.php'));
        $this->set('services.js_manager', $jsm);
        $this->set('services.manager.like_button', new LikeButtonManager($this));
    }

}
