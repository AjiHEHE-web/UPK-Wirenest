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
            ['id_kategori' => '10000000-0000-4000-8000-000000000001', 'name' => 'Mawar'],
            ['id_kategori' => '10000000-0000-4000-8000-000000000002', 'name' => 'Lily'],
            ['id_kategori' => '10000000-0000-4000-8000-000000000003', 'name' => 'Matahari'],
            ['id_kategori' => '10000000-0000-4000-8000-000000000004', 'name' => 'Tulip'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['id_kategori' => $category['id_kategori']],
                ['name' => $category['name']],
            );
        }
    }
}
