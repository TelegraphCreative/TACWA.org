<?php
/**
 * Change Stripe Subscription plugin for Craft CMS 3.x
 *
 * Modify an organization's Stripe subscription
 *
 * @link      http://cleave.co
 * @copyright Copyright (c) 2019 John Mueller
 */

namespace cleaveco\changestripesubscription;


use Craft;

use craft\db\Query;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\services\Elements;
use craft\elements\User;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use cleaveco\changestripesubscription\variables\defaultVariable;

use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use \Solspace\Freeform\Services\FormsService;
use \Solspace\Freeform\Events\Forms\AfterSubmitEvent;
use \Solspace\Freeform\Services\SubmissionsService;
use \Solspace\Freeform\Events\Submissions\SubmitEvent;

use \Solspace\FreeformPayments\FreeformPayments;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    John Mueller
 * @package   ChangeStripeSubscription
 * @since     1.0.0
 *
 */
class ChangeStripeSubscription extends Plugin
{
    
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * ChangeStripeSubscription::$plugin
     *
     * @var ChangeStripeSubscription
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * ChangeStripeSubscription::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */

    private function logg($message) {
      $file = Craft::getAlias('@storage/logs/changestripesubsciption.log');
      $log = date('Y-m-d H:i:s').' '.$message."\n";
      \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }
    
    private $_planId = '';
    private $_subscriptionId = '';
	
	public function getSubscription($subscriptionId) {
		
		\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_API_KEY'));
		$subscription = \Stripe\Subscription::retrieve($subscriptionId);
		
		return $subscription;
	}

    // make sure to register this plugin by running from vagrant shell: composer require cleaveco/change-stripe-subscription
    // you'll need you're composer.json to match the one in this branch so that it can pick up the symlink -john
    // more here: https://docs.craftcms.com/v3/extend/plugin-guide.html#loading-your-plugin-into-a-craft-project
    public function init()
    {
      parent::init();
      self::$plugin = $this;
      
      Craft::$app->getView()->hook('cp.entries.edit.details', function(array &$context) {
        /** @var EntryModel $entry **/
        $entry = $context['entry'];

        // Make sure this is the correct section
        if ($entry->sectionId == 2) { // MEMBERSHIP SECTION
	        $query = (new Query())
	        	->select(['id'])
	        	->from('freeform_forms')
	        	->filterWhere(
		        	['handle' => 'joinTACWApage2']
	        	);
	        $formId = $query->scalar();
	        
	        if ($formId) {
	        
		        $query       = (new Query())
		            ->select(["id"])
		            ->from('freeform_submissions')
		            ->filterWhere(
		                [
			                'formId'	=> $formId,
		                    "field_42"	=> $entry->creationId
		                ]
		            );
		        $submissionId = $query->scalar();
		        
		        if ($submissionId) {
	
			        $payments = FreeformPayments::getInstance()->payments->getPaymentDetails($submissionId);
			        
			        if ($payments && $payments->status == 'active') {
				        
				        $html = '<div class="meta">';
				        
				        $subscription = $this->getSubscription($payments->resourceId);
				        
				        if ($subscription->canceled_at) {
					        
					        $html.= 'Subscription Cancelled:<br>'.date('n/j/Y g:i A', $subscription->canceled_at);
					        
				        } else {
				        
					        $url = UrlHelper::siteUrl().'actions/change-stripe-subscription/default/cancelsubscription?id='.$entry->id;
				        
					        $html = '<a class="btn submit cancelSubscription" href="'.$url.'" onclick="return confirm(\'Are you sure?\')">Cancel Subscription</a>
						        	 <p style="line-height: 1.2;"><small>Cancelling a subscription will both cancel the Stripe subscription and deactivate all members of this organization.</small></p>';
						
						}
						
						$html.= '</div>';
						
			            return $html;
		            }
	            }
	        }
        }
    });

      // Do something after we're installed
      Event::on(
          Plugins::class,
          Plugins::EVENT_AFTER_INSTALL_PLUGIN,
          function (PluginEvent $event) {
              if ($event->plugin === $this) {
                  // We were just installed
                  $this->logg("Logging working");
              }
          }
      );
      	
      	// CHANGE MEMBERSHIP SUBSCRIPTION
      	Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT, function (ElementEvent $event) {
		    // Ignore any element that is not an entry
		    if (!($event->element instanceof Entry)) return;
		    
		    // Ignore entries that are new
		    if ($event->isNew) return;
		    
		    // Get original entry from database
		    $originalEntry = Craft::$app->entries->getEntryById($event->element->id);
		    
		    // Ignore entries not of sectionId = 2 (Membership)
		    if ($originalEntry->sectionId != 2) return;
		    
		    // Add entry's status to array
		    $this->_planId = ($originalEntry->collectionAndTreatmentSystems ? $originalEntry->collectionAndTreatmentSystems : ($originalEntry->collectionSystemOnly ? $originalEntry->collectionSystemOnly : $originalEntry->affiliateSize));
		    
		});

      	Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function (ElementEvent $event) {
		    // Ignore any element that is not an entry
		    if (!($event->element instanceof Entry)) return;
		    
		    // Ignore entries that are new
		    if ($event->isNew) return;
		    
		    // Get original entry from database
		    $savedEntry = Craft::$app->entries->getEntryById($event->element->id);
		    
		    // Ignore entries not of sectionId = 2 (Membership)
		    if ($savedEntry->sectionId != 2) return;
		    
		    $planId = ($savedEntry->collectionAndTreatmentSystems ? $savedEntry->collectionAndTreatmentSystems : ($savedEntry->collectionSystemOnly ? $savedEntry->collectionSystemOnly : $savedEntry->affiliateSize));
		    // Ignore unchanged plans
		    if ($this->_planId == $planId) {
			    $this->logg('Subscription has not changed.');
			    return;
			}
		    
		    

          // taken from: https://stripe.com/docs/billing/subscriptions/upgrading-downgrading
          // we should pull api keys from .env -john
          \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_API_KEY'));

          $subscriptionID = $savedEntry->stripeSubscriptionId;

          // this namespace should work b/c of Composer's autoloading (defined in /web/index.php)
          // if it doesn't, we'll need some troubleshooting -john
          $subscription = \Stripe\Subscription::retrieve($subscriptionID);
          \Stripe\Subscription::update($subscriptionID, [
            'cancel_at_period_end' => false,
            'items' => [
              [
                'id' => $subscription->items->data[0]->id,
                'plan' => $planId,
              ],
            ],
          ]);
		    
		    $this->logg('Change subscription ('.$subscriptionID.') to '.$planId);
		});



      // This is taken from here: http://docs.solspace.com/craft/freeform/v2/developer/events-and-hooks.html#usage-examples
      // Not sure if it's the correct event, but seems like we'll want to do it on submission -john
      // Submission
      Event::on(
        FormsService::class,
        FormsService::EVENT_AFTER_SUBMIT,
        // if I remove any function args it executes this block... but without any data -john
        function (AfterSubmitEvent $event) {
//           $submission = $event->getElement();
          $submission = $event->getSubmission();
          $form       = $event->getForm();
            
          // Get a specific field value
          // only do this for org update submissions
          // change this to get new plan -john
          // $value = $form->get('firstName')->getValue();
          
/*
          $payments = \FreeformPayments::payments($submission->id);
          var_dump($payments);
*/
          
          if ($form->getHandle() == 'joinTACWApage2') {
	          
	        $organizationID = $form->get('entryId')->getValue();
	        $organization = \craft\elements\Entry::find()
	        	->anyStatus()
			    ->id($organizationID)
			    ->one();
			    
// 			$payments = craft\freeformPayments\payments($submission.id);
			// WOULD LIKE TO GET SUBSCRIPTION ID FROM PAYMENT DETAIL TO ADD TO ORGANIZATION

			$organization->enabled = true;
	        Craft::$app->getElements()->saveElement($organization, false);
	        
	
          }
        }
      );
    
    
    // STRIPE WEBHOOK - CUSTOMER SUBSCRIPTION DELETED
    Event::on(
        \rias\stripewebhooks\records\StripeWebhookCall::class,
        'stripe-webhooks::customer.subscription.deleted',
        function (\rias\stripewebhooks\events\WebhookEvent $event) {
            $webhookCall = $event->model;
            
            $this->logg('Webhook Call ('.$webhookCall.')');
        }
    );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'change-stripe-subscription',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
