<?php
/**
 * TACWA module for Craft CMS 3.x
 *
 * TACWA PHP file
 *
 * @link      http://tacwa.org
 * @copyright Copyright (c) 2019 telegraph
 */

namespace modules\tacwamodule;

use modules\tacwamodule\assetbundles\tacwamodule\TACWAModuleAsset;
use modules\tacwamodule\services\TACWAModuleService as TACWAModuleServiceService;
use modules\tacwamodule\variables\TACWAModuleVariable;
use modules\tacwamodule\twigextensions\TACWAModuleTwigExtension;

use Craft;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\TemplateEvent;
use craft\i18n\PhpMessageSource;
use craft\web\View;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 * Class TACWAModule
 *
 * @author    telegraph
 * @package   TACWAModule
 * @since     1.0.0
 *
 * @property  TACWAModuleServiceService $tACWAModuleService
 */
class TACWAModule extends Module
{
    // Static Properties
    // =========================================================================

    /**
     * @var TACWAModule
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@modules/tacwamodule', $this->getBasePath());
        $this->controllerNamespace = 'modules\tacwamodule\controllers';

        // Translation category
        $i18n = Craft::$app->getI18n();
        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (!isset($i18n->translations[$id]) && !isset($i18n->translations[$id.'*'])) {
            $i18n->translations[$id] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@modules/tacwamodule/translations',
                'forceTranslation' => true,
                'allowOverrides' => true,
            ];
        }

        // Base template directory
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $e) {
            if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                $e->roots[$this->id] = $baseDir;
            }
        });

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(TACWAModuleAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
        }

        Craft::$app->view->registerTwigExtension(new TACWAModuleTwigExtension());

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'modules/tacwa-module/default';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'modules/tacwa-module/default/do-something';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('tACWAModule', TACWAModuleVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'tacwa-module',
                '{name} module loaded',
                ['name' => 'TACWA']
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
