<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = [
            [
                'code' => 'MCH-001',
                'name' => 'SmartMart Central',
                'description' => 'Pusat retail modern dengan koleksi produk lengkap dan layanan terbaik.',
                'location' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'icon' => 'store',
                'status' => 'active',
            ],
            [
                'code' => 'MCH-002',
                'name' => 'TechStore Pro',
                'description' => 'Toko elekronik dan gadget terlengkap dengan harga kompetitif.',
                'location' => 'Jl. Thamrin No. 45, Jakarta Pusat',
                'icon' => 'monitor',
                'status' => 'active',
            ],
            [
                'code' => 'MCH-003',
                'name' => 'FreshMarket Hub',
                'description' => 'Pasar segar dengan produk pertanian dan bahan makanan berkualitas.',
                'location' => 'Jl. Gatot Subroto No. 78, Jakarta Selatan',
                'icon' => 'apple',
                'status' => 'active',
            ],
            [
                'code' => 'MCH-004',
                'name' => 'Fashion Corner',
                'description' => 'Butik fashion dan aksesoris trendy untuk semua kalangan.',
                'location' => 'Jl. Asia Afrika No. 90, Jakarta Barat',
                'icon' => 'shirt',
                'status' => 'active',
            ],
            [
                'code' => 'MCH-005',
                'name' => 'HomeLiving Plus',
                'description' => 'Furnitur dan perlengkapan rumah tangga dengan desain modern.',
                'location' => 'Jl. Gatot Subroto No. 12, Jakarta Timur',
                'icon' => 'home',
                'status' => 'active',
            ],
        ];

        foreach ($merchants as $merchant) {
            Merchant::updateOrCreate(
                ['code' => $merchant['code']],
                $merchant
            );
        }
    }
}
