<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace cleaveco\changestripesubscription\variables;

use cleaveco\changestripesubscription\ChangeStripeSubscription;

use Craft;
use craft\services;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use craft\elements\User;

class DefaultVariable
{
    /**
     * @param string|int $submissionId
     *
     * @return null|PaymentModel
     */
    
    
    public function getSubscription($subscriptionID) {
	    return 'hey';
    }
}
