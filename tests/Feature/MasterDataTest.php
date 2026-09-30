<?php

namespace Tests\Feature;

use App\Models\MasterCustomer;
use App\Models\MasterJasa;
use App\Models\MasterMekanik;
use App\Models\MasterPart;
use Database\Seeders\MasterDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MasterDataSeeder::class);
    }

    public function test_master_views_render_successfully(): void
    {
        $this->get('/master-jasa')->assertStatus(200);
        $this->get('/master-part')->assertStatus(200);
        $this->get('/master-mekanik')->assertStatus(200);
        $this->get('/master-customer')->assertStatus(200);
    }

    public function test_master_data_ajax_endpoints_return_json(): void
    {
        $this->getJson('/master-jasa/data')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['items', 'total']]);

        $this->getJson('/master-part/data')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['items', 'total']]);

        $this->getJson('/master-mekanik/data')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['items', 'total']]);

        $this->getJson('/master-customer/data')
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['items', 'total']]);
    }

    public function test_validation_rejects_negative_price_and_quantity(): void
    {
        // Negative harga for Master Jasa
        $this->postJson('/master-jasa', [
            'id_job' => 'TEST_NEG_1',
            'kode_motor' => 'Beat',
            'keterangan' => 'Uji Coba',
            'harga' => -50000,
        ])->assertStatus(422);

        // Negative qty and harga for Master Part
        $this->postJson('/master-part', [
            'part_number' => 'TEST_NEG_PART',
            'nama_part' => 'Uji Coba Part',
            'harga' => -1000,
            'qty_stock' => -5,
            'qty_rfs' => -2,
            'qty_book' => -1,
        ])->assertStatus(422);
    }

    public function test_crud_master_jasa_lifecycle(): void
    {
        $id = 'TEST_JASA_' . rand(1000, 9999);

        // Create
        $response = $this->postJson('/master-jasa', [
            'id_job' => $id,
            'kode_motor' => 'Beat Deluxe',
            'keterangan' => 'Servis Ringan Test',
            'harga' => 50000,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('master_jasa', ['id_job' => $id]);

        // Update
        $this->putJson("/master-jasa/{$id}", [
            'id_job' => $id,
            'kode_motor' => 'Beat Deluxe 2024',
            'keterangan' => 'Servis Ringan Test Updated',
            'harga' => 60000,
        ])->assertStatus(200);
        $this->assertDatabaseHas('master_jasa', ['id_job' => $id, 'harga' => 60000]);

        // Delete
        $this->deleteJson("/master-jasa/{$id}")->assertStatus(200);
        $this->assertDatabaseMissing('master_jasa', ['id_job' => $id]);
    }

    public function test_crud_master_part_lifecycle(): void
    {
        $partNumber = 'TEST_PART_' . rand(1000, 9999);

        // Create
        $response = $this->postJson('/master-part', [
            'part_number' => $partNumber,
            'nama_part' => 'Filter Udara Test',
            'harga' => 45000,
            'qty_stock' => 10,
            'qty_rfs' => 10,
            'qty_book' => 0,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('master_part', ['part_number' => $partNumber]);

        // Search
        $searchRes = $this->getJson('/master-part/data?search=' . $partNumber);
        $searchRes->assertStatus(200)->assertJsonFragment(['part_number' => $partNumber]);

        // Delete
        $this->deleteJson("/master-part/{$partNumber}")->assertStatus(200);
        $this->assertDatabaseMissing('master_part', ['part_number' => $partNumber]);
    }

    public function test_crud_master_mekanik_and_customer(): void
    {
        // Mekanik
        $mekanikRes = $this->postJson('/master-mekanik', [
            'honda_id_mekanik' => 'MEK-999',
            'nama_mekanik' => 'Mekanik Uji Coba',
        ]);
        $mekanikRes->assertStatus(201);
        $mekanikId = $mekanikRes->json('data.id');

        $this->deleteJson("/master-mekanik/{$mekanikId}")->assertStatus(200);

        // Customer
        $custRes = $this->postJson('/master-customer', [
            'id_customer' => 'CUST-9999',
            'nama_customer' => 'Pelanggan Test Padang',
            'no_plat' => 'BA 9999 ZZ',
        ]);
        $custRes->assertStatus(201);
        $custId = $custRes->json('data.id');

        $this->deleteJson("/master-customer/{$custId}")->assertStatus(200);
    }
}
