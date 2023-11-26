<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Broker;
use Botble\RealEstate\Models\Seeker;
use Illuminate\Support\Facades\Hash;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountType;
use Botble\RealEstate\Models\Property;

class AccountSeeder extends BaseSeeder
{
    public function run(): void
    {
        Account::query()->truncate();

        $files = $this->uploadFiles('accounts');

        $faker = $this->fake();
        $now = $this->now();

        $defaultAccount = Account::query()->create([
            'full_name' => $faker->firstName,
            'email' => 'john.smith@botble.com',
            'username' => Str::slug($faker->unique()->userName()),
            'password' => Hash::make($faker->password()),
            'dob' => $faker->dateTime(),
            'phone' => $faker->e164PhoneNumber(),
            'description' => $faker->realText(30),
            'credits' => $faker->numberBetween(1, 10),
            'confirmed_at' => $now,
            'verified_at' => $now,
            'avatar_id' => $faker->randomElements($files)[0]['data']->id,
            'account_type_id' => AccountType::BROKER
        ]);
        $defaultAccount->broker()->create();

        for ($i = 0; $i < 10; $i++) {
            $account = Account::query()->create([
                'full_name' => $faker->firstName,
                'email' => $faker->email(),
                'username' => Str::slug($faker->unique()->userName()),
                'password' => Hash::make($faker->password()),
                'dob' => $faker->dateTime(),
                'phone' => $faker->e164PhoneNumber(),
                'description' => $faker->realText(30),
                'credits' => $faker->numberBetween(1, 10),
                'confirmed_at' => $now,
                'verified_at' => $now,
                'avatar_id' => $faker->randomElements($files)[0]['data']->id,
                'account_type_id' => $faker->numberBetween(1, 3),
            ]);
        }

        if ($account->IsBrokerAccount) {
            Broker::create([
                'is_developer' => 0,
                'val_license_number' => $faker->text(10),
                'commercial_registration' => $faker->text(10),
                'license_number' => $faker->text(10),
                'account_id' => $account->id,
            ]);
        } elseif ($account->IsDeveloperAccount) {
            Broker::create([
                'is_developer' => 1,
                'val_license_number' => $faker->text(10),
                'commercial_registration' => $faker->text(10),
                'license_number' => $faker->text(10),
                'account_id' => $account->id,
            ]);
        } else {
            Seeker::create([
                'account_id' => $account->id,
            ]);
        }

        foreach (Account::query()->get() as $account) {
            if (is_int($account->id) && $account->id % 2 == 0) {
                $account->is_featured = true;
                $account->save();
            }
        }

        $properties = Property::query()->get();

        foreach ($properties as $property) {
            $property->author_id = Account::query()->inRandomOrder()->value('id');
            $property->author_type = Account::class;
            $property->save();
        }
    }

    private function generateRandomPhoneNumber()
    {
        // Generating a random phone number with a simple format
        return '+1' . mt_rand(100, 999) . mt_rand(1000000, 9999999);
    }
}
