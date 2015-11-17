<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace FacebookAWD\Plugin\LikeButton\Model;

use PopCode\Framework\Model\Model;

/**
 * FacebookAWD Like Button LikeButtonPostType Model.
 *
 * This file is the LikeButtonPostType Model
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButtonPostType extends Model
{

    /**
     * The position top.
     */
    const POSITION_TOP = 'top';

    /**
     * The position bottom.
     */
    const POSITION_BOTTOM = 'bottom';

    /**
     * The position top & bottom.
     */
    const POSITION_BOTH = 'both';

    /**
     * Enable.
     *
     * @var boolean
     * @FormType(
     *              help="Display or hide the like button.",
     *              type="radio",
     *              options="boolean",
     *              attrs={"class":"hideIfOn", "data-hide-on": ".likeButton-group, .position-group" }
     *              )
     */
    protected $enable = false;

    /**
     * Position.
     *
     * @var string
     * @FormType(
     *             label="Where ?",
     *             help="The position of the like button.",
     *             type="select",
     *             options={
     *             {"value": "top", "label": "Before content"},
     *             {"value": "bottom", "label": "After content"},
     *             {"value": "both", "label": "Before & After content"}
     *             }
     *             )
     */
    protected $position = self::POSITION_TOP;

    /**
     * Redefine.
     *
     * Determine if this post configuration is redefined from post
     * If yes, this config will be used.
     *
     * @var boolean
     * @FormType(
     *              label="Redefine global settings",
     *              help="You can redefine the global settings if you select Yes",
     *              type="radio",
     *              options="boolean",
     *              attrs={"class":"hideIfOn", "data-hide-on": ".section_likeButtonPostTypeOptions" }
     *              )
     */
    protected $redefine = false;

    /**
     * LikeButton.
     *
     * The like button associated with the post type
     *
     * @var \FacebookAWD\Plugin\LikeButton\Model\LikeButton
     * @FormType(
     *    label=false,
     *    type="form",
     *    entity="\FacebookAWD\Plugin\LikeButton\Model\LikeButton"
     * )
     */
    protected $likeButton;

    /**
     * Get the like button.
     *
     * @return \FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function getLikeButton()
    {
        return $this->likeButton;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->likeButton = new \FacebookAWD\Plugin\LikeButton\Model\LikeButton();
    }

    /**
     * Set the like button.
     *
     * @param \FacebookAWD\Plugin\LikeButton\Model\LikeButton $likeButton
     *
     * @return \FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setLikeButton(\FacebookAWD\Plugin\LikeButton\Model\LikeButton $likeButton)
    {
        $this->likeButton = $likeButton;

        return $this;
    }

    /**
     * Get enable.
     *
     * @return boolean
     */
    public function getEnable()
    {
        return (bool) $this->enable;
    }

    /**
     * Get position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set enable.
     *
     * @param boolean $enable
     *
     * @return \FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setEnable($enable)
    {
        $this->enable = (bool) $enable;

        return $this;
    }

    /**
     * Set position.
     *
     * @param boolean $position
     *
     * @return \FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get redefine.
     *
     * @return boolean
     */
    public function getRedefine()
    {
        return $this->redefine;
    }

    /**
     * Set redefine.
     *
     * @param boolean $redefine
     *
     * @return \FacebookAWD\Plugin\LikeButton\Model\LikeButtonPostType
     */
    public function setRedefine($redefine)
    {
        $this->redefine = $redefine;

        return $this;
    }

}
