<?php

namespace Tests\Feature;

use App\Models\FormServiceAdvisor;
use App\Models\MasterCustomer;
use App\Models\NotaJasaBengkel;
use App\Models\NotaSukuCadangSo;
use App\Models\WorkOrder;
use App\Services\IdGeneratorService;
use Carbon\Carbon;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdGeneratorAndFormSaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MasterDataSeeder::class);
    }

    public function test_id_generator_service_formats_and_sequence(): void
    {
        $service = new IdGeneratorService();
        $fixedDate = Carbon::create(2026, 8, 26, 10, 0, 0);

        // 1. Test SA ID: SA/YYYY/36915/0001
        $saId1 = $service->generateIdFormSa($fixedDate);
        $this->assertEquals('SA/2026/36915/0001', $saId1);

        // Simulate existing SA record
        FormServiceAdvisor::create([
            'id_form_sa' => $saId1,
            'id_customer' => 'CUST-0001',
            'waktu_form_sa' => $fixedDate,
            'keluhan_konsumen' => 'Keluhan pertama',
            'no_plat' => 'BA 1234 AB',
        ]);

        $saId2 = $service->generateIdFormSa($fixedDate);
        $this->assertEquals('SA/2026/36915/0002', $saId2);

        // 2. Test WO ID: WO/YY/MM/36915/00001
        $woId1 = $service->generateIdWorkOrder($fixedDate);
        $this->assertEquals('WO/26/08/36915/00001', $woId1);

        // Simulate existing WO record
        WorkOrder::create([
            'id_wo' => $woId1,
            'waktu_wo' => $fixedDate,
            'status_wo' => 'PENDING',
            'id_mekanik' => '11111',
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 0,
            'total_bayar_keseluruhan' => 0,
        ]);

        $woId2 = $service->generateIdWorkOrder($fixedDate);
        $this->assertEquals('WO/26/08/36915/00002', $woId2);

        // 3. Test NJB ID: NJB/YY/MM/36915/00001
        $njbId1 = $service->generateIdNjb($fixedDate);
        $this->assertEquals('NJB/26/08/36915/00001', $njbId1);

        NotaJasaBengkel::create([
            'id_njb' => $njbId1,
            'id_wo' => $woId1,
            'waktu_njb' => $fixedDate,
            'id_mekanik' => '11111',
            'tipe_unit' => 'Beat',
        ]);

        $njbId2 = $service->generateIdNjb($fixedDate);
        $this->assertEquals('NJB/26/08/36915/00002', $njbId2);

        // 4. Test NSC ID: NSC/YY/MM/36915/00001
        $nscId1 = $service->generateIdNsc($fixedDate);
        $this->assertEquals('NSC/26/08/36915/00001', $nscId1);

        NotaSukuCadangSo::create([
            'no_nsc' => $nscId1,
            'tgl_nsc' => $fixedDate,
            'id_customer' => 'CUST-0001',
            'amount' => 50000,
            'id_wo' => $woId1,
            'diskon_part' => 0,
            'total_amount' => 50000,
        ]);

        $nscId2 = $service->generateIdNsc($fixedDate);
        $this->assertEquals('NSC/26/08/36915/00002', $nscId2);
    }

    public function test_form_service_advisor_endpoints(): void
    {
        // 1. GET View
        $this->get('/form-sa')->assertStatus(200);

        // 2. GET JSON Data
        $this->getJson('/form-sa/data')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['items', 'total', 'nextIdPreview']]);

        // 3. POST Valid Form SA
        $response = $this->postJson('/form-sa', [
            'id_customer' => 'CUST-0001',
            'no_plat' => 'BA 1234 AB',
            'keluhan_konsumen' => 'Motor bunyi klotok di bagian CVT',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['id_form_sa', 'id_customer', 'no_plat', 'keluhan_konsumen']]);

        $currentYear = date('Y');
        $this->assertStringStartsWith("SA/{$currentYear}/36915/", $response->json('data.id_form_sa'));

        $this->assertDatabaseHas('form_service_advisor', [
            'id_customer' => 'CUST-0001',
            'no_plat' => 'BA 1234 AB',
        ]);

        // 4. Validation Error on Invalid Input
        $this->postJson('/form-sa', [
            'id_customer' => 'INVALID_CUST',
            'no_plat' => '',
            'keluhan_konsumen' => '',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['id_customer', 'no_plat', 'keluhan_konsumen']);
    }
}
