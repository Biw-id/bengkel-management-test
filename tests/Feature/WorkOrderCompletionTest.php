<?php

namespace Tests\Feature;

use App\Models\MasterCustomer;
use App\Models\MasterMekanik;
use App\Models\MasterPart;
use App\Models\WorkOrder;
use App\Models\DetailWorkOrder;
use App\Services\MainDealerApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class WorkOrderCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        MasterCustomer::create([
            'id_customer' => 'CUST001',
            'nama_customer' => 'John Doe',
            'no_plat' => 'BA 1234 AB'
        ]);

        MasterMekanik::create([
            'honda_id_mekanik' => 'MEK001',
            'nama_mekanik' => 'Jane Smith'
        ]);

        MasterPart::create([
            'part_number' => '082322MBK4LN9',
            'nama_part' => 'Oli SPX2',
            'harga' => 60000,
            'qty_stock' => 10,
            'qty_rfs' => 8,
            'qty_book' => 2
        ]);

        MasterPart::create([
            'part_number' => '43130KZL930',
            'nama_part' => 'Kampas Rem Belakang (Brake Shoe)',
            'harga' => 55000,
            'qty_stock' => 15,
            'qty_rfs' => 14,
            'qty_book' => 1
        ]);

        \App\Models\FormServiceAdvisor::create([
            'id_form_sa' => 'SA/2026/36915/0001',
            'id_customer' => 'CUST001',
            'waktu_form_sa' => now(),
            'keluhan_konsumen' => 'Rem bunyi',
            'no_plat' => 'BA 1234 AB'
        ]);
    }

    public function test_complete_work_order_success()
    {
        // Mock API Service agar tidak menembak endpoint sungguhan
        $mockApiService = Mockery::mock(MainDealerApiService::class);
        $mockApiService->shouldReceive('submitTransaction')->once()->andReturnNull();
        $this->app->instance(MainDealerApiService::class, $mockApiService);

        $wo = WorkOrder::create([
            'id_wo' => 'WO/26/08/36915/00001',
            'id_form_sa' => 'SA/2026/36915/0001',
            'waktu_wo' => now(),
            'status_wo' => 'PROSES',
            'id_mekanik' => 'MEK001',
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 175000,
            'total_bayar_keseluruhan' => 175000,
        ]);

        DetailWorkOrder::create([
            'id_wo' => $wo->id_wo,
            'jenis_penanganan' => 'Part',
            'biaya' => 60000,
            'deskripsi' => 'Oli SPX2 (082322MBK4LN9)',
            'qty' => 2
        ]);

        // Tambahkan part yang mengandung tanda kurung pada nama part (Brake Shoe)
        DetailWorkOrder::create([
            'id_wo' => $wo->id_wo,
            'jenis_penanganan' => 'Part',
            'biaya' => 55000,
            'deskripsi' => 'Kampas Rem Belakang (Brake Shoe) (43130KZL930)',
            'qty' => 1
        ]);

        $response = $this->postJson(route('work-orders.complete', $wo->id_wo), [
            'tipe_unit' => 'Vario 125'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'api_success' => true
        ]);

        // Verifikasi Perubahan DB
        $wo->refresh();
        $this->assertEquals('SELESAI', $wo->status_wo);

        $this->assertDatabaseHas('nota_jasa_bengkel', ['id_wo' => $wo->id_wo, 'tipe_unit' => 'Vario 125']);
        $this->assertDatabaseHas('nota_suku_cadang_so', ['id_wo' => $wo->id_wo, 'id_customer' => 'CUST001']);
        $this->assertDatabaseHas('nota_suku_cadang_h3', ['id_wo' => $wo->id_wo, 'part_number' => '082322MBK4LN9', 'qty' => 2]);
        $this->assertDatabaseHas('nota_suku_cadang_h3', ['id_wo' => $wo->id_wo, 'part_number' => '43130KZL930', 'qty' => 1]);

        // Verifikasi qty_book berkurang (permanen dipotong)
        $part = MasterPart::where('part_number', '082322MBK4LN9')->first();
        $this->assertEquals(0, $part->qty_book);

        $partBrake = MasterPart::where('part_number', '43130KZL930')->first();
        $this->assertEquals(0, $partBrake->qty_book);
    }

    public function test_complete_work_order_api_failure_commits_db()
    {
        // Mock API Service agar melempar exception
        $mockApiService = Mockery::mock(MainDealerApiService::class);
        $mockApiService->shouldReceive('submitTransaction')->once()->andThrow(new \Exception('API_CONNECTION_FAILED'));
        $this->app->instance(MainDealerApiService::class, $mockApiService);

        $wo = WorkOrder::create([
            'id_wo' => 'WO/26/08/36915/00002',
            'id_form_sa' => 'SA/2026/36915/0001',
            'waktu_wo' => now(),
            'status_wo' => 'PROSES',
            'id_mekanik' => 'MEK001',
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 0,
            'total_bayar_keseluruhan' => 0,
        ]);

        $response = $this->postJson(route('work-orders.complete', $wo->id_wo), [
            'tipe_unit' => 'Beat'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'api_success' => false
        ]);

        // Verifikasi DB Tetap Commit meskipun API Gagal
        $wo->refresh();
        $this->assertEquals('SELESAI', $wo->status_wo);
        $this->assertDatabaseHas('nota_jasa_bengkel', ['id_wo' => $wo->id_wo]);
    }
}
