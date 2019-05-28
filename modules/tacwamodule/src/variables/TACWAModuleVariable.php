<?php
/**
 * TACWA module for Craft CMS 3.x
 *
 * TACWA PHP file
 *
 * @link      http://tacwa.org
 * @copyright Copyright (c) 2019 telegraph
 */

namespace modules\tacwamodule\variables;

use modules\tacwamodule\TACWAModule;

use Craft;

/**
 * @author    telegraph
 * @package   TACWAModule
 * @since     1.0.0
 */
class TACWAModuleVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
