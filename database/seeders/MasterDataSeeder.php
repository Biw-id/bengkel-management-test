<?php

namespace Database\Seeders;

use App\Models\MasterCustomer;
use App\Models\MasterJasa;
use App\Models\MasterMekanik;
use App\Models\MasterPart;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Master Jasa
        $jasas = [
            [
                'id_job' => '00000BL063',
                'kode_motor' => 'BL',
                'keterangan' => 'Complete Service (CS/PL) BEAT SPORTY DLX',
                'harga' => 55000,
            ],
            [
                'id_job' => '00001VP063',
                'kode_motor' => 'VP',
                'keterangan' => 'SERVIS LENGKAP VARIO 125',
                'harga' => 55000,
            ],
            [
                'id_job' => '00003VP071',
                'kode_motor' => 'VP',
                'keterangan' => 'Jasa Ganti Ban',
                'harga' => 30000,
            ],
            [
                'id_job' => '00000BL111',
                'kode_motor' => 'BL',
                'keterangan' => 'Jasa Ganti Oli',
                'harga' => 10000,
            ],
            [
                'id_job' => '00001VP061',
                'kode_motor' => 'VP',
                'keterangan' => 'Ganti Kanvas Rem Belakang',
                'harga' => 25000,
            ],
        ];

        foreach ($jasas as $jasa) {
            MasterJasa::updateOrCreate(
                ['id_job' => $jasa['id_job']],
                $jasa
            );
        }

        // 2. Seed Master Part
        $parts = [
            [
                'part_number' => '42711K59A12',
                'nama_part' => 'TIRE RR TL (90/90-14) - Ban Vario',
                'harga' => 290000,
                'qty_stock' => 11,
                'qty_rfs' => 11,
                'qty_book' => 0,
            ],
            [
                'part_number' => '43130KZL930',
                'nama_part' => 'BRAKE SHOE (Kanvas Rem)',
                'harga' => 55000,
                'qty_stock' => 14,
                'qty_rfs' => 14,
                'qty_book' => 0,
            ],
            [
                'part_number' => '082342MBK0LZ0',
                'nama_part' => 'Oli SPX2 10W30 SLMB 0,8L REP',
                'harga' => 73500,
                'qty_stock' => 9,
                'qty_rfs' => 9,
                'qty_book' => 0,
            ],
        ];

        foreach ($parts as $part) {
            MasterPart::updateOrCreate(
                ['part_number' => $part['part_number']],
                $part
            );
        }

        // 3. Seed Master Mekanik
        $mekaniks = [
            [
                'honda_id_mekanik' => '11111',
                'nama_mekanik' => 'Budi Santoso',
            ],
            [
                'honda_id_mekanik' => '22222',
                'nama_mekanik' => 'Rahmat Hidayat',
            ],
            [
                'honda_id_mekanik' => '33333',
                'nama_mekanik' => 'Yogi Pratama',
            ],
        ];

        foreach ($mekaniks as $mekanik) {
            MasterMekanik::updateOrCreate(
                ['honda_id_mekanik' => $mekanik['honda_id_mekanik']],
                $mekanik
            );
        }

        // 4. Seed Master Customer
        $customers = [
            [
                'id_customer' => 'CUST-0001',
                'nama_customer' => 'Ahmad Fauzi',
                'no_plat' => 'BA 1234 AB',
            ],
            [
                'id_customer' => 'CUST-0002',
                'nama_customer' => 'Siti Rahma',
                'no_plat' => 'BA 5678 CD',
            ],
            [
                'id_customer' => 'CUST-0003',
                'nama_customer' => 'Riki Kurniawan',
                'no_plat' => 'BA 9012 EF',
            ],
        ];

        foreach ($customers as $customer) {
            MasterCustomer::updateOrCreate(
                ['id_customer' => $customer['id_customer']],
                $customer
            );
        }
    }
}
