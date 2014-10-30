<?php

/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Shortcode;

use AHWEBDEV\FacebookAWD\Plugin\LikeButton\Manager\LikeButtonManager;
use AHWEBDEV\Wordpress\Helper\Helper;
use AHWEBDEV\Wordpress\Shortcode\Shortcode;

/**
 * FacebookAWD Like Button Shortcode
 *
 * This file is the Like Button Shortcode class
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package      FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonShortcode extends Shortcode
{

    /**
     * The like button Manager
     * 
     * @var LikeButtonManager 
     */
    protected $likeButtonManager;

    /**
     * Constructor
     * 
     * @param string $slug
     * @param LikeButtonManager $likeButtonManager
     */
    public function __construct($slug, LikeButtonManager $likeButtonManager)
    {
        parent::__construct($slug);
        $this->likeButtonManager = $likeButtonManager;
    }

    /**
     * Render the like button shortcode
     * 
     * @param type $options
     * @param type $content
     * @return type
     */
    public function shortcode($options, $content = null)
    {
        $likeButton = $this->likeButtonManager->create();
        $configs = Helper::parseShortCodeConfig($options);
        if ($content) {
            $configs['content'] = $content;
        }
        $likeButton->bind($configs);
        return $this->likeButtonManager->renderButton($likeButton);
    }

}
