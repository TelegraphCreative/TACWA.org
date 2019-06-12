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

use cleaveco\changestripesubscription\ChangeStripeSubscription;

use Craft;
use craft\services;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use craft\elements\User;

/**
 * Default Controller
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
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something', 'join'];

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
        $result = 'Welcome to the DefaultController actionIndex() method';

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
        $result = 'Welcome to the DefaultController actionDoSomething() method';

        return $result;
    }

    /**
     * @return null|Response
     * @throws FreeformException
     */
    public function actionInvite()
    {
		$this->requirePostRequest();
        
        $returnUrl = \Craft::$app->request->getUrl();
        $subject = 'TACWA Member Invitation';
        $organizationId = \Craft::$app->request->getBodyParam('orgId');
        $organizationName = \Craft::$app->request->getBodyParam('orgName');
        $registrationURL = UrlHelper::siteUrl().'accountregistration/'.$organizationId;
        
				
		$view = \Craft::$app->getView();         
		if ($view->getTemplateMode() !== $view::TEMPLATE_MODE_SITE) {
		    $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
		}
		
		$template = file_get_contents(UrlHelper::siteUrl().'freeform_formatting/email-template-invite.html');
        		
		$invites = \Craft::$app->request->getBodyParam('invites');
		$sent = 0;
		for($i=1; $i<=$invites; $i++) {
			$name = \Craft::$app->request->getBodyParam('name--'.$i);
			$email = \Craft::$app->request->getBodyParam('email--'.$i);
			if ($name && $email) {
				
				$html = '<p>Hello '.$name.',</p><p>You have been invited to join TACWA by '.$organizationName.'. Please <a href="'.$registrationURL.'">click here</a> to register and set up your account.</p>';
				$html = str_replace('#body#', $html, $template);
								$rc = \Craft::$app
			        ->getMailer()
			        ->compose()
			        ->setTo($email)
			        ->setSubject($subject)
			        ->setHtmlBody($html)
			        ->send();
			    $sent++;
			}
		}
	    
	    return $this->redirect($returnUrl.'?success='.$sent);
	}
	
	
    public function actionRequestcancelsubscription()
    {
	    $this->requirePostRequest();
	    
	    $organizationId = \Craft::$app->request->getBodyParam('organizationId');
	    
	    return $this->redirect(UrlHelper::siteUrl()); 
	    
/*
	    $activeUsers = \craft\elements\User::find()
		    ->search('organizationId::' . $organizationId)
		    ->all();

		foreach($activeUsers as $user) {
			if (!$user->admin) {
				\Craft::$app->users->suspendUser($user);
			}
		}
		
	    
	    $stripeSubscriptionId = \Craft::$app->request->getBodyParam('stripeSubscriptionId');
          // taken from: https://stripe.com/docs/billing/subscriptions/upgrading-downgrading
          // we should pull api keys from .env -john
          \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_API_KEY'));

          // this namespace should work b/c of Composer's autoloading (defined in /web/index.php)
          // if it doesn't, we'll need some troubleshooting -john
          $subscription = \Stripe\Subscription::retrieve($stripeSubscriptionId);
          $subscription->cancel();
		
		return $this->redirect(UrlHelper::siteUrl());
*/
	}
	
	
    public function actionCancelsubscription()
    {
	    $this->requirePostRequest();
	    
	    $organizationId = \Craft::$app->request->getBodyParam('organizationId');
	    
	    $activeUsers = \craft\elements\User::find()
		    ->search('organizationId::' . $organizationId)
		    ->all();

		foreach($activeUsers as $user) {
			if (!$user->admin) {
				\Craft::$app->users->suspendUser($user);
			}
		}
		
	    
	    $stripeSubscriptionId = \Craft::$app->request->getBodyParam('stripeSubscriptionId');
          // taken from: https://stripe.com/docs/billing/subscriptions/upgrading-downgrading
          // we should pull api keys from .env -john
          \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_API_KEY'));

          // this namespace should work b/c of Composer's autoloading (defined in /web/index.php)
          // if it doesn't, we'll need some troubleshooting -john
          $subscription = \Stripe\Subscription::retrieve($stripeSubscriptionId);
          $subscription->cancel();
		
		return $this->redirect(UrlHelper::siteUrl());
	}
	

    private function logg($message) {
      $file = Craft::getAlias('@storage/logs/changestripesubsciption.log');
      $log = date('Y-m-d H:i:s').' '.$message."\n";
      \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }
    
}
