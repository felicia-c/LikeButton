<?php

/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller;

use AHWEBDEV\FacebookAWD\Controller\AdminMenuController as BaseController;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType;
use AHWEBDEV\Framework\Model\Model;
use AHWEBDEV\Framework\TemplateManager\Form;
use AHWEBDEV\Wordpress\Admin\MetaboxInterface;
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
        return $this->container->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuTitle()
    {
        return '<img src="' . plugins_url(null, __DIR__) . '/Resources/public/img/facebook_like_thumb.png' . '" style="float: left; width:15px; margin-right: 10px;" class="alig"> '
                . preg_replace('/' . $this->container->getRoot()->getTitle() . '/', '', $this->container->getTitle());
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        add_action('wp_ajax_save_settings_' . $this->container->getSlug(), array($this, 'handlesSettingsSection'));
        add_action('wp_ajax_shortcode_generator_' . $this->container->getSlug(), array($this, 'handlesShortcodeSection'));

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

        $assets['script'][$this->container->getSlug() . 'likebuttonTinyMce'] = array(
            'src' => $publicUrl . '/js/likebuttonTinyMce.js',
            'deps' => array($parentSlug . 'admin')
        );

        //enqueue this script.
        $pageHook = $this->admin->getAdminMenuHook($this->container->getSlug());
        add_action('admin_print_scripts-' . $pageHook, array($this, 'enqueueScripts'));
        add_action('widgets_init', array($this, 'registerWidgets'));

        //add the tinyMce plugins for shortcode generator
        add_action('init', array($this, 'shortcodeGeneratorTinyMceButton'));
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
            add_meta_box($this->container->getSlug() . $postType->name, 'Display on ' . strtolower($postType->labels->name), array($this, 'settingsBoxes'), $pageHook, 'normal', 'default', array($postType));
            add_meta_box($this->container->getSlug() . $postType->name, 'Like Button Settings', array($this, 'settingsBoxes'), $postType->name, 'normal', 'default', array($postType));

            //post type pages
            add_action('admin_print_styles-post.php', array($this->admin, 'enqueueStyles'));
            add_action('admin_print_styles-post.php', array($this->admin, 'enqueueScripts'));
            add_action('save_post', array($this, 'handlesSettingsSection'));
            add_action('edit_attachment', array($this, 'handlesSettingsSection'));
        }

        //shortcode usage
        $shortcodeString = $this->handlesShortcodeSection();
        add_meta_box($this->container->getSlug() . '_shortcode', 'Shortcode generator', array($this, 'shortcodeBox'), $pageHook, 'normal', 'default', array($shortcodeString));
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
        $application = $this->container->getRoot()->get('services.option_manager')->get('options.application');
        $template = $this->container->getRoot()->getRootPath() . '/Resources/views/admin/metaboxes.html.php';
        echo $this->render($template, array(
            'title' => $this->container->getTitle() . ' <a class="button-secondary" href="?page=' . $this->container->getRoot()->getSlug() . '">Back</a>',
            'application' => $application,
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

        $likeButtonPostType = $this->om->get($this->container->getSlug() . '.' . $postType->name);
        if (!is_object($likeButtonPostType)) {
            $likeButtonPostType = new LikeButtonPostType();
        }

        $form = new Form($this->container->getSlug());

        $success = $this->om->get($this->container->getSlug() . '_' . $postType->name . '_success');
        $this->om->set($this->container->getSlug() . '_' . $postType->name . '_success', false);

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
                            $this->om->set($this->container->getSlug() . '.' . $postTypeName, $likeButtonPostType);
                            $this->om->set($this->container->getSlug() . '_' . $postTypeName . '_success', 'Settings were updated with success');
                            if ($this->isAjaxRequest()) {
                                $template = $this->container->getRoot()->getRootPath() . '/Resources/views/ajax/ajax.json.php';
                                echo $this->render($template, array(
                                    'sectionClass' => $postTypeName,
                                    'section' => $this->settingsSection(get_post_type_object($postTypeName))
                                ));
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Wrap a section into a metabox 
     */
    public function shortcodeBox($post, array $metaboxData)
    {
        $shortcode = $metaboxData['args'][0];
        echo $this->shortcodeSection(null, $shortcode);
    }

    public function shortcodeSection($likebutton = null, $shortcode = null)
    {
        $success = false;
        $sections = array();
        if (!$likebutton) {
            $likebutton = new LikeButton();
        }
        $form = new Form($this->container->getSlug());
        if ($shortcode) {
            $success = "Shortcode generated!";
            $sections['shortcode'] = '<h4 class="text-primary">Shortcode</h4><div class="well well-xs"><samp>' . $shortcode . '</samp></div>';
            $sections['preview'] = '<h4 class="text-primary">Preview</h4><div class="well well-xs">' . do_shortcode($shortcode) . '</div>';
        }
        $sections['likebutton'] = $form->processFields('shortcode_generator', $likebutton->getFormConfig());
        $sections['security'] = $form->processFields('shortcode_generator', $this->container->getRoot()->getTokenFormConfig());
        $data = array('classes' => 'shortcode_section section ',
            'withSubmit' => 'Generate the shortcode',
            'sections' => $sections,
            'success' => $success,
        );

        $template = $this->container->getRoot()->getRootPath() . '/Resources/views/admin/settingsForm.html.php';
        return $this->render($template, $data);
    }

    public function handlesShortcodeSection()
    {
        $request = filter_input_array(INPUT_POST);
        if (isset($request[$this->container->getSlug() . 'shortcode_generator'])) {
            $shortCodeGeneratorRequest = $request[$this->container->getSlug() . 'shortcode_generator'];
            $likebutton = new LikeButton();
            $likebutton->bind($shortCodeGeneratorRequest);
            $shortcodeString = $this->shortCodeGenerator($likebutton);
            if ($this->isAjaxRequest()) {
                $template = $this->container->getRoot()->getRootPath() . '/Resources/views/ajax/ajax.json.php';
                echo $this->render($template, array(
                    'sectionClass' => 'shortcode_section',
                    'section' => $this->shortcodeSection($likebutton, $shortcodeString),
                    'shortcode' => $shortcodeString
                ));
                exit;
            }
            return $shortcodeString;
        }
    }

    /**
     * Generate a shortcode from the LikeButton object
     * 
     * @param LikeButton $likebutton
     * @return string
     */
    public function shortCodeGenerator(Model $object)
    {
        $reflector = new \ReflectionClass(get_class($object));
        $shortcodeTmp = '';
        foreach ($reflector->getProperties() as $property) {
            $method = 'get' . ucfirst($property->getName());
            $value = null;
            if ($reflector->hasMethod($method)) {
                $value = call_user_func(array($object, $method));
                if ($value === false) {
                    $value = '0';
                }
                $shortcodeTmp .= $property->getName() . '="' . $value . '" ';
            }
        }
        $shortcode = '[' . $this->container->getSlug() . ' ' . rtrim($shortcodeTmp) . ']';
        return $shortcode;
    }

    public function shortcodeGeneratorTinyMceButton()
    {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array($this, 'registerShortcodeGeneratorTinyMcePlugin'));
            add_filter('mce_buttons', array($this, 'addShortcodeGeneratorTinyMceButton'));
        }
    }

    public function registerShortcodeGeneratorTinyMcePlugin($plugins)
    {
        $plugins['facebookawdlikebutton_shortcode_generator'] = plugins_url(null, __DIR__) . '/Resources/public/js/likeButtonTinyMce.js';
        return $plugins;
    }

    public function addShortcodeGeneratorTinyMceButton($buttons)
    {
        $buttons[] = 'facebookawdlikebutton_shortcode_generator';
        return $buttons;
    }

}
