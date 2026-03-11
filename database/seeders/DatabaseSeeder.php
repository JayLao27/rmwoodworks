<?php

namespace Database\Seeders;

use App\Models\System\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo manager account
      User::updateOrCreate(
    ['email' => 'admin@rmwoodworks.com'],
    [
        'name' => 'Admin',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]
);

User::updateOrCreate(
    ['email' => 'inventory@rmwoodworks.com'],
    [
        'name' => 'Inventory Clerk',
        'password' => Hash::make('password'),
        'role' => 'inventory_clerk',
    ]
);

User::updateOrCreate(
    ['email' => 'procurement@rmwoodworks.com'],
    [
        'name' => 'Procurement Officer',
        'password' => Hash::make('password'),
        'role' => 'procurement_officer',
    ]
);

User::updateOrCreate(
    ['email' => 'workshop@rmwoodworks.com'],
    [
        'name' => 'Workshop Staff',
        'password' => Hash::make('password'),
        'role' => 'workshop_staff',
    ]
);

User::updateOrCreate(
    ['email' => 'sales@rmwoodworks.com'],
    [
        'name' => 'Sales Clerk',
        'password' => Hash::make('password'),
        'role' => 'sales_clerk',
    ]
);

User::updateOrCreate(
    ['email' => 'accounting@rmwoodworks.com'],
    [
        'name' => 'Accounting Staff',
        'password' => Hash::make('password'),
        'role' => 'accounting_staff',
    ]
);

        // Run example seeders
        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            MaterialSeeder::class,
            ProductMaterialSeeder::class, // BOM: materials needed per product
            SalesOrderSeeder::class,
            PurchaseOrderSeeder::class,
            WorkOrderSeeder::class,
            AccountingSeeder::class,
            InventoryMovementSeeder::class,
        ]);
    }
}