<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Service\LikeButtonManager;
use AHWEBDEV\Framework\ContainerInterface;
use AHWEBDEV\Framework\TemplateManager\Form;
use AHWEBDEV\Wordpress\Admin\AdminInterface;
use AHWEBDEV\Wordpress\Admin\AjaxInterface;
use AHWEBDEV\Wordpress\Admin\MetaboxInterface;
use AHWEBDEV\Wordpress\Admin\TinyMceInterface;
use AHWEBDEV\Wordpress\Controller\AdminMenuController;
use AHWEBDEV\Wordpress\Shortcode\Shortcode;
use InvalidArgumentException;

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
     * @var LikeButtonManager
     */
    protected $likeButtonManager;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param AdminInterface     $admin
     * @param LikeButtonManager  $likeButtonManager
     */
    public function __construct(ContainerInterface $container, AdminInterface $admin, LikeButtonManager $likeButtonManager)
    {
        parent::__construct($container, $admin);
        $this->likeButtonManager = $likeButtonManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuTitle()
    {
        $iconUrl = plugins_url(null, __DIR__).'/Resources/public/img/facebook_like_thumb.png';

        return '<img src="'.$iconUrl.'" style="float: left; width:15px; margin-right: 6px;"> '.parent::getMenuTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function registerAjaxHook()
    {
        add_action('wp_ajax_save_settings_'.$this->container->getSlug(), array($this, 'handlesSettingsPostTypeSection'));
        add_action('wp_ajax_generator_'.$this->container->getSlug(), array($this, 'handlesGeneratorSection'));
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        //add the related assets js and css
        $this->addAssets();

        //enqueue this script.
        $pageHook = $this->admin->getAdminMenuHook($this->container->getSlug());
        add_action('admin_print_scripts-'.$pageHook, array($this, 'enqueueScripts'));
    }

    /**
     * Add the assets on the container.
     */
    public function addAssets()
    {
        //assets configurations
        $publicUrl = plugins_url(null, __DIR__).'/Resources/public';
        $parentSlug = $this->container->getRoot()->getSlug();
        $assets = $this->container->getRoot()->getAssets();
        $assets['script'][$this->container->getSlug().'admin'] = array(
            'src' => $publicUrl.'/js/admin.js',
            'deps' => array($parentSlug.'admin'),
        );
        $assets['script'][$parentSlug.'admin-init']['deps'][] = $this->container->getSlug().'admin';
        $this->container->getRoot()->setAssets($assets);
    }

    /**
     * Enqueue assets.
     */
    public function enqueueScripts()
    {
        wp_enqueue_script($this->container->getSlug().'admin');
    }

    /**
     * {@inheritdoc}
     */
    public function adminMenu()
    {
        parent::adminMenu();

        //add the like button boxes to the post.php also
        add_action('load-post.php', array($this, 'loadPageHook'));
    }

    /**
     * {@inheritdoc}
     */
    public function addMetaBoxes($pageHook)
    {
        $adminController = $this->container->getRoot()->get('controller.backend');
        $adminController->addDefaultMetaBoxes($pageHook);

        //post types metaboxes
        $postTypes = get_post_types(array('public' => true), 'objects');
        foreach ($postTypes as $postType) {
            //facebookawd admin
            add_meta_box($this->container->getSlug().$postType->name, 'Display on '.strtolower($postType->labels->name), array($this, 'settingsPostTypeBoxes'), $pageHook, 'normal', 'default', array($postType));
            add_meta_box($this->container->getSlug().$postType->name, 'Like Button Settings', array($this, 'settingsPostTypeBoxes'), $postType->name, 'normal', 'default', array($postType));
        }
        //post type pages
        add_action('admin_print_styles-post.php', array($this->admin, 'enqueueStyles'));
        add_action('admin_print_scripts-post.php', array($this->admin, 'enqueueScripts'));
        add_action('save_post', array($this, 'handlesSettingsPostTypeSection'));
        add_action('edit_attachment', array($this, 'handlesSettingsPostTypeSection'));

        //shortcode usage
        $shortcodeString = $this->handlesGeneratorSection();
        add_meta_box($this->container->getSlug().'_generator', 'Like Button Generator', array($this, 'generatorBox'), $pageHook, 'normal', 'default', array($shortcodeString));
    }

    /**
     * Create the index page.
     */
    public function index()
    {
        $this->handlesSettingsPostTypeSection();
        echo $this->render($this->container->getRoot()->getRootPath().'/Resources/views/admin/metaboxes.html.php', array(
            'title' => $this->container->getTitle().' <a class="button-secondary" href="?page='.$this->container->getRoot()->getSlug().'">Back</a>',
            'args' => array(),
            'boxes' => array(
                array(
                    'type' => 'do_meta_boxes',
                    'context' => 'normal',
                ),
            ),
            'boxesSide' => array(
                array(
                    'type' => 'do_meta_boxes',
                    'context' => 'side',
                ),
            ),
        ));
    }

    /**
     * Wrap a section into a metabox.
     *
     * @param $post
     * @param array $metaboxData
     */
    public function settingsPostTypeBoxes($post, array $metaboxData)
    {
        echo $this->settingsPostTypeSection($metaboxData['args'][0], $post);
    }

    /**
     * Render the like Button settgins section.
     *
     * This method is called by meta boxes in admin of plugin
     * and in the post types edition pages
     *
     * @param  $postType
     * @param  $post
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function settingsPostTypeSection($postType, $post = null)
    {
        if (!$postType) {
            throw new InvalidArgumentException('The $postType arg is required for settingsSection');
        }
        $likeButtonPostType = null;
        $form = new Form($this->container->getSlug());
        $sections = array();

        //if we are on a post
        if ($post) {
            // try to get instance from post meta.
            $likeButtonPostType = get_post_meta($post->ID, $this->container->getSlug().'_posttype', true);
        }

        //post type not exists on post
        if (!$likeButtonPostType) {
            //get it from global settings
            $likeButtonPostType = $this->om->get($this->container->getSlug().'.'.$postType->name);
            //create it
            if (!is_object($likeButtonPostType)) {
                $likeButtonPostType = new LikeButtonPostType();
            }
        }

        //get the form config
        $likeButtonPostTypeFormConfig = $likeButtonPostType->getFormConfig();

        //if on post set the redefine section
        if ($post) {
            $sections['redefine'] = $form->processFields('posttype_'.$postType->name, array('redefine' => $likeButtonPostTypeFormConfig['redefine']));
        }

        $sections['likeButtonPostTypeOptions'] = array('enable' => $form->processFields('posttype_'.$postType->name, array('enable' => $likeButtonPostTypeFormConfig['enable'])));
        //remove this field, he is only required on post edition and already rendered
        unset($likeButtonPostTypeFormConfig['redefine']);
        unset($likeButtonPostTypeFormConfig['enable']);

        $sections['likeButtonPostTypeOptions']['likeButtonPostType'] = $form->processFields('posttype_'.$postType->name, $likeButtonPostTypeFormConfig);
        $sections['security'] = $form->processFields('posttype_'.$postType->name, $this->container->getRoot()->getTokenFormConfig());

        $success = $this->om->get($this->container->getSlug().'_'.$postType->name.'_success');
        $this->om->set($this->container->getSlug().'_'.$postType->name.'_success', false);

        return $this->render($this->container->getRoot()->getRootPath().'/Resources/views/admin/settingsForm.html.php', array('classes' => 'posttype_section section '.$postType->name,
                    'withSubmit' => !$post,
                    'postTypeName' => $postType->name,
                    'sections' => $sections,
                    'success' => $success,
        ));
    }

    /**
     * Handle the post of the settings section.
     *
     * This method works for each post types.
     * The configuration will be save on post directly (if set from post/page editor)
     * Or saved into the plugin configuration related to each post type.
     *
     * @param integer|null $postId
     */
    public function handlesSettingsPostTypeSection($postId = null)
    {
        $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        if ($request) {
            foreach ($request as $key => $postTypeRequest) {
                if (preg_match('/'.$this->container->getSlug().'posttype/', $key) && is_array($postTypeRequest)) {
                    if (wp_verify_nonce($postTypeRequest['token'], 'fawd-token')) {
                        $likeButtonPostType = new LikeButtonPostType();
                        $likeButtonPostType->bind($postTypeRequest);
                        /* $likeButton = $likeButtonPostType->getLikeButton();
                          $likeButton->bind($postTypeRequest);
                         */
                        //save meta to post if redefine is used.
                        if ($postId && isset($postTypeRequest['redefine'])) {
                            if ($postTypeRequest['redefine'] == true) {
                                update_post_meta($postId, $this->container->getSlug().'_posttype', $likeButtonPostType);
                            } else {
                                delete_post_meta($postId, $this->container->getSlug().'_posttype');
                            }

                            return;
                        }

                        $postTypeName = str_replace($this->container->getSlug().'posttype_', '', $key);
                        $this->om->set($this->container->getSlug().'.'.$postTypeName, $likeButtonPostType);
                        $this->om->set($this->container->getSlug().'_'.$postTypeName.'_success', 'Settings were updated with success');
                        if ($this->isAjaxRequest()) {
                            echo $this->render($this->container->getRoot()->getRootPath().'/Resources/views/ajax/ajax.json.php', array(
                                'sectionClass' => $postTypeName,
                                'section' => $this->settingsPostTypeSection(get_post_type_object($postTypeName)),
                            ));
                            exit;
                        }
                    }
                }
            }
        }
    }

    /**
     * Wrap the generator section.
     *
     * @param $post
     * @param array $metaboxData
     */
    public function generatorBox($post, array $metaboxData)
    {
        $shortcode = $metaboxData['args'][0];
        echo $this->generatorSection(null, $shortcode);
    }

    /**
     * Get the generator section.
     *
     * @param LikeButton $likebutton
     * @param string     $shortcode
     *
     * @return string
     */
    public function generatorSection(LikeButton $likebutton = null, $shortcode = null)
    {
        $success = false;
        $sections = array();
        if (!$likebutton) {
            $likebutton = $this->likeButtonManager->create();
        }
        if ($shortcode) {
            $sections['phpcode'] = $this->render($this->container->getRoot()->getRootPath().'/Resources/views/admin/phpCode.code.php', array(
                'shortcode' => $shortcode,
                'likebuttonCode' => implode("\n", $this->likeButtonManager->generatePhpCode($likebutton)),
            ));
            $sections['shortcode'] = '<h4 class="text-primary">Using shortcode</h4><pre class="prettyprint">'.$shortcode.'</pre>';
            $sections['preview'] = '<h4 class="text-primary">Preview</h4><div class="well well-xs">'.do_shortcode($shortcode).'</div>';
            $success = "Code generated!";
        }

        $form = new Form($this->container->getSlug());
        $sections['likebutton'] = $form->processFields('shortcode_generator', $likebutton->getFormConfig());
        $sections['security'] = $form->processFields('shortcode_generator', $this->container->getRoot()->getTokenFormConfig());
        $data = array('classes' => 'shortcode_section_likebutton section ',
            'withSubmit' => 'Generate the Like Button',
            'sections' => $sections,
            'success' => $success,
        );

        return $this->render($this->container->getRoot()->getRootPath().'/Resources/views/admin/settingsForm.html.php', $data);
    }

    /**
     * Handle the generator section.
     *
     * @return string
     */
    public function handlesGeneratorSection()
    {
        $request = filter_input_array(INPUT_POST);
        if (isset($request[$this->container->getSlug().'shortcode_generator'])) {
            $shortCodeGeneratorRequest = $request[$this->container->getSlug().'shortcode_generator'];
            $likebutton = $this->likeButtonManager->create();
            $likebutton->bind($shortCodeGeneratorRequest);
            $shortcodeString = Shortcode::shortCodeGenerator($this->container->getSlug(), $likebutton);

            if ($this->isAjaxRequest()) {
                $template = $this->container->getRoot()->getRootPath().'/Resources/views/ajax/ajax.json.php';
                echo $this->render($template, array(
                    'sectionClass' => 'shortcode_section_likebutton',
                    'section' => $this->generatorSection($likebutton, $shortcodeString),
                    'shortcode' => $shortcodeString,
                ));
                exit;
            }

            return $shortcodeString;
        }
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
        $plugins[$this->container->getSlug().'_shortcode_generator'] = plugins_url(null, __DIR__).'/Resources/public/js/likeButtonTinyMce.js';

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
        $buttons[] = $this->container->getSlug().'_shortcode_generator';

        return $buttons;
    }
}
