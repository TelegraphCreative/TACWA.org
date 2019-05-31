<?php
/**
 * Change Stripe Subscription plugin for Craft CMS 3.x
 *
 * Modify an organization's Stripe subscription
 *
 * @link      http://cleave.co
 * @copyright Copyright (c) 2019 John Mueller
 */

namespace cleaveco\changestripesubscription\controllers;

use Craft;
/*
use craft\services;
use craft\helpers\UrlHelper;
use craft\web\Controller;
*/

/**
 * Guest Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    John Mueller
 * @package   ChangeStripeSubscription
 * @since     1.0.0
 */
class GuestController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/change-stripe-subscription/default
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the GuestController actionIndex() method';

        return $result;
    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/change-stripe-subscription/default/do-something
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'Welcome to the GuestController actionDoSomething() method';

        return $result;
    }
	

    public function actionJoin()
    {
		
		$this->logg('I\'m here');
		
		return $this->redirect('registration-success');
	}
	

    private function logg($message) {
      $file = Craft::getAlias('@storage/logs/changestripesubsciption.log');
      $log = date('Y-m-d H:i:s').' '.$message."\n";
      \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }
    
}
