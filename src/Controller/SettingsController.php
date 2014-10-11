<?php

/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType;
use AHWEBDEV\Framework\TemplateManager\Form;
use AHWEBDEV\Wordpress\Admin\MetaboxInterface;
use AHWEBDEV\Wordpress\Controller\AdminMenuController as BaseController;
use AHWEBDEV\Wordpress\Widget\Widget;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use WP_Post;
use WP_Widget_Factory;

/**
 * FacebookAWD Like Button SettingsController
 *
 * This file is the setting controller
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package   FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class SettingsController extends BaseController implements MetaboxInterface
{

    /**
     * {@inheritdoc}
     */
    public function getMenuType()
    {
        return self::TYPE_SUBMENU;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuSlug()
    {
        return $this->container->getRoot()->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuTitle()
    {
        return preg_replace('/' . $this->container->getRoot()->getTitle() . '/', '', $this->container->getTitle());
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        add_action('wp_ajax_save_settings_' . $this->container->getSlug(), array($this, 'handlesSettingsSection'));

        //assets configurations
        $publicUrl = plugins_url(null, __DIR__) . '/Resources/public';
        $parentSlug = $this->container->getRoot()->getSlug();
        $assets = $this->container->getRoot()->getAssets();
        $assets['script'][$this->container->getSlug() . 'admin'] = array(
            'src' => $publicUrl . '/js/admin.js',
            'deps' => array($parentSlug . 'admin')
        );
        //the init js script requires this asset to be loaded before to be enqueu
        $assets['script'][$parentSlug . 'admin-init']['deps'][] = $this->container->getSlug() . 'admin';
        $this->container->getRoot()->setAssets($assets);

        //enqueue this script.
        $pageHook = $this->admin->getAdminMenuHook($this->container->getSlug());
        add_action('admin_print_scripts-' . $pageHook, array($this, 'enqueueScripts'));
        add_action('widgets_init', array($this, 'registerWidgets'));
    }

    /**
     * {@inheritdoc}
     */
    public function adminMenu()
    {
        parent::adminMenu();

        //add the like button boxes to the post.php
        add_action('load-post.php', array($this, 'loadMetaboxes'));
    }

    /**
     * Register widgets
     * 
     * @todo Add an interface to call this method automatically
     * @global WP_Widget_Factory $wp_widget_factory
     */
    public function registerWidgets()
    {
        //widgets
        global $wp_widget_factory;
        $wp_widget_factory->widgets[$this->container->getSlug()] = new Widget(array(
            'idBase' => $this->container->getSlug(),
            'name' => $this->container->getTitle(),
            'description' => $this->container->getTitle(),
            'ptd' => $this->container->getPtd(),
            'model' => 'AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton',
            'selfCallback' => array($this->container->get('controller.front'), 'renderLikeButton'),
            'preview' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function addMetaBoxes($pageHook)
    {
        $adminController = $this->container->getRoot()->get('controller.backend');

        //Default metaboxes
        $adminController->addMetaboxes($pageHook);
        remove_meta_box($pageHook . '_plugins', $pageHook, 'normal');

        //post types metaboxes
        $postTypes = get_post_types(array('public' => true), 'objects');
        foreach ($postTypes as $postType) {
            //facebookawd admin
            add_meta_box($this->container->getSlug() . $postType->name, 'On ' . strtolower($postType->labels->name), array($this, 'settingsBoxes'), $pageHook, 'normal', 'default', array($postType));

            //post type pages
            add_action('admin_print_styles-post.php', array($this->admin, 'enqueueStyles'));
            add_action('admin_print_styles-post.php', array($this->admin, 'enqueueScripts'));
            add_action('save_post', array($this, 'handlesSettingsSection'));
            add_action('edit_attachment', array($this, 'handlesSettingsSection'));
            add_meta_box($this->container->getSlug() . $postType->name, 'Like Button Settings', array($this, 'settingsBoxes'), $postType->name, 'normal', 'default', array($postType));
        }
    }

    /**
     * Enqueue assets
     */
    public function enqueueScripts()
    {
        wp_enqueue_script($this->container->getSlug() . 'admin');
    }

    /**
     * Create the index page
     */
    public function index()
    {
        $this->handlesSettingsSection();
        $template = $this->container->getRoot()->getRootPath() . '/Resources/views/admin/metaboxes.html.php';
        echo $this->render($template, array(
            'title' => $this->container->getTitle() . ' <a class="button-secondary" href="?page=' . $this->container->getRoot()->getSlug() . '">Back</a>',
            'application' => $this->container->getRoot()->get('services.application'),
            'args' => array(),
            'boxes' => array(
                array(
                    //'type' => 'do_accordion_sections',
                    'type' => 'do_meta_boxes',
                    'context' => 'normal'
                ),
            ),
            'boxesSide' => array(
                array(
                    'type' => 'do_meta_boxes',
                    'context' => 'side'
                )
            )
        ));
    }

    /**
     * Render the like Button settgins section
     * 
     * This method is called by meta boxes in admin of plugin
     * and in the post types edition pages
     * 
     * @param  stdClass           $postType
     * @param  LikeButtonPostType $likeButtonPostType
     * @param  null|WP_Post       $post
     * @return string
     */
    public function settingsSection($postType, $post = null)
    {

        if (!$postType) {
            throw new InvalidArgumentException('The $postType arg is required for settingsSection');
        }

        $om = $this->container->getRoot()->get('services.option_manager');

        $likeButtonPostType = $om->load('options.' . $this->container->getSlug() . '.' . $postType->name);
        if (!is_object($likeButtonPostType)) {
            $likeButtonPostType = new LikeButtonPostType();
        }

        $form = new Form($this->container->getSlug());

        $success = $om->load($this->container->getSlug() . '_' . $postType->name . '_success' . $postType->name);
        $om->save($this->container->getSlug() . '_' . $postType->name . '_success', false);

        //default instance and config
        $likeButtonPostTypeFormConfig = $likeButtonPostType->getFormConfig();
        //if we are on a post
        if ($post) {
            // try to get instance from post meta.
            $likeButtonPostTypeFromPost = get_post_meta($post->ID, $this->container->getSlug() . '_posttype', true);
            if (is_object($likeButtonPostTypeFromPost)) {
                $likeButtonPostType = $likeButtonPostTypeFromPost;
                $likeButtonPostTypeFormConfig = $likeButtonPostType->getFormConfig();
                unset($likeButtonPostTypeFromPost);
            }

            //redefine section only on post
            $likeButtonPostTypeFormConfig['redefine']['attr'] = array(
                'class' => 'form-control hideIfOn',
                'data-hide-on' => '.section_likeButtonPostTypeOptions'
            );
            $redefineFormConfig = array('redefine' => $likeButtonPostTypeFormConfig['redefine']);
            $sections['redefine'] = $form->processFields('posttype_' . $postType->name, $redefineFormConfig);
        }
        //remove this field, he is only required on post edition and already rendered
        unset($likeButtonPostTypeFormConfig['redefine']);

        //enable section
        $likeButtonPostTypeFormConfig['enable']['attr'] = array(
            'class' => 'form-control hideIfOn',
            'data-hide-on' => '.section_likeButtonPostType, .section_likeButton'
        );
        $enableFormConfig = array('enable' => $likeButtonPostTypeFormConfig['enable']);
        unset($likeButtonPostTypeFormConfig['enable']);

        //the section
        $sections['likeButtonPostTypeOptions'] = array(
            'enable' => $form->processFields('posttype_' . $postType->name, $enableFormConfig),
            'likeButtonPostType' => $form->processFields('posttype_' . $postType->name, $likeButtonPostTypeFormConfig),
            'likeButton' => $form->processFields('posttype_' . $postType->name, $likeButtonPostType->getLikeButton()->getFormConfig())
        );
        $sections['security'] = $form->processFields('posttype_' . $postType->name, $this->container->getRoot()->getTokenFormConfig());

        $data = array('classes' => 'posttype_section section ' . $postType->name,
            'withSubmit' => !$post,
            'postTypeName' => $postType->name,
            'sections' => $sections,
            'success' => $success
        );

        $template = $this->container->getRoot()->getRootPath() . '/Resources/views/admin/settingsForm.html.php';
        return $this->render($template, $data);
    }

    /**
     * Wrap a section into a metabox
     * 
     * @param $post
     * @param  array            $metaboxData
     * @throws RuntimeException
     */
    public function settingsBoxes($post, array $metaboxData)
    {
        echo $this->settingsSection($metaboxData['args'][0], $post);
    }

    /**
     * Handle the post of the settings section
     * 
     * This method works for each post types.
     * The configuration will be save on post directly (if set from post/page editor)
     * Or saved into the plugin configuration related to each post type.
     * 
     * @param integer|null $postId
     * @return void
     */
    public function handlesSettingsSection($postId = null)
    {
        $request = filter_input_array(INPUT_POST);
        if ($request) {
            foreach ($request as $key => $postTypeRequest) {
                if (preg_match('/' . $this->container->getSlug() . 'posttype/', $key)) {
                    if (is_array($postTypeRequest)) {
                        $isTokenValid = wp_verify_nonce($postTypeRequest['token'], 'fawd-token');
                        if ($isTokenValid) {

                            $likeButtonPostType = new LikeButtonPostType();
                            $likeButtonPostType->bind($postTypeRequest);
                            $likeButton = $likeButtonPostType->getLikeButton();
                            $likeButton->bind($postTypeRequest);

                            //save meta to post if redefine is used.
                            if (isset($postTypeRequest['redefine']) && $postId) {
                                if ($postTypeRequest['redefine'] == true) {
                                    update_post_meta($postId, $this->container->getSlug() . '_posttype', $likeButtonPostType);
                                } else {
                                    delete_post_meta($postId, $this->container->getSlug() . '_posttype');
                                }

                                return;
                            }

                            $postTypeName = str_replace($this->container->getSlug() . 'posttype_', '', $key);
                            $om = $this->container->getRoot()->get('services.option_manager');
                            $om->save('options.' . $this->container->getSlug() . '.' . $postTypeName, $likeButtonPostType);
                            $om->save($this->container->getSlug() . '_' . $postTypeName . '_success', 'Settings were updated with success');
                            if ($this->isAjaxRequest()) {
                                $template = $this->container->getRoot()->getRootPath() . '/Resources/views/ajax/ajax.json.php';
                                echo $this->render($template, array(
                                    'postTypeName' => $postTypeName,
                                    'section' => $this->settingsSection(get_post_type_object($postTypeName), $likeButtonPostType)
                                ));
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }

}
