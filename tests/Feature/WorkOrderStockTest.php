<?php

namespace Tests\Feature;

use App\Models\FormServiceAdvisor;
use App\Models\MasterCustomer;
use App\Models\MasterMekanik;
use App\Models\MasterPart;
use App\Models\WorkOrder;
use App\Models\DetailWorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan data master
        MasterCustomer::create([
            'id_customer' => 'CUST001',
            'nama_customer' => 'John Doe',
            'no_plat' => 'BA 1234 AB'
        ]);

        MasterMekanik::create([
            'honda_id_mekanik' => 'MEK001',
            'nama_mekanik' => 'Jane Smith'
        ]);

        FormServiceAdvisor::create([
            'id_form_sa' => 'SA/2026/36915/0001',
            'id_customer' => 'CUST001',
            'waktu_form_sa' => now(),
            'keluhan_konsumen' => 'Motor brebet',
            'no_plat' => 'BA 1234 AB'
        ]);

        MasterPart::create([
            'part_number' => '082322MBK4LN9',
            'nama_part' => 'Oli SPX2',
            'harga' => 60000,
            'qty_stock' => 10,
            'qty_rfs' => 2,
            'qty_book' => 0
        ]);
    }

    public function test_work_order_creation_from_sa()
    {
        $response = $this->post(route('work-orders.store'), [
            'id_form_sa' => 'SA/2026/36915/0001',
            'id_mekanik' => 'MEK001'
        ]);

        $wo = WorkOrder::first();
        $this->assertNotNull($wo);
        $this->assertEquals('MEK001', $wo->id_mekanik);
        $this->assertEquals('PROSES', $wo->status_wo);
        
        $response->assertRedirect(route('work-orders.edit', $wo->id_wo));
    }

    public function test_add_part_insufficient_stock_rejected()
    {
        $wo = WorkOrder::create([
            'id_wo' => 'WO/26/08/36915/00001',
            'waktu_wo' => now(),
            'status_wo' => 'PROSES',
            'id_mekanik' => 'MEK001',
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 0,
            'total_bayar_keseluruhan' => 0,
        ]);

        // Coba tambah qty 5 padahal RFS cuma 2
        $response = $this->postJson(route('work-orders.addPart', $wo->id_wo), [
            'part_number' => '082322MBK4LN9',
            'qty' => 5
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'code' => 'INSUFFICIENT_STOCK'
        ]);

        // Pastikan RFS tidak berubah
        $part = MasterPart::where('part_number', '082322MBK4LN9')->first();
        $this->assertEquals(2, $part->qty_rfs);
        $this->assertEquals(0, $part->qty_book);
    }

    public function test_add_part_success_updates_stock()
    {
        $wo = WorkOrder::create([
            'id_wo' => 'WO/26/08/36915/00001',
            'waktu_wo' => now(),
            'status_wo' => 'PROSES',
            'id_mekanik' => 'MEK001',
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 0,
            'total_bayar_keseluruhan' => 0,
        ]);

        // Tambah qty 2
        $response = $this->postJson(route('work-orders.addPart', $wo->id_wo), [
            'part_number' => '082322MBK4LN9',
            'qty' => 2
        ]);

        $response->assertStatus(200);

        // Pastikan RFS berkurang, Book bertambah
        $part = MasterPart::where('part_number', '082322MBK4LN9')->first();
        $this->assertEquals(0, $part->qty_rfs);
        $this->assertEquals(2, $part->qty_book);

        // Pastikan detail tercatat
        $this->assertDatabaseHas('detail_work_order', [
            'id_wo' => $wo->id_wo,
            'jenis_penanganan' => 'Part',
            'qty' => 2
        ]);

        // Pastikan total bayar terupdate (2 * 60000 = 120000)
        $wo->refresh();
        $this->assertEquals(120000, $wo->total_bayar_part);
        $this->assertEquals(120000, $wo->total_bayar_keseluruhan);
    }
    
    public function test_remove_part_restores_stock()
    {
        $wo = WorkOrder::create([
            'id_wo' => 'WO/26/08/36915/00001',
            'waktu_wo' => now(),
            'status_wo' => 'PROSES',
            'id_mekanik' => 'MEK001',
            'total_bayar_jasa' => 0,
            'total_bayar_part' => 60000,
            'total_bayar_keseluruhan' => 60000,
        ]);

        // Modifikasi stock agar seolah-olah sudah di-book 1
        $part = MasterPart::where('part_number', '082322MBK4LN9')->first();
        $part->qty_rfs = 1;
        $part->qty_book = 1;
        $part->save();

        $detail = DetailWorkOrder::create([
            'id_wo' => $wo->id_wo,
            'jenis_penanganan' => 'Part',
            'biaya' => 60000,
            'deskripsi' => 'Oli SPX2 (082322MBK4LN9)',
            'qty' => 1
        ]);

        // Hapus part
        $response = $this->deleteJson(route('work-orders.removePart', $detail->id_detail));

        $response->assertStatus(200);

        // Pastikan RFS balik ke 2, book ke 0
        $part->refresh();
        $this->assertEquals(2, $part->qty_rfs);
        $this->assertEquals(0, $part->qty_book);

        // Pastikan total kembali ke 0
        $wo->refresh();
        $this->assertEquals(0, $wo->total_bayar_part);
        $this->assertEquals(0, $wo->total_bayar_keseluruhan);
    }
}
