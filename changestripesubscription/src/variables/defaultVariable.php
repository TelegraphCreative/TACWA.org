<?php

namespace cleaveco\changestripesubscription\variables;

class DefaultVariable
{

    /**
     * @return Settings
     */
    public function getCustomerBySubscription()
    {
        return 'hey';
    }

    public function getExpiringSubscriptions()
    {
        $memberships = \craft\elements\Entry::find()
			->type('membership')
			->all();

		$admins = \craft\elements\User::find()
			->group('memberAdmin')
			->all();

		$membershipArray = [];
		foreach ($memberships as $membership) {
			$membershipArray[$membership['stripeSubscriptionId']] = $membership;
		}

		$adminArray = [];
		foreach ($admins as $admin) {
			$adminArray[$admin['organizationId']][] = $admin;
		}

		\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_API_KEY'));
		
		$ninetyDays = strtotime('+90 days', time());
		// $ninetyDays = strtotime('+10 months', time());
		$subscriptions = \Stripe\Subscription::all([
			'current_period_end' => ['lte' => $ninetyDays],
			// 'limit' => 3,
		]);

		$data = [];

		foreach ($subscriptions->autoPagingIterator() as $sub) {
			if (array_key_exists($sub['id'], $membershipArray)) {
				$member = $membershipArray[$sub['id']];
				$admins = '';
				foreach ($adminArray[$member['id']] as $admin) {
					$admins .= "(" .
						$admin['firstName'] . " " .
						$admin['lastName'] . ", " .
						$admin['individualAccountTitle'] . ", " .
						$admin['phone'] . ", " .
						$admin['email'] . ") ";
				}

				$data[] = [
                    $sub['id'],
                    date('Y-m-d', $sub['current_period_end']),
					$member['organizationName'],
					$member['organizationStreetAddress'],
					$member['organizationCity'],
					$member['organizationState'],
					$member['organizationZip'],
					$admins
				];
			} else {
				$data[] = [$sub['id'], date('Y-m-d', $sub['current_period_end']), 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A'];
			}
		}

		// print_r($data);
        return $data;
    }
}
