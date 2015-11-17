<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace FacebookAWD\Plugin\LikeButton\Controller\Settings;

use PopCode\Wordpress\Admin\AjaxInterface;
use PopCode\Wordpress\Admin\MetaboxInterface;
use PopCode\Wordpress\Admin\TinyMceInterface;
use PopCode\Wordpress\Controller\AdminMenuController;

/**
 * FacebookAWD Like Button SettingsController.
 *
 * This file is the setting controller
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class SettingsController extends AdminMenuController implements MetaboxInterface, AjaxInterface, TinyMceInterface
{

    /**
     * {@inheritdoc}
     */
    public function getMenuTitle()
    {
        $iconUrl = plugins_url('/Resources/public/img/facebook_like_thumb.png', dirname(__DIR__));
        return '<img src="' . $iconUrl . '" style="float: left; width:15px; margin-right: 6px;"> ' . $this->tm->_('Like Button');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        return $this;
    }

    /**
     * Enqueue the admin scripts
     */
    public function enqueueScripts()
    {
        wp_enqueue_script($this->container->getSlug() . '_admin');
        wp_enqueue_script($this->container->getSlug() . '_admin-init');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerAjaxHook()
    {
        $this->get('controller.like_button_generator')->registerAjaxHook();
        $this->get('controller.posttype')->registerAjaxHook();
    }

    /**
     * {@inheritdoc}
     */
    public function addMetaBoxes($pageHook)
    {
        $this->container->getRoot()->get('controller.backend')->addDefaultMetaBoxes($pageHook);
        $this->get('controller.like_button_generator')->addMetaBoxes($pageHook);
        $this->get('controller.posttype')->addMetaBoxes($pageHook);
    }

    /**
     * Register plugins on tinyMce.
     *
     * @param array $plugins
     *
     * @return array
     */
    public function registerTinyMcePlugins(array $plugins)
    {
        //$plugins[$this->container->getSlug() . '_shortcode_generator'] = plugins_url('/Resources/public/js/likeButtonTinyMce.js', __DIR__);

        return $plugins;
    }

    /**
     * Register buttons on tinyMce.
     *
     * @param array $buttons
     *
     * @return array
     */
    public function registerTinyMceButtons(array $buttons)
    {
        //$buttons[] = $this->container->getSlug() . '_shortcode_generator';

        return $buttons;
    }

}
