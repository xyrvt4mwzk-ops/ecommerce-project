<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\roles;
use App\Models\Permission;
use App\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       {
        // 1. Define your 5 Core Roles
        $roles = [
            [
                'name' => 'super-admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full access to platform control, payouts, and system overrides.',
            ],
            [
                'name' => 'creator',
                'display_name' => 'Content Creator',
                'description' => 'Can upload digital goods, manage store fonts, and view earnings analytics.',
            ],
            [
                'name' => 'buyer',
                'display_name' => 'Customer / Buyer',
                'description' => 'Can purchase products, download instant secure links, and leave reviews.',
            ],
            [
                'name' => 'affiliate',
                'display_name' => 'Affiliate Marketer',
                'description' => 'Can generate tracking links and track click-to-conversion referral rewards.',
            ],
            [
                'name' => 'content-reviewer',
                'display_name' => 'Content Reviewer / Moderator',
                'description' => 'Can audit products, handle DMCA copyright complaints, and clear takedown flags.',
            ],
        ];

        // 2. Loop through and insert each role into your XAMPP MySQL database
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }
    }
}
}
