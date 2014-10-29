<?php


/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Controller;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType;
use AHWEBDEV\FacebookAWD\Controller\Controller as BaseController;
use WP_Post;

/**
 * FacebookAWD Like Button FrontController
 *
 * This file is the front controller
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package   FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class FrontController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $assets = $this->container->getRoot()->getAssets();

        $publicUrl = plugins_url(null, __DIR__) . '/Resources/public';
        $assets['script'][$this->container->getSlug()] = array(
            'src' => $publicUrl . '/js/likeButton.js',
            'deps' => array($this->container->getRoot()->getSlug())
        );
        $this->container->getRoot()->setAssets($assets);

        //front end hooks
        add_filter('the_content', array($this, 'addLikeButton'));
        //add_filter('prepend_attachment', array($this, 'addLikeButton'));
        add_filter('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_shortcode($this->container->getSlug(), array($this, 'shortcodeLikeButton'));
    }

    /**
     * Enqueue assets
     */
    public function enqueueScripts()
    {
        $this->container->getRoot()->registerAssets();
        wp_enqueue_script($this->container->getSlug());
        wp_enqueue_script($this->container->getRoot()->getSlug());
    }

    /**
     * Helpers to get a likeButton from postType configuration
     * 
     * @param  WP_Post                    $post
     * @return boolean|LikeButtonPostType
     */
    public function getLikeButtonPostTypeFromPost($post, $config = array())
    {
        $postType = get_post_type_object($post->post_type);
        //get the configuation
        $likeButtonPosType = $this->om->get('options.' . $this->container->getSlug() . '.' . $postType->name);
        $likeButtonPostTypeFromPost = get_post_meta($post->ID, $this->container->getSlug() . '_posttype', true);

        if (is_object($likeButtonPostTypeFromPost)) {
            $likeButtonPosType = $likeButtonPostTypeFromPost;
            unset($likeButtonPostTypeFromPost);
        }
        if (empty($likeButtonPosType)) {
            return false;
        }

        //allow the config to be overided
        $likeButtonPosType->bind($config);

        return $likeButtonPosType;
    }

    /**
     * Render the Like Button
     * 
     * @param  LikeButton $likeButton
     * @return string
     */
    public function renderLikeButton(LikeButton $likeButton)
    {
        $template = $this->container->getRootPath() . '/Resources/views/likeButton.html.php';

        return $this->render($template, array('likeButton' => $likeButton));
    }

    /**
     * Parse Like Button the shortcode
     * 
     * @param  LikeButton $likeButton
     * @return string
     */
    public function shortcodeLikeButton($options, $content = null)
    {
        //to camelcase convert for model binding.
        $configs = array();
        foreach ($options as $key => $value) {
            $configs[self::underscoreToCC($key)] = $value;
        }
        if (isset($configs['singularOnly']) && !is_singular()) {
            return;
        }
        $likeButton = new LikeButton();
        $likeButton->bind($configs);

        return $this->renderLikeButton($likeButton);
    }

    /**
     * Adds the like buntton on content
     * 
     * @param  string $content
     * @return string
     */
    public function addLikeButton($content)
    {
        if (!is_singular()) {
            return $content;
        }

        $post = get_post();
        $likeButtonPosType = $this->getLikeButtonPostTypeFromPost($post);

        if (!$likeButtonPosType) {
            return $content;
        }

        if (!$likeButtonPosType->getEnable()) {
            return $content;
        }

        //render the likebutton
        $html = $this->renderLikeButton($likeButtonPosType->getLikeButton());
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

    /**
     * Convert underscored string by camel case
     * 
     * @todo Move this method to an utility class
     * @param string $string
     * @return string
     */
    public static function underscoreToCC($string)
    {
        return lcfirst(preg_replace("/ /", "", ucwords(preg_replace("/_/", " ", $string))));
    }

}
