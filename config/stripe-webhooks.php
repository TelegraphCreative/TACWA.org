<?php
	
	
return [
    /*
     * Stripe will sign each webhook using a secret. You can find the used secret at the
     * webhook configuration settings: https://dashboard.stripe.com/account/webhooks.
     */
    'signingSecret' => 'whsec_iYsCVWR6rRQtRVWm97q0QcIEtwlQhV8Q',

    /*
     * You can define the job that should be run when a certain webhook hits your application
     * here. The key is the name of the Stripe event type with the `.` replaced by a `_`.
     *
     * You can find a list of Stripe webhook types here:
     * https://stripe.com/docs/api#event_types.
     */
    'jobs' => [
        // 'customer.subscription.deleted' => \modules\sitemodule\jobs\StripeWebhooks\HandleChargeableSource::class,
    ],

    /*
     * The classname of the model to be used. The class should equal or extend
     * rias\stripewebhooks\records\StripeWebhookCall.
     */
    'model' => \rias\stripewebhooks\records\StripeWebhookCall::class,

    /*
     * The url of the Stripe endpoint you want to use in your application
     */
    'endpoint' => 'stripe-webhooks',
];