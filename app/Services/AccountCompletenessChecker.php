<?php

namespace App\Services;

use Botble\RealEstate\Models\Broker;

class AccountCompletenessChecker
{
    public static function check($account)
    {
        $requiredFields = [
            'full_name',
            'email',
            'phone',
        ];

        if ($account->IsBrokerAccount) {
            $broker = Broker::where('account_id', $account->id)->first();

            if (empty($broker->val_license_number) || empty($broker->commercial_registration) || empty($broker->license_number)) {
                return false;
            }
        }

        foreach ($requiredFields as $field) {
            if (empty($account->{$field})) {
                return false;
            }
        }

        return true;
    }
}
