<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Service\LikeButtonManager;
use AHWEBDEV\Framework\ContainerInterface;
use AHWEBDEV\Wordpress\Admin\ShortcodeInterface;
use AHWEBDEV\Wordpress\Admin\WidgetInterface;
use AHWEBDEV\Wordpress\Controller\Controller;
use AHWEBDEV\Wordpress\Shortcode\Shortcode;
use AHWEBDEV\Wordpress\Widget\Widget;

/**
 * FacebookAWD Like Button Controller.
 *
 * This file is the front controller
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonController extends Controller implements ShortcodeInterface, WidgetInterface
{
    /**
     * The likeButton manager.
     *
     * @var LikeButtonManager
     */
    protected $likeButtonManager;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param LikeButtonManager  $likeButtonManager
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
        parent::init();

        //add the assets likebutton
        $assets = $this->container->getRoot()->getAssets();
        $publicUrl = plugins_url(null, __DIR__).'/Resources/public';
        $assets['script'][$this->container->getSlug()] = array(
            'src' => $publicUrl.'/js/likeButton.js',
            'deps' => array($this->container->getRoot()->getSlug()),
        );
        $this->container->getRoot()->setAssets($assets);

        //add the like button to content
        add_filter('the_content', array($this, 'addLikeButton'));

        //add the required scripts
        add_filter('wp_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    /**
     * Enqueue assets.
     */
    public function enqueueScripts()
    {
        wp_enqueue_script($this->container->getSlug());
    }

    /**
     * Register widgets.
     *
     * @global $wp_widget_factory
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
            'selfCallback' => array($this->likeButtonManager, 'renderButton'),
            'preview' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function registerShortcodes()
    {
        $model = 'AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton';
        $likeButtonShortcode = new Shortcode($this->container->getSlug(), $model, array($this->likeButtonManager, 'renderButton'));
        $likeButtonShortcode->register();
    }

    /**
     * Adds the like buntton on content.
     *
     * @param string $content
     *
     * @return string
     */
    public function addLikeButton($content)
    {
        if (!is_singular()) {
            return $content;
        }

        $post = get_post();
        $likeButtonPosType = $this->likeButtonManager->getLikeButtonPostTypeFromPost($post);

        if (!$likeButtonPosType) {
            return $content;
        }

        if (!$likeButtonPosType->getEnable()) {
            return $content;
        }

        //render the likebutton
        $html = $this->likeButtonManager->renderButton($likeButtonPosType->getLikeButton());
        $contents = array(1 => $content);
        switch ($likeButtonPosType->getPosition()) {
            case $likeButtonPosType::POSITION_TOP:
                $contents[0] = $html;
                break;
            case $likeButtonPosType::POSITION_BOTTOM:
                $contents[2] = $html;
                break;
            case $likeButtonPosType::POSITION_BOTH:
                $contents[0] = $html;
                $contents[2] = $html;
                break;
        }
        ksort($contents);

        return implode('', $contents);
    }
}
