<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id_kategori' => '10000000-0000-4000-8000-000000000001', 'name' => 'Bunga Potong'],
            ['id_kategori' => '10000000-0000-4000-8000-000000000002', 'name' => 'Buket Bunga'],
            ['id_kategori' => '10000000-0000-4000-8000-000000000003', 'name' => 'Tanaman Hias'],
            ['id_kategori' => '10000000-0000-4000-8000-000000000004', 'name' => 'Bunga Kering'],
        ];

        foreach ($categories as $category) {
            Category::query()->firstOrCreate(
                ['name' => $category['name']],
                ['id_kategori' => $category['id_kategori']],
            );
        }
    }
}
