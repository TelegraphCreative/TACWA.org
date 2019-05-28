<?php
/**
 * TACWA module for Craft CMS 3.x
 *
 * TACWA PHP file
 *
 * @link      http://tacwa.org
 * @copyright Copyright (c) 2019 telegraph
 */

namespace modules\tacwamodule\services;

use modules\tacwamodule\TACWAModule;

use Craft;
use craft\base\Component;

/**
 * @author    telegraph
 * @package   TACWAModule
 * @since     1.0.0
 */
class TACWAModuleService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }
}
