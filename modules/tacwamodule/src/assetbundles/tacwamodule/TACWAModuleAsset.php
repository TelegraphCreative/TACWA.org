<?php
/**
 * TACWA module for Craft CMS 3.x
 *
 * TACWA PHP file
 *
 * @link      http://tacwa.org
 * @copyright Copyright (c) 2019 telegraph
 */

namespace modules\tacwamodule\assetbundles\TACWAModule;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    telegraph
 * @package   TACWAModule
 * @since     1.0.0
 */
class TACWAModuleAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@modules/tacwamodule/assetbundles/tacwamodule/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/TACWAModule.js',
        ];

        $this->css = [
            'css/TACWAModule.css',
        ];

        parent::init();
    }
}
