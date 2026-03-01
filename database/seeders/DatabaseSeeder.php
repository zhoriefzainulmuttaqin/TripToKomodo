<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\TourCategory;
use App\Models\TourOperator;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@triptokomodo.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ],
        );

        TourCategory::updateOrCreate(
            ['slug' => 'luxury-phinisi'],
            ['name' => 'Luxury Phinisi', 'is_active' => true, 'sort_order' => 1],
        );

        TourCategory::updateOrCreate(
            ['slug' => 'speedboat'],
            ['name' => 'Speedboat', 'is_active' => true, 'sort_order' => 2],
        );

        TourCategory::updateOrCreate(
            ['slug' => 'open-trip'],
            ['name' => 'Open Trip', 'is_active' => true, 'sort_order' => 3],
        );

        TourOperator::updateOrCreate(
            ['slug' => 'triptokomodo'],
            [
                'name' => 'Trip to Komodo',
                'slug' => 'triptokomodo',
                'contact_name' => 'Concierge',
                'contact_email' => 'hello@triptokomodo.com',
                'contact_phone' => null,
                'default_commission_rate' => 0,
                'is_active' => true,
            ],
        );

        // Languages (dipakai untuk multi-language UI & konten tour_package_translations)
        Language::updateOrCreate(
            ['code' => 'id'],
            ['name' => 'Indonesia', 'native_name' => 'Bahasa Indonesia', 'is_active' => true],
        );
        Language::updateOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'native_name' => 'English', 'is_active' => true],
        );
        Language::updateOrCreate(
            ['code' => 'ru'],
            ['name' => 'Russian', 'native_name' => 'Русский', 'is_active' => true],
        );
        Language::updateOrCreate(
            ['code' => 'zh'],
            ['name' => 'Chinese', 'native_name' => '中文', 'is_active' => true],
        );
        Language::updateOrCreate(
            ['code' => 'es'],
            ['name' => 'Spanish', 'native_name' => 'Español', 'is_active' => true],
        );
        Language::updateOrCreate(
            ['code' => 'de'],
            ['name' => 'German', 'native_name' => 'Deutsch', 'is_active' => true],
        );
        $this->call([
            CurrencySeeder::class,
        ]);
    }
}
