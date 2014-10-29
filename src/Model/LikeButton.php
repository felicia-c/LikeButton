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
 * FacebookAWD Like Button Model
 *
 * This file is the Facebook LikeButton Model
 * 
 * @subpackage   FacebookAWDLikeButton
 * @package   FacebookAWD
 * @category     Extension
 * @author       Alexandre Hermann <hermann.alexandre@ahwebdev.fr>
 */
class LikeButton extends Model
{

    /**
     * Href
     * 
     * @var string
     */
    protected $href;

    /**
     * Action
     * 
     * @var string
     */
    protected $action;

    /**
     * Layout
     * 
     * @var string
     */
    protected $layout;

    /**
     * Share
     * 
     * @var string
     */
    protected $share;

    /**
     * Width
     * 
     * @var string
     */
    protected $width;

    /**
     * Colorscheme
     *  
     * @var string
     */
    protected $colorscheme;

    /**
     * Kid directed site
     * 
     * @var string
     */
    protected $kidDirectedSite;

    /**
     * Ref
     * 
     * @var string
     */
    protected $ref;

    /**
     * Show faces
     * 
     * @var string
     */
    protected $showFaces;

    /**
     * Get href
     * 
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Get action
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get layout
     * 
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Get share
     * 
     * @return string
     */
    public function getShare()
    {
        return $this->share;
    }

    /**
     * Get action
     * 
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get colorscheme
     * 
     * @return string
     */
    public function getColorscheme()
    {
        return $this->colorscheme;
    }

    /**
     * Get kid directed site
     * 
     * @return string
     */
    public function getKidDirectedSite()
    {
        return $this->kidDirectedSite;
    }

    /**
     * Get ref
     * 
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Get show faces
     * 
     * @return string
     */
    public function getShowFaces()
    {
        return $this->showFaces;
    }

    /**
     * Set href
     * 
     * @param  string                                                   $href
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Set action
     * 
     * @param  string                                                   $action
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set layout
     * 
     * @param  string                                                   $layout
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Set share
     * 
     * @param  string                                                   $share
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setShare($share)
    {
        $this->share = (bool) $share;

        return $this;
    }

    /**
     * Set width
     * 
     * @param  string                                                   $width
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set colorscheme
     * 
     * @param  string                                                   $colorscheme
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setColorscheme($colorscheme)
    {
        $this->colorscheme = $colorscheme;

        return $this;
    }

    /**
     * Set Kid directed site
     * 
     * @param  boolean                                                   $kidDirectedSite
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setKidDirectedSite($kidDirectedSite)
    {
        $this->kidDirectedSite = (bool) $kidDirectedSite;

        return $this;
    }

    /**
     * Set ref
     * 
     * @param  string                                                   $ref
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Set show faces
     * 
     * @param  boolean                                                   $showFaces
     * @return \AHWEBDEV\FacebookAWD\Plugin\LikeButton\Model\LikeButton
     */
    public function setShowFaces($showFaces)
    {
        $this->showFaces = (bool) $showFaces;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormConfig()
    {
        $formConfig = array();
        foreach ($this as $key => $value) {
            $matches = null;
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $key, $matches);
            $name = ucfirst(strtolower(trim(implode(' ', $matches[0]))));
            $formConfig[$key] = array(
                'type' => 'text',
                'value' => $value,
                'label' => $name//transform to readable state here
            );
            $booleanProps = array('share', 'kidDirectedSite', 'showFaces');
            if (in_array($key, $booleanProps)) {
                $formConfig[$key]['type'] = 'select';
                $formConfig[$key]['options'] = array(
                    array('value' => 0, 'label' => 'No'),
                    array('value' => 1, 'label' => 'Yes')
                );
            }
        }

        $formConfig['href']['help'] = 'The absolute URL of the page that will be liked. (Empty = auto)';
        $formConfig['share']['help'] = 'Specifies whether to include a share button beside the Like button. ' .
                'This only works with the XFBML version.';
        $formConfig['width']['help'] = 'The width of the plugin. The layout you choose affects the minimum and default widths you can use, ';
        $formConfig['action']['help'] = 'The verb to display on the button.';
        $formConfig['action']['type'] = 'select';
        $formConfig['action']['options'] = array(
            array('value' => 'like', 'label' => 'Like'),
            array('value' => 'recommend', 'label' => 'Recommend')
        );
        $formConfig['showFaces']['help'] = 'Specifies whether to display profile photos below the button (standard layout only). ' .
                'You must not enable this on child-directed sites.';
        $formConfig['layout']['type'] = 'select';
        $formConfig['layout']['help'] = 'Selects one of the different layouts that are available for the plugin. ';
        $formConfig['layout']['options'] = array(
            array('value' => 'standard', 'label' => 'Standard'),
            array('value' => 'button_count', 'label' => 'Button count'),
            array('value' => 'button', 'label' => 'Button'),
            array('value' => 'box_count', 'label' => 'Box count')
        );
        $formConfig['colorscheme']['type'] = 'select';
        $formConfig['colorscheme']['help'] = 'The color scheme used by the plugin for any text outside of the button itself.';
        $formConfig['colorscheme']['options'] = array(
            array('value' => 'light', 'label' => 'Light'),
            array('value' => 'dark', 'label' => 'Dark')
        );

        $formConfig['ref']['attr'] = array('maxlength' => 50, 'class' => 'form-control');
        $formConfig['ref']['help'] = 'A label for tracking referrals which must be less than 50 characters and can contain alphanumeric ' .
                'characters and some punctuation (currently +/=-.:_).';

        $formConfig['kidDirectedSite']['help'] = 'If your web site or online service, or a portion of your service, ' .
                'is directed to children under 13 you must enable this';

        return $formConfig;
    }

}