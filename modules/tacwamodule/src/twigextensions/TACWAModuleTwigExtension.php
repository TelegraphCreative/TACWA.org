<?php
/**
 * TACWA module for Craft CMS 3.x
 *
 * TACWA PHP file
 *
 * @link      http://tacwa.org
 * @copyright Copyright (c) 2019 telegraph
 */

namespace modules\tacwamodule\twigextensions;

use modules\tacwamodule\TACWAModule;

use Craft;

/**
 * @author    telegraph
 * @package   TACWAModule
 * @since     1.0.0
 */
class TACWAModuleTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'TACWAModule';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('someFilter', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('someFunction', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function someInternalFunction($text = null)
    {
        $result = $text . " in the way";

        return $result;
    }
}
