<?php

namespace Botble\ACL\Database\Seeders;

use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends BaseSeeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        User::query()->truncate();
        Role::query()->truncate();
        DB::table('role_users')->truncate();
        DB::table('activations')->truncate();

        $faker = $this->fake();

        $this->createUser([
            'full_name' => $faker->firstName(),
            'email' => $faker->companyEmail(),
            'username' => 'botble',
            'password' => Hash::make('159357'),
            'super_user' => 1,
            'manage_supers' => 1,
        ]);

        $superuser = $this->createUser([
            'full_name' => $faker->firstName(),
            'email' => $faker->companyEmail(),
            'username' => 'admin',
            'password' => Hash::make('12345678'),
            'super_user' => 1,
            'manage_supers' => 1,
        ]);

        $permissions = (new Role())->getAvailablePermissions();

        $permissions = array_map(function () {
            return true;
        }, $permissions);

        Role::query()->forceCreate([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin users role',
            'permissions' => $permissions,
            'is_default' => true,
            'created_by' => $superuser->getKey(),
            'updated_by' => $superuser->getKey(),
        ]);
    }

    protected function createUser(array $data): User
    {
        $user = new User();
        $user->forceFill($data);
        $user->save();

        app(ActivateUserService::class)->activate($user);

        return $user;
    }
}
