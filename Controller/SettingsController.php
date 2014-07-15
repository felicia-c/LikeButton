<?php

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType;
use AHWEBDEV\Framework\TemplateManager\Form;
use AHWEBDEV\Wordpress\Admin\MetaboxInterface;
use AHWEBDEV\Wordpress\Controller\AdminMenuController as BaseController;
use AHWEBDEV\Wordpress\Widget\Widget;
use RuntimeException;
use stdClass;

/*
 * This file is part of FacebookAWD.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * SettingsController
 *
 * @author Alexandre Hermann <hermann.alexandren@ahwebdev.fr>
 * @package FacebookAWD\Extension\Wordpress
 */
class SettingsController extends BaseController implements MetaboxInterface
{

    /**
     *
     * @return string
     */
    public function getMenuType()
    {
        return self::TYPE_SUBMENU;
    }

    /**
     *
     * @return string
     */
    public function getMenuSlug()
    {
        return $this->container->getRoot()->getSlug();
    }

    /**
     *
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->container->getTitle();
    }

    /**
     * Init the controller
     * Add an wordpress action to create the admin menu/submenu page
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
     * 
     * @global \WP_Widget_Factory $wp_widget_factory
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
            'selfCallback' => array($this->container->get('front.controller'), 'renderLikeButton'),
            'preview' => true
        ));
    }

    /**
     *
     * @param string $pageHook
     */
    public function addMetaBoxes($pageHook)
    {
        $pageHook = $this->admin->getAdminMenuHook($this->container->getSlug());
        $adminController = $this->container->getRoot()->get('backend.controller');
        $om = $this->container->getRoot()->get('services.option_manager');

        //Default metaboxes
        $adminController->addMetaboxes($pageHook);
        remove_meta_box($pageHook . '_plugins', $pageHook, 'normal');

        //post types metaboxes
        $postTypes = get_post_types(array('public' => true), 'objects');
        foreach ($postTypes as $postType) {
            $likeButtonPosType = $om->load('options.' . $this->container->getSlug() . '.' . $postType->name);
            if (!is_object($likeButtonPosType)) {
                $likeButtonPosType = new LikeButtonPostType();
            }
            //facebookawd admin
            add_meta_box($this->container->getSlug() . $postType->name, $postType->labels->name, array($this, 'settingsBoxes'), $pageHook, 'normal', 'default', array($postType, $likeButtonPosType));

            //post type pages
            if (is_object($likeButtonPosType)) {
                //$likeButtonPosType->setEnable(0);
                add_action('admin_print_styles-post.php', array($this->admin, 'enqueueStyles'));
                add_action('admin_print_styles-post.php', array($this->admin, 'enqueueScripts'));
                add_action('save_post', array($this, 'handlesSettingsSection'));

                add_meta_box($this->container->getSlug() . $postType->name, 'Like Button Settings', array($this, 'settingsBoxes'), $postType->name, 'normal', 'default', array($postType, $likeButtonPosType));
            }
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
     * Return index content
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
     * 
     * @param stdClass $postType
     * @param LikeButtonPostType $likeButtonPostType
     * @param null|WP_Post $post
     * @return string
     */
    public function settingsSection($postType, $likeButtonPostType, $post = null)
    {
        $om = $this->container->getRoot()->get('services.option_manager');
        $form = new Form($this->container->getSlug());

        $success = $om->load('fawd_application_' . $this->container->getSlug() . '_success_' . $postType->name);
        $om->save('fawd_application_' . $this->container->getSlug() . '_success_' . $postType->name, false);

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
            $sections['redefine'] = $form->proccessFields('posttype_' . $postType->name, $redefineFormConfig);
        }

        //remove this field, he is only required on post edition.
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
            'enable' => $form->proccessFields('posttype_' . $postType->name, $enableFormConfig),
            'likeButtonPostType' => $form->proccessFields('posttype_' . $postType->name, $likeButtonPostTypeFormConfig),
            'likeButton' => $form->proccessFields('posttype_' . $postType->name, $likeButtonPostType->getLikeButton()->getFormConfig())
        );
        $sections['security'] = $form->proccessFields('posttype_' . $postType->name, $this->container->getRoot()->getTokenFormConfig());

        $template = $this->container->getRootPath() . '/Resources/views/admin/settingsForm.html.php';
        return $this->render($template, array(
                    'withSubmit' => !$post,
                    'postTypeName' => $postType->name,
                    'sections' => $sections,
                    'success' => $success
        ));
    }

    /**
     * Wrap a section into metaboes
     * @param $post
     * @param array $metaboxData
     * @throws RuntimeException
     */
    public function settingsBoxes($post, array $metaboxData)
    {
        echo $this->settingsSection($metaboxData['args'][0], $metaboxData['args'][1], $post);
    }

    /**
     * 
     */
    public function handlesSettingsSection($postId = null)
    {
        $request = filter_input_array(INPUT_POST);
        if ($request) {
            foreach ($request as $key => $postTypeRequest) {
                if (preg_match('/' . $this->container->getSlug() . '_posttype/', $key)) {
                    if (is_array($postTypeRequest)) {
                        //echo "<pre>" . print_r($postTypeRequest, true) . "</pre>";
                        $isTokenValid = wp_verify_nonce($postTypeRequest['token'], 'fawd-token');
                        if ($isTokenValid) {
                            $likeButton = new LikeButton();
                            $likeButton->bind($postTypeRequest);
                            $likeButtonPostType = new LikeButtonPostType();
                            $likeButtonPostType->bind($postTypeRequest);
                            $likeButtonPostType->setLikeButton($likeButton);

                            //save meta to post if redefine is used.
                            if (isset($postTypeRequest['redefine']) && $postId) {
                                if ($postTypeRequest['redefine'] == true) {
                                    update_post_meta($postId, $this->container->getSlug() . '_posttype', $likeButtonPostType);
                                } else {
                                    delete_post_meta($postId, $this->container->getSlug() . '_posttype');
                                }
                                return;
                            }

                            $postTypeName = str_replace($this->container->getSlug() . '_posttype_', '', $key);
                            $om = $this->container->getRoot()->get('services.option_manager');
                            $om->save('options.' . $this->container->getSlug() . '.' . $postTypeName, $likeButtonPostType);
                            $om->save('fawd_application_' . $this->container->getSlug() . '_success_' . $postTypeName, 'Settings were updated with success');
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
