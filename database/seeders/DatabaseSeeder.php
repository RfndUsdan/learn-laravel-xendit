<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // HAPUS bagian User::factory()->create(...) di sini
        // karena emailnya bentrok dengan yang ada di UserSeeder.

        $this->call([
            UserSeeder::class, // Cukup panggil ini saja
            // ProductSeeder::class, // Aktifkan jika nanti ingin isi ulang produk
        ]);
    }
}