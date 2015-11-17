<?php

/**
 * Facebook AWD.
 *
 * This file is part of the facebook AWD package
 */

namespace FacebookAWD\Plugin\LikeButton\Model;

use PopCode\Framework\Model\Model;

/**
 * FacebookAWD Like Button Model.
 *
 * This file is the Facebook LikeButton Model
 *
 * @category     Extension
 *
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
 
class LikeButton extends Model
{

    /**
     * Href.
     *
     * @var string
     * @FormType(
     *             type="text",
     *             help="The absolute URL of the page that will be liked. (Empty = auto)"
     *             )
     */
    protected $href;

    /**
     * Action.
     *
     * @var string
     * @FormType(
     *             help="The verb to display on the button.",
     *             type="radio",
     *             options={
     *             {"value":"like", "label": "Like"},
     *             {"value":"recommend", "label": "Recommend"}
     *             }
     *             )
     */
    protected $action;

    /**
     * Layout.
     *
     * @var string
     * @FormType(
     *             help="Selects one of the different layouts that are available for the plugin.",
     *             type="select",
     *             options={
     *             {"value":"standard", "label": "Standard"},
     *             {"value":"button", "label": "Button"},
     *             {"value":"button_count", "label": "Button count"},
     *             {"value":"box_count", "label": "Box count"}
     *             }
     *             )
     */
    protected $layout;

    /**
     * Share.
     *
     * @var string
     * @FormType(
     *             help="Specifies whether to include a share button beside the Like button. This only works with the XFBML version.",
     *             type="radio",
     *             options="boolean"
     *             )
     */
    protected $share;

    /**
     * Width.
     *
     * @var string
     * @FormType(
     *             type="text",
     *             help="The width of the plugin. The layout you choose affects the minimum and default widths you can use."
     *             )
     */
    protected $width;

    /**
     * Colorscheme.
     *
     * @var string
     * @FormType(
     *
     *             help="The color scheme used by the plugin for any text outside of the button itself.",
     *             type="radio",
     *             options={
     *             {"value":"light", "label": "Light"},
     *             {"value":"dark", "label": "Dark"}
     *             }
     *             )
     */
    protected $colorscheme;

    /**
     * Kid directed site.
     *
     * @var string
     * @FormType(
     *             help="If your web site or online service, or a portion of your service is directed to children under 13 you must enable this.",
     *             type="radio",
     *             options="boolean"
     *             )
     */
    protected $kidDirectedSite;

    /**
     * Ref.
     *
     * @var string
     * @FormType(
     *             type="text",
     *             help="A label for tracking referrals which must be less than 50 characters and can contain alphanumeric characters and some punctuation (currently +/=-.:_).",
     *             attrs={
     *             "maxlength": 50,
     *             "class": "form-control"
     *             }
     *             )
     */
    protected $ref;

    /**
     * Show faces.
     *
     * @var string
     * @FormType(
     *             help="Specifies whether to display profile photos below the button (standard layout only). You must not enable this on child-directed sites.",
     *             type="radio",
     *             options="boolean"
     *             )
     */
    protected $showFaces;

    public function __construct()
    {
        $this->action = 'like';
        $this->colorscheme = 'light';
        $this->kidDirectedSite = false;
        $this->showFaces = false;
        $this->layout = 'standard';
    }

    /**
     * Get href.
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Get action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Get share.
     *
     * @return string
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Get action.
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get colorscheme.
     *
     * @return string
     */
    public function getColorscheme()
    {
        return $this->colorscheme;
    }

    /**
     * Get kid directed site.
     *
     * @return string
     */
    public function getKidDirectedSite()
    {
        return $this->kidDirectedSite;
    }

    /**
     * Get ref.
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Get show faces.
     *
     * @return string
     */
    public function getShowFaces()
    {
        return $this->showFaces;
    }

    /**
     * Set href.
     *
     * @param string $href
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Set action.
     *
     * @param string $action
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set layout.
     *
     * @param string $layout
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Set share.
     *
     * @param string $share
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setShare($share)
    {
        $this->share = (bool) $share;

        return $this;
    }

    /**
     * Set width.
     *
     * @param string $width
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set colorscheme.
     *
     * @param string $colorscheme
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setColorscheme($colorscheme)
    {
        $this->colorscheme = $colorscheme;

        return $this;
    }

    /**
     * Set Kid directed site.
     *
     * @param bool $kidDirectedSite
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setKidDirectedSite($kidDirectedSite)
    {
        $this->kidDirectedSite = (bool) $kidDirectedSite;

        return $this;
    }

    /**
     * Set ref.
     *
     * @param string $ref
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Set show faces.
     *
     * @param bool $showFaces
     *
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setShowFaces($showFaces)
    {
        $this->showFaces = (bool) $showFaces;

        return $this;
    }

}
