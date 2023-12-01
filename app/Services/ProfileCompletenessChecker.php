<?php

namespace App\Services;

class ProfileCompletenessChecker
{
    public static function check($account)
    {
        $requiredFields = [
            'full_name',
            'email',
            'phone',
        ];

        foreach ($requiredFields as $field) {
            if (empty($account->{$field})) {
                return false;
            }
        }

        return true;
    }
}
