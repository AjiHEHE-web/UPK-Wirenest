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
                'category' => 'Bunga Potong',
                'name_produk' => 'Mawar Merah Premium',
                'deskripsi_produk' => 'Mawar merah segar pilihan, cocok untuk menyampaikan kasih sayang.',
                'harga_produk' => 25000,
                'stock_produk' => 50,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000002',
                'category' => 'Bunga Potong',
                'name_produk' => 'Lili Putih',
                'deskripsi_produk' => 'Bunga lili putih segar dengan tampilan anggun dan aroma lembut.',
                'harga_produk' => 45000,
                'stock_produk' => 30,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000003',
                'category' => 'Bunga Potong',
                'name_produk' => 'Bunga Matahari',
                'deskripsi_produk' => 'Bunga matahari cerah untuk memberikan suasana hangat.',
                'harga_produk' => 35000,
                'stock_produk' => 25,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000004',
                'category' => 'Buket Bunga',
                'name_produk' => 'Buket Romantis',
                'deskripsi_produk' => 'Rangkaian mawar merah dan dedaunan segar dalam balutan elegan.',
                'harga_produk' => 275000,
                'stock_produk' => 12,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000005',
                'category' => 'Buket Bunga',
                'name_produk' => 'Buket Ceria',
                'deskripsi_produk' => 'Buket warna-warni untuk merayakan momen istimewa.',
                'harga_produk' => 225000,
                'stock_produk' => 15,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000006',
                'category' => 'Tanaman Hias',
                'name_produk' => 'Anggrek Bulan',
                'deskripsi_produk' => 'Anggrek bulan pilihan yang cantik untuk memperindah ruangan.',
                'harga_produk' => 185000,
                'stock_produk' => 10,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000007',
                'category' => 'Tanaman Hias',
                'name_produk' => 'Lili Paris',
                'deskripsi_produk' => 'Tanaman hias daun yang mudah dirawat untuk rumah dan kantor.',
                'harga_produk' => 55000,
                'stock_produk' => 20,
            ],
            [
                'id_produk' => '20000000-0000-4000-8000-000000000008',
                'category' => 'Bunga Kering',
                'name_produk' => 'Buket Baby Breath Kering',
                'deskripsi_produk' => 'Rangkaian baby breath kering yang tahan lama sebagai dekorasi.',
                'harga_produk' => 150000,
                'stock_produk' => 18,
            ],
        ];

        foreach ($products as $product) {
            $category = Category::query()
                ->where('name', $product['category'])
                ->firstOrFail();

            Product::query()->firstOrCreate(
                [
                    'name_produk' => $product['name_produk'],
                    'id_kategori' => $category->id_kategori,
                ],
                [
                    'id_produk' => $product['id_produk'],
                    'deskripsi_produk' => $product['deskripsi_produk'],
                    'harga_produk' => $product['harga_produk'],
                    'stock_produk' => $product['stock_produk'],
                    'image' => null,
                ],
            );
        }
    }
}
