<?php

/**
 * Facebook AWD
 *
 * This file is part of the facebook AWD package
 * 
 */

namespace AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model;

use AHWEBDEV\Framework\Model\Model;

/**
 * FacebookAWD Like Button LikeButtonPostType Model
 *
 * This file is the LikeButtonPostType Model
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package   FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonPostType extends Model
{

    /**
     * The position top
     */
    const POSITION_TOP = 'top';

    /**
     * The position bottom
     */
    const POSITION_BOTTOM = 'bottom';

    /**
     * The position top & bottom
     */
    const POSITION_BOTH = 'both';

    /**
     * Enable
     * 
     * @var boolean
     */
    protected $enable = false;

    /**
     * Position
     * 
     * @var string
     */
    protected $position = self::POSITION_TOP;

    /**
     * LikeButton
     * 
     * The like button associated with the post type
     * 
     * @var \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    protected $likeButton;

    /**
     * Redefine
     * 
     * Determine if this post configuration is redefined from post
     * If yes, this config will be used.
     * 
     * @var boolean 
     */
    protected $redefine = false;

    /**
     * Get the like button
     * 
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function getLikeButton()
    {
        return $this->likeButton;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->likeButton = new \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton();
    }

    /**
     * Set the like button
     * 
     * @param  \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton         $likeButton
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setLikeButton(\AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton $likeButton)
    {
        $this->likeButton = $likeButton;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormConfig()
    {
        return array(
            'enable' => array(
                'type' => 'select',
                'label' => 'Enable',
                'help' => 'Display or hide the like button',
                'options' => array(
                    array('value' => 0, 'label' => 'No'),
                    array('value' => 1, 'label' => 'Yes')
                )
            ),
            'redefine' => array(
                'name' => 'redefine',
                'type' => 'select',
                'label' => 'Redefine global settings',
                'help' => 'Display or hide the like button',
                'options' => array(
                    array('value' => 0, 'label' => 'No'),
                    array('value' => 1, 'label' => 'Yes')
                )
            ),
            'position' => array(
                'type' => 'select',
                'label' => 'Where ?',
                'help' => 'The position of the like button',
                'options' => array(
                    array('value' => 'top', 'label' => 'Before content'),
                    array('value' => 'bottom', 'label' => 'After content'),
                    array('value' => 'both', 'label' => 'Before & after content'),
                )
            ),
        );
    }

    /**
     * Get enable
     * 
     * @return boolean
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Get position
     * 
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set enable
     * 
     * @param boolean $enable
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Set position
     * 
     * @param boolean $position
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get redefine
     * 
     * @return boolean
     */
    public function getRedefine()
    {
        return $this->redefine;
    }

    /**
     * Set redefine
     * 
     * @param boolean $redefine
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setRedefine($redefine)
    {
        $this->redefine = $redefine;

        return $this;
    }

}
