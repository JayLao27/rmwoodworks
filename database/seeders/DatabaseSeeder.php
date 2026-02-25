<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name'  => 'Admin',
                'email' => 'admin@rmwoodworks.com',
                'role'  => 'admin',
            ],
            [
                'name'  => 'Inventory Clerk',
                'email' => 'inventory@rmwoodworks.com',
                'role'  => 'inventory_clerk',
            ],
            [
                'name'  => 'Procurement Officer',
                'email' => 'procurement@rmwoodworks.com',
                'role'  => 'procurement_officer',
            ],
            [
                'name'  => 'Workshop Staff',
                'email' => 'workshop@rmwoodworks.com',
                'role'  => 'workshop_staff',
            ],
            [
                'name'  => 'Sales Clerk',
                'email' => 'sales@rmwoodworks.com',
                'role'  => 'sales_clerk',
            ],
            [
                'name'  => 'Accounting Staff',
                'email' => 'accounting@rmwoodworks.com',
                'role'  => 'accounting_staff',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make('password'),
                    'role'     => $userData['role'],
                ]
            );
        }
    }
}
