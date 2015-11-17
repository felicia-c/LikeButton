<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace FacebookAWD\Plugin\LikeButton\Controller\Settings;

use FacebookAWD\Plugin\LikeButton\Service\LikeButtonManager;
use InvalidArgumentException;
use PopCode\Framework\ContainerInterface;
use PopCode\Framework\Form\FormFactory;
use PopCode\Framework\Form\FormType;
use PopCode\Wordpress\Admin\AjaxInterface;
use PopCode\Wordpress\Controller\Controller;

/**
 * FacebookAWD LikeButton LikeButtonPostTypeController.
 *
 * This file is the LikeButtonPostTypeController settings controller
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@pop-code.fr>
 */
class LikeButtonPostTypeController extends Controller implements AjaxInterface
{

    /**
     * @var LikeButtonManager
     */
    protected $likeButtonManager;

    /**
     * @var array
     */
    protected $forms;

    /**
     * Constructor
     * 
     * @param ContainerInterface $container
     * @param LikeButtonManager $likeButtonManager
     */
    public function __construct(ContainerInterface $container, LikeButtonManager $likeButtonManager)
    {
        parent::__construct($container);

        $this->likeButtonManager = $likeButtonManager;
        $this->forms = array();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $postTypes = get_post_types(array('public' => true), 'objects');
        unset($postTypes['attachment']);

        foreach ($postTypes as $postType) {
            //Init screen settings form
            $likeButtonPostType = $this->om->get($this->container->getSlug() . '.likebutton_' . $postType->name);
            $formType = new FormType();
            $formType->setPtd($this->container->getPtd());
            $formType->setEntity('FacebookAWD\\Plugin\\LikeButton\\Model\\LikeButtonPostType');
            $form = FormFactory::createForm($this->container->getSlug() . '_type_' . $postType->name, $formType, $likeButtonPostType);
            $form->build();

            $formTypePost = new FormType();
            $formTypePost->setType('hidden')->setMapped(false);
            $formPost = FormFactory::createForm('type', $formTypePost, $postType->name);

            $form->add('type', $formPost);
            $form->build();
            $this->forms[$postType->name] = $form;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerAjaxHook()
    {
        add_action('wp_ajax_save_post_type_settings_' . $this->container->getSlug(), array($this, 'handleSettingsPostTypeSection'));
    }

    /**
     * {@inheritdoc}
     */
    public function addMetaBoxes($pageHook)
    {
        //post types metaboxes
        $postTypes = get_post_types(array('public' => true), 'objects');
        unset($postTypes['attachment']);
        foreach ($postTypes as $postType) {
            add_meta_box($this->container->getSlug() . '_' . $postType->name, 'Display on ' . strtolower($postType->labels->name), array($this, 'settingsPostTypeSection'), $pageHook, 'normal', 'default', array($postType->name));
            add_meta_box($this->container->getSlug() . '_' . $postType->name, 'Display on ' . strtolower($postType->labels->name), array($this, 'settingsPostTypeSection'), $postType->name, 'normal', 'default', array($postType->name));
        }
    }

    /**
     * The post type settings
     * 
     * @param \WP_Post|null $post
     * @param array $args
     */
    public function settingsPostTypeSection($post, $args = array())
    {
        echo $this->renderPostTypeForm($args['args'][0], $post);
    }

    /**
     * Render a post type form
     * 
     * @param string $postTypeName
     * @param \WP_Post|null $post
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderPostTypeForm($postTypeName, $post = null)
    {
        $form = $this->forms[$postTypeName];
        if (!$form) {
            throw new InvalidArgumentException('The form ' . $args['args'][0] . ' is does not exist.');
        }
        //redefine form is only present on form edition page
        if (!$post) {
            $form->remove('redefine');
        }
        return $this->render($this->container->getRoot()->getRootPath() . '/Resources/views/admin/settingsForm.html.php', array(
                    'form' => $form,
                    //'beforeForm' => $this->getListenerResponse(),
                    'withSubmit' => true,
                    'success' => $this->om->getFlash($this->container->getSlug() . '.success.likebutton_' . $postTypeName)
        ));
    }

    /**
     * Handle the post type settings
     */
    public function handleSettingsPostTypeSection()
    {
        if ($this->isMethod('POST')) {
            foreach ($this->forms as $form) {
                if ($form->hasRequest()) {
                    $form->bind(filter_input_array(INPUT_POST));
                    if ($form->isValid()) {
                        $likeButtonPostType = $form->getData();
                        $postTypeName = $form->get('type')->getData();
                        $this->om->set($this->container->getSlug() . '.likebutton_' . $postTypeName, $likeButtonPostType);
                        $this->om->set($this->container->getSlug() . '.success.likebutton_' . $postTypeName, 'Settings were updated with success.');
                    }
                    if ($this->isAjaxRequest()) {
                        $this->renderJson(array('view' => $this->renderPostTypeForm($postTypeName)), true);
                        exit;
                    }
                }
            }
        }
    }

}
