<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ──────────────────────────────────────────────────────────

        $adminId = DB::table('users')->insertGetId([
            'name'              => 'Admin User',
            'email'             => 'admin@homefixer.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $customerId = DB::table('users')->insertGetId([
            'name'              => 'Ravi Kumar',
            'email'             => 'ravi@example.com',
            'password'          => Hash::make('password'),
            'role'              => 'customer',
            'phone'             => '9876543210',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $provider1Id = DB::table('users')->insertGetId([
            'name'              => 'Ramesh Plumbing Works',
            'email'             => 'ramesh@provider.com',
            'password'          => Hash::make('password'),
            'role'              => 'provider',
            'phone'             => '9123456780',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $provider2Id = DB::table('users')->insertGetId([
            'name'              => 'Sunita Electricals',
            'email'             => 'sunita@provider.com',
            'password'          => Hash::make('password'),
            'role'              => 'provider',
            'phone'             => '9988776655',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ── 2. Service Categories ─────────────────────────────────────────────

        $plumbingId = DB::table('service_categories')->insertGetId([
            'name'        => 'Plumbing',
            'slug'        => 'plumbing',
            'icon'        => 'fa-solid fa-wrench',
            'description' => 'Pipe repairs, leaks, fittings, and installations',
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $electricalId = DB::table('service_categories')->insertGetId([
            'name'        => 'Electrical',
            'slug'        => 'electrical',
            'icon'        => 'fa-solid fa-bolt',
            'description' => 'Wiring, switches, fans, and appliance repairs',
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $carpentryId = DB::table('service_categories')->insertGetId([
            'name'        => 'Carpentry',
            'slug'        => 'carpentry',
            'icon'        => 'fa-solid fa-hammer',
            'description' => 'Furniture repair, door fixing, and woodwork',
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $cleaningId = DB::table('service_categories')->insertGetId([
            'name'        => 'Cleaning',
            'slug'        => 'cleaning',
            'icon'        => 'fa-solid fa-broom',
            'description' => 'Deep cleaning, pest control, and sanitization',
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // ── 3. Services ───────────────────────────────────────────────────────

        $pipeRepairId = DB::table('services')->insertGetId([
            'category_id'           => $plumbingId,
            'name'                  => 'Pipe Repair & Leak Fixing',
            'description'           => 'Fix leaking pipes, joints, and fittings',
            'base_price'            => 350.00,
            'duration_estimate_hrs' => 1.5,
            'is_active'             => true,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $tapId = DB::table('services')->insertGetId([
            'category_id'           => $plumbingId,
            'name'                  => 'Tap & Faucet Installation',
            'description'           => 'Install or replace taps and faucets',
            'base_price'            => 200.00,
            'duration_estimate_hrs' => 0.5,
            'is_active'             => true,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $wiringId = DB::table('services')->insertGetId([
            'category_id'           => $electricalId,
            'name'                  => 'Wiring & Switchboard Repair',
            'description'           => 'Fix faulty wiring, switches, and sockets',
            'base_price'            => 400.00,
            'duration_estimate_hrs' => 2.0,
            'is_active'             => true,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $fanId = DB::table('services')->insertGetId([
            'category_id'           => $electricalId,
            'name'                  => 'Fan & Light Installation',
            'description'           => 'Install ceiling fans and light fixtures',
            'base_price'            => 250.00,
            'duration_estimate_hrs' => 1.0,
            'is_active'             => true,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        // ── 4. Provider Profiles ──────────────────────────────────────────────

        DB::table('provider_profiles')->insert([
            [
                'user_id'            => $provider1Id,
                'bio'                => '8 years experience in residential plumbing in Ranchi',
                'years_experience'   => 8,
                'service_radius_km'  => 15,
                'hourly_rate'        => 300.00,
                'avg_rating'         => 4.70,
                'total_jobs'         => 142,
                'is_available'       => true,
                'verified_at'        => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'user_id'            => $provider2Id,
                'bio'                => 'Certified electrician, specializing in residential wiring',
                'years_experience'   => 5,
                'service_radius_km'  => 10,
                'hourly_rate'        => 350.00,
                'avg_rating'         => 4.85,
                'total_jobs'         => 98,
                'is_available'       => true,
                'verified_at'        => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);

        // ── 5. Provider ↔ Service mappings ────────────────────────────────────

        DB::table('provider_services')->insert([
            [
                'provider_id'  => $provider1Id,
                'service_id'   => $pipeRepairId,
                'custom_price' => 400.00,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'provider_id'  => $provider1Id,
                'service_id'   => $tapId,
                'custom_price' => null, // Uses base_price
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'provider_id'  => $provider2Id,
                'service_id'   => $wiringId,
                'custom_price' => 450.00,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'provider_id'  => $provider2Id,
                'service_id'   => $fanId,
                'custom_price' => null,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        echo "✅ Seeded: users, categories, services, provider profiles\n";
        echo "   Login credentials (all passwords: 'password'):\n";
        echo "   finalprojectmini@gmail.com  → Admin\n";
        echo "   ravi@example.com     → Customer\n";
        echo "   ramesh@provider.com  → Provider (Plumbing)\n";
        echo "   sunita@provider.com  → Provider (Electrical)\n";
    }
}
