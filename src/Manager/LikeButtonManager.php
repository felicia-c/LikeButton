<?php

/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Manager;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton;
use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType;
use AHWEBDEV\Wordpress\Controller\Controller;
use ReflectionClass;

/**
 * FacebookAWD Like Button Manager
 *
 * This file is the Facebook LikeButton Manager
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package      FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonManager extends Controller
{

    /**
     * Init
     */
    public function init()
    {
        //silence
    }

    /**
     * Create a LikeButton instance
     * 
     * @return LikeButton
     */
    public function create()
    {
        return new LikeButton();
    }

    /**
     * Render the like button
     * 
     * @param LikeButton $likeButton
     * @return string
     */
    public function renderButton(LikeButton $likeButton)
    {
        $template = $this->container->getRootPath() . '/Resources/views/likeButton.html.php';
        return $this->render($template, array('likeButton' => $likeButton));
    }

    /**
     * Generate the php code to create a like button
     * 
     * @param LikeButton $likeButton
     * @return array
     */
    public function generatePhpCode(LikeButton $likeButton)
    {
        $code = array(
            '$manager = getFacebookAWD()->getPlugin(\'' . $this->container->getSlug() . '\')->get(\'manager\');',
            '$likeButton = $manager->create();'
        );
        $reflector = new ReflectionClass(get_class($likeButton));
        foreach ($reflector->getProperties() as $property) {
            $setterName = 'set' . ucfirst($property->getName());
            $getterName = 'get' . ucfirst($property->getName());
            $value = '';
            if ($reflector->hasMethod($getterName)) {
                $value = call_user_func(array($likeButton, $getterName));
            }
            if ($reflector->hasMethod($setterName) && $value != '') {
                $methodCall = $setterName . '(\'' . $value . '\');';
                $code[] = '$likeButton->' . $methodCall;
            }
        }
        $code[] = 'echo $manager->renderButton($likeButton);';
        return $code;
    }

    /**
     * Helpers to get a likeButton from postType configuration
     * 
     * @param  \WP_Post $post
     * @return boolean|LikeButtonPostType
     */
    public function getLikeButtonPostTypeFromPost($post, $config = array())
    {
        $postType = get_post_type_object($post->post_type);
        //get the configuation
        $likeButtonPosType = $this->om->get($this->container->getSlug() . '.' . $postType->name);
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

}
