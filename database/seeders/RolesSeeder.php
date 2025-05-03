<?php

// database/seeders/RolesSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['user', 'Sales', 'Purchasing', 'Warehouse', 'Route'];
        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }
}

