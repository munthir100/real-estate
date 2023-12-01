<?php

namespace App\Services;

class BrokerCompletenessChecker
{
    public static function check($account)
    {
        $broker = $account->broker;

        $valLicenseNumber = $broker->val_license_number;
        $commercialRegistration = $broker->commercial_registration;
        $licenseNumber = $broker->license_number;

        return !empty($valLicenseNumber) && !empty($commercialRegistration) && !empty($licenseNumber);
    }
}
