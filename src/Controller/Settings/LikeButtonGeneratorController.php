<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace FacebookAWD\Plugin\LikeButton\Controller\Settings;

use FacebookAWD\Plugin\LikeButton\Service\LikeButtonManager;
use PopCode\Framework\ContainerInterface;
use PopCode\Framework\Form\AbstractForm;
use PopCode\Framework\Form\FormFactory;
use PopCode\Framework\Form\FormType;
use PopCode\Wordpress\Admin\AjaxInterface;
use PopCode\Wordpress\Controller\Controller;

/**
 * FacebookAWD LikeButton LikeButtonGeneratorController.
 *
 * This file is the LikeButtonGeneratorController settings controller
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@pop-code.fr>
 */
class LikeButtonGeneratorController extends Controller implements AjaxInterface
{

    /**
     * @var AbstractForm
     */
    protected $generator;

    /**
     * @var LikeButtonManager
     */
    protected $likeButtonManager;

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
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $formType = new FormType();
        $formType->setPtd($this->container->getPtd());
        $formType->setEntity('FacebookAWD\\Plugin\\LikeButton\\Model\\LikeButton');
        $this->generator = FormFactory::createForm($this->container->getSlug() . '_generator', $formType);
        $this->generator->build();
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerAjaxHook()
    {
        add_action('wp_ajax_generator_' . $this->container->getSlug(), array($this, 'handleGeneratorSection'));
    }

    /**
     * Add meta box
     */
    public function addMetaboxes($pageHook)
    {
        add_meta_box($this->container->getSlug() . '_shortcode', $this->tm->_('Like Button Generator'), array($this, 'generatorSection'), $pageHook, 'normal', 'default');
    }

    /**
     * The generator section.
     *
     * @return string
     */
    public function generatorSection()
    {
        $data = array(
            'form' => $this->generator,
            'beforeForm' => $this->getListenerResponse(),
            'withSubmit' => 'Generate',
            'success' => $this->om->getFlash($this->container->getSlug() . '.generator.success')
        );
        
        return $this->render($this->container->getRoot()->getRootPath() . '/Resources/views/admin/settingsForm.html.php', $data, func_num_args());
    }

    /**
     * Handle the shortcode genrator section.
     * if request is made via ajax, a json encoded string is echoed
     *
     * @return string  
     */
    public function handleGeneratorSection()
    {
        if ($this->isMethod('POST') && $this->generator->hasRequest()) {
            $this->removeCache('generatorSection');
            $this->generator->bind(filter_input_array(INPUT_POST));
            if ($this->generator->isValid()) {
                $likeButton = $this->generator->getData();
                $this->setListenerResponse($this->render($this->container->getRoot()->getRootPath() . '/Resources/views/admin/generator.html.php', array(
                            'phpCode' => $this->likeButtonManager->generatePhpCode($likeButton),
                            'shortcode' => $this->likeButtonManager->generateShorcode($likeButton),
                            'preview' => $this->likeButtonManager->renderButton($likeButton)
                )));
            }
            if ($this->isAjaxRequest()) {
                $this->renderJson(array('view' => $this->generatorSection()), true);
                exit;
            }
        }
    }

}
