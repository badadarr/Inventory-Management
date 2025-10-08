<?php

namespace Database\Seeders;

use App\Enums\Customer\CustomerStatusEnum;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'PT Maju Jaya',
                'email' => 'contact@majujaya.com',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'nama_owner' => 'Budi Santoso',
                'bulan_join' => 'Januari',
                'tahun_join' => '2024',
                'status_customer' => CustomerStatusEnum::REPEAT->value,
                'harga_komisi_standar' => 50000,
                'harga_komisi_extra' => 75000,
                'repeat_order_count' => 5,
            ],
            [
                'name' => 'CV Sukses Makmur',
                'email' => 'info@suksesmakmur.com',
                'phone' => '081234567891',
                'address' => 'Jl. Gatot Subroto No. 456, Bandung',
                'nama_owner' => 'Siti Aminah',
                'bulan_join' => 'Maret',
                'tahun_join' => '2024',
                'status_customer' => CustomerStatusEnum::REPEAT->value,
                'harga_komisi_standar' => 45000,
                'harga_komisi_extra' => 70000,
                'repeat_order_count' => 3,
            ],
            [
                'name' => 'UD Berkah Sentosa',
                'email' => 'berkah@sentosa.com',
                'phone' => '081234567892',
                'address' => 'Jl. Asia Afrika No. 789, Surabaya',
                'nama_owner' => 'Ahmad Hidayat',
                'bulan_join' => 'Oktober',
                'tahun_join' => '2025',
                'status_customer' => CustomerStatusEnum::BARU->value,
                'harga_komisi_standar' => 40000,
                'harga_komisi_extra' => 60000,
                'repeat_order_count' => 0,
            ],
            [
                'name' => 'Toko Sejahtera',
                'email' => 'toko@sejahtera.com',
                'phone' => '081234567893',
                'address' => 'Jl. Diponegoro No. 321, Semarang',
                'nama_owner' => 'Rina Kusuma',
                'bulan_join' => 'Februari',
                'tahun_join' => '2024',
                'status_customer' => CustomerStatusEnum::REPEAT->value,
                'harga_komisi_standar' => 55000,
                'harga_komisi_extra' => 80000,
                'repeat_order_count' => 7,
            ],
            [
                'name' => 'PT Global Trading',
                'email' => 'global@trading.com',
                'phone' => '081234567894',
                'address' => 'Jl. Thamrin No. 654, Jakarta Pusat',
                'nama_owner' => 'Eko Prasetyo',
                'bulan_join' => 'September',
                'tahun_join' => '2025',
                'status_customer' => CustomerStatusEnum::BARU->value,
                'harga_komisi_standar' => 42000,
                'harga_komisi_extra' => 65000,
                'repeat_order_count' => 0,
            ],
            [
                'name' => 'CV Karya Mandiri',
                'email' => 'karya@mandiri.com',
                'phone' => '081234567895',
                'address' => 'Jl. Pemuda No. 987, Yogyakarta',
                'nama_owner' => 'Dewi Lestari',
                'bulan_join' => 'April',
                'tahun_join' => '2024',
                'status_customer' => CustomerStatusEnum::REPEAT->value,
                'harga_komisi_standar' => 48000,
                'harga_komisi_extra' => 72000,
                'repeat_order_count' => 4,
            ],
            [
                'name' => 'Toko Harapan Baru',
                'email' => 'harapan@baru.com',
                'phone' => '081234567896',
                'address' => 'Jl. Veteran No. 147, Malang',
                'nama_owner' => 'Agus Wijaya',
                'bulan_join' => 'Agustus',
                'tahun_join' => '2025',
                'status_customer' => CustomerStatusEnum::BARU->value,
                'harga_komisi_standar' => 38000,
                'harga_komisi_extra' => 58000,
                'repeat_order_count' => 0,
            ],
            [
                'name' => 'PT Nusantara Jaya',
                'email' => 'nusantara@jaya.com',
                'phone' => '081234567897',
                'address' => 'Jl. Pahlawan No. 258, Medan',
                'nama_owner' => 'Linda Kartika',
                'bulan_join' => 'Mei',
                'tahun_join' => '2024',
                'status_customer' => CustomerStatusEnum::REPEAT->value,
                'harga_komisi_standar' => 52000,
                'harga_komisi_extra' => 78000,
                'repeat_order_count' => 6,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('✅ Created ' . count($customers) . ' customers (5 repeat, 3 new)');
    }
}
