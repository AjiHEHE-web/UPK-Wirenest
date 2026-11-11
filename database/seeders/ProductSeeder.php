<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'id_produk' => '20000000-0000-4000-8000-000000000001',
                'category' => 'Mawar',
                'name_produk' => 'Mawar Merah Premium',
                'deskripsi_produk' => 'Mawar merah segar pilihan, cocok untuk menyampaikan kasih sayang.',
                'harga_produk' => 25000,
                'stock_produk' => 50,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000002',
                'category' => 'Lily',
                'name_produk' => 'Lily Putih',
                'deskripsi_produk' => 'Bunga lily putih segar dengan tampilan anggun dan aroma lembut.',
                'harga_produk' => 45000,
                'stock_produk' => 30,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000003',
                'category' => 'Matahari',
                'name_produk' => 'Bunga Matahari',
                'deskripsi_produk' => 'Bunga matahari cerah untuk memberikan suasana hangat.',
                'harga_produk' => 35000,
                'stock_produk' => 25,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000004',
                'category' => 'Tulip',
                'name_produk' => 'Tulip Merah',
                'deskripsi_produk' => 'Tulip merah segar dengan tampilan elegan untuk berbagai momen.',
                'harga_produk' => 27500,
                'stock_produk' => 12,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000005',
                'category' => 'Tulip',
                'name_produk' => 'Tulip Warna-Warni',
                'deskripsi_produk' => 'Tulip warna-warni yang cerah untuk merayakan momen istimewa.',
                'harga_produk' => 22500,
                'stock_produk' => 15,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000006',
                'category' => 'Lily',
                'name_produk' => 'Lily Casablanca',
                'deskripsi_produk' => 'Lily Casablanca pilihan dengan kelopak besar dan aroma yang lembut.',
                'harga_produk' => 85000,
                'stock_produk' => 10,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000007',
                'category' => 'Mawar',
                'name_produk' => 'Mawar Pink',
                'deskripsi_produk' => 'Mawar pink segar dengan warna lembut untuk hadiah dan dekorasi.',
                'harga_produk' => 30000,
                'stock_produk' => 20,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000008',
                'category' => 'Matahari',
                'name_produk' => 'Matahari Mini',
                'deskripsi_produk' => 'Bunga matahari berukuran mini yang cerah dan cocok sebagai dekorasi.',
                'harga_produk' => 20000,
                'stock_produk' => 18,
            ],
        ];

        foreach ($products as $product) {
            $category = Category::query()
                ->where('name', $product['category'])
                ->firstOrFail();

            $seededProduct = Product::query()->firstOrNew([
                'id_produk' => $product['id_produk'],
            ]);
            $seededProduct->fill([
                'id_kategori' => $category->id_kategori,
                'name_produk' => $product['name_produk'],
                'deskripsi_produk' => $product['deskripsi_produk'],
            ]);

            if (! $seededProduct->exists) {
                $seededProduct->fill([
                    'harga_produk' => $product['harga_produk'],
                    'stock_produk' => $product['stock_produk'],
                    'image' => null,
                ]);
            }

            $seededProduct->save();
        }
    }
}
