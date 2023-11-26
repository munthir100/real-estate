<?php

namespace Database\Seeders;

use Botble\RealEstate\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountTypes = [
            'broker',
            'seeker',
            'developer',
        ];

        foreach($accountTypes as $accountTypeName){
            AccountType::create(['name' => $accountTypeName]);
        }
    }
}
