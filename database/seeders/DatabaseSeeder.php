<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MemberTypeSeeder::class);
        // Demo access is deliberately limited to development and test environments.
        if (app()->environment(['local', 'testing'])) {
            User::firstOrCreate(['email' => 'beheerder@fratertje.test'], ['name' => 'Beheerder', 'password' => 'Fratertje-Demo-2026!']);
        }
    }
}
