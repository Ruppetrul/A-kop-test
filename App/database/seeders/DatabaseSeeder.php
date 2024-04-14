<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = Company::create(['owner_email' => '', 'name' => 'А-Кор']);

        $adminRole = Role::create(['id' => 1, 'name' => 'администратор сервиса']);
        Role::create(['id' => 2, 'name' => 'владелец организации']);
        Role::create(['id' => 3, 'name' => 'администратор организации']);
        Role::create(['id' => 4, 'name' => 'пользователь организации']);

        $admin = User::create([
            'name'     => 'Иван',
            'surname'  => 'Иванов',
            'email'    => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);

        $admin->companies()->attach($company);

        $companyRelation = UserCompany::where([
            'user_id'    => $admin->id,
            'company_id' => $company->id,
        ])->first();

        $companyRelation->roles()->attach($adminRole->id);
    }
}
