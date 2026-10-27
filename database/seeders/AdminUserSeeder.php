<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Admin istifadəçini yaradır (və ya yeniləyir).
     * Məlumatları .env faylından götürür: ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD
     */
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@orizon-labs.com');
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            $this->command?->error('ADMIN_PASSWORD .env faylında təyin olunmayıb. Admin yaradılmadı.');
            return;
        }

        $user = User::firstOrNew(['email' => $email]);

        // is_admin $fillable-da deyil (təhlükəsizlik üçün), ona görə forceFill
        $user->forceFill([
            'name'     => env('ADMIN_NAME', 'Admin'),
            'password' => $password, // User modelində 'hashed' cast avtomatik hash edir
            'is_admin' => true,
        ])->save();

        $this->command?->info("Admin hazırdır: {$email}");
    }
}
