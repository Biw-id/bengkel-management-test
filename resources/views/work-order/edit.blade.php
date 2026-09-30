@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Work Order: {{ $wo->id_wo }}</h1>
        <a href="{{ route('work-orders.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    <!-- Info Utama -->
    <div class="card shadow mb-4 border-left-danger">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p class="mb-1 text-muted small">Status WO</p>
                    <span class="badge bg-warning">{{ $wo->status_wo }}</span>
                </div>
                <div class="col-md-3">
                    <p class="mb-1 text-muted small">Mekanik</p>
                    <span class="fw-bold">{{ $wo->mekanik->nama_mekanik }}</span>
                </div>
                <div class="col-md-3">
                    <p class="mb-1 text-muted small">Waktu Dibuat</p>
                    <span class="fw-bold">{{ $wo->waktu_wo->format('d M Y, H:i') }}</span>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeWoModal" {{ $wo->status_wo === 'SELESAI' ? 'disabled' : '' }}>
                        <i class="fas fa-check-circle me-1"></i> Selesaikan WO
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Jasa -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-wrench me-2"></i>Penanganan Jasa</h6>
                    <span class="badge bg-primary rounded-pill" id="badge-total-jasa">Rp {{ number_format($wo->total_bayar_jasa, 0, ',', '.') }}</span>
                </div>
                <div class="card-body">
                    <!-- Form Tambah Jasa -->
                    <form id="formAddJasa" class="mb-3 border-bottom pb-3">
                        <div class="row align-items-end">
                            <div class="col-md-7">
                                <label class="form-label small">Pilih Jasa</label>
                                <select class="form-select select2-jasa" id="id_job" required>
                                    <option value="">-- Cari Jasa --</option>
                                    @foreach($jasas as $j)
                                        <option value="{{ $j->id_job }}">{{ $j->keterangan }} - Rp {{ number_format($j->harga, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="number" class="form-control" id="qty_jasa" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100" id="btnAddJasa">Tambah</button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Jasa -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="table-jasa">
                            <thead class="table-light">
                                <tr>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Biaya</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wo->detailWorkOrders->where('jenis_penanganan', 'Jasa') as $dj)
                                <tr id="row-detail-{{ $dj->id_detail }}">
                                    <td>{{ $dj->deskripsi }}</td>
                                    <td class="text-center">{{ $dj->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($dj->biaya * $dj->qty, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-jasa" data-id="{{ $dj->id_detail }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Part -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-cogs me-2"></i>Penggunaan Suku Cadang (Part)</h6>
                    <span class="badge bg-danger rounded-pill" id="badge-total-part">Rp {{ number_format($wo->total_bayar_part, 0, ',', '.') }}</span>
                </div>
                <div class="card-body">
                    <!-- Form Tambah Part -->
                    <form id="formAddPart" class="mb-3 border-bottom pb-3">
                        <div class="row align-items-end">
                            <div class="col-md-7">
                                <label class="form-label small">Pilih Part</label>
                                <select class="form-select select2-part" id="part_number" required>
                                    <option value="">-- Cari Part --</option>
                                    @foreach($parts as $p)
                                        <option value="{{ $p->part_number }}" data-rfs="{{ $p->qty_rfs }}">
                                            {{ $p->nama_part }} ({{ $p->part_number }}) - RFS: {{ $p->qty_rfs }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="number" class="form-control" id="qty_part" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-danger w-100" id="btnAddPart">Tambah</button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel Part -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="table-part">
                            <thead class="table-light">
                                <tr>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Biaya</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wo->detailWorkOrders->where('jenis_penanganan', 'Part') as $dp)
                                <tr id="row-detail-{{ $dp->id_detail }}">
                                    <td>{{ $dp->deskripsi }}</td>
                                    <td class="text-center">{{ $dp->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($dp->biaya * $dp->qty, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-part" data-id="{{ $dp->id_detail }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesaikan WO -->
<div class="modal fade" id="completeWoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Selesaikan Work Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCompleteWo">
                <div class="modal-body">
                    <div class="alert alert-info py-2 small">
                        <strong>Customer:</strong> {{ $wo->formServiceAdvisor ? $wo->formServiceAdvisor->customer->nama_customer : 'Tanpa Customer' }} 
                        ({{ $wo->formServiceAdvisor ? $wo->formServiceAdvisor->customer->no_plat : '-' }})
                    </div>
                    <div class="mb-3">
                        <label for="tipe_unit" class="form-label">Tipe Unit Motor</label>
                        <input type="text" class="form-control" id="tipe_unit" placeholder="Contoh: Beat, Vario 125, dll" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitComplete">Selesaikan & Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF untuk semua AJAX Request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.select2-jasa').select2({ theme: 'bootstrap-5' });
    $('.select2-part').select2({ theme: 'bootstrap-5' });

    let woId = "{{ urlencode($wo->id_wo) }}";

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number).replace("Rp", "Rp ").trim();
    }

    // --- LOGIKA JASA ---
    $('#formAddJasa').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#btnAddJasa');
        let id_job = $('#id_job').val();
        let qty = $('#qty_jasa').val();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `/work-orders/${woId}/add-jasa`,
            type: 'POST',
            data: { id_job: id_job, qty: qty },
            success: function(res) {
                if(res.success) {
                    Swal.fire({ toast:true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                    // Tambah row baru
                    let d = res.data.detail;
                    let html = `
                    <tr id="row-detail-${d.id_detail}">
                        <td>${d.deskripsi}</td>
                        <td class="text-center">${d.qty}</td>
                        <td class="text-end">${formatRupiah(d.biaya * d.qty)}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-jasa" data-id="${d.id_detail}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                    $('#table-jasa tbody').append(html);
                    
                    // Update badges
                    $('#badge-total-jasa').text(formatRupiah(res.data.total_bayar_jasa));
                    $('#text-total-keseluruhan').text(formatRupiah(res.data.total_bayar_keseluruhan));

                    // Reset form
                    $('#id_job').val('').trigger('change');
                    $('#qty_jasa').val(1);
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan";
                Swal.fire('Gagal!', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('Tambah');
            }
        });
    });

    $(document).on('click', '.btn-delete-jasa', function() {
        let id_detail = $(this).data('id');
        let tr = $(`#row-detail-${id_detail}`);
        let btn = $(this);

        Swal.fire({
            title: 'Hapus Jasa?',
            text: "Jasa ini akan dihapus dari WO.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#secondary',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url: `/work-orders/remove-jasa/${id_detail}`,
                    type: 'DELETE',
                    success: function(res) {
                        if(res.success) {
                            tr.fadeOut(300, function(){ $(this).remove(); });
                            $('#badge-total-jasa').text(formatRupiah(res.data.total_bayar_jasa));
                            $('#text-total-keseluruhan').text(formatRupiah(res.data.total_bayar_keseluruhan));
                            Swal.fire({ toast:true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Gagal menghapus jasa', 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    }
                });
            }
        });
    });


    // --- LOGIKA PART (ATURAN STOK MUTLAK) ---
    $('#formAddPart').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#btnAddPart');
        let part_number = $('#part_number').val();
        let qty = parseInt($('#qty_part').val());
        
        let selectedOption = $('#part_number').find(':selected');
        let qtyRfs = parseInt(selectedOption.data('rfs'));

        // Pre-validation di sisi client
        if (qty > qtyRfs) {
            Swal.fire('Stok Tidak Cukup', `Part ini hanya memiliki stok RFS sebanyak ${qtyRfs}. Tidak bisa menambahkan ${qty}.`, 'warning');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `/work-orders/${woId}/add-part`,
            type: 'POST',
            data: { part_number: part_number, qty: qty },
            success: function(res) {
                if(res.success) {
                    Swal.fire({ toast:true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                    
                    let d = res.data.detail;
                    let html = `
                    <tr id="row-detail-${d.id_detail}">
                        <td>${d.deskripsi}</td>
                        <td class="text-center">${d.qty}</td>
                        <td class="text-end">${formatRupiah(d.biaya * d.qty)}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-part" data-id="${d.id_detail}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                    $('#table-part tbody').append(html);
                    
                    // Update badges
                    $('#badge-total-part').text(formatRupiah(res.data.total_bayar_part));
                    $('#text-total-keseluruhan').text(formatRupiah(res.data.total_bayar_keseluruhan));

                    // Update option RFS in select2 (Client side reflection)
                    let newRfs = qtyRfs - qty;
                    selectedOption.data('rfs', newRfs);
                    let originalText = selectedOption.text();
                    let newText = originalText.replace(/RFS: \d+/, `RFS: ${newRfs}`);
                    selectedOption.text(newText);
                    $('#part_number').trigger('change.select2');

                    // Reset form
                    $('#part_number').val('').trigger('change');
                    $('#qty_part').val(1);
                }
            },
            error: function(xhr) {
                // Handle HTTP 400 Insufficient Stock
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan";
                let code = xhr.responseJSON ? xhr.responseJSON.code : "";
                if(code === 'INSUFFICIENT_STOCK') {
                     Swal.fire('Stok Ditolak Sistem', msg, 'error');
                } else {
                     Swal.fire('Gagal!', msg, 'error');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('Tambah');
            }
        });
    });

    $(document).on('click', '.btn-delete-part', function() {
        let id_detail = $(this).data('id');
        let tr = $(`#row-detail-${id_detail}`);
        let btn = $(this);

        Swal.fire({
            title: 'Hapus Part?',
            text: "Part akan dikembalikan ke stok RFS.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#secondary',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                $.ajax({
                    url: `/work-orders/remove-part/${id_detail}`,
                    type: 'DELETE',
                    success: function(res) {
                        if(res.success) {
                            tr.fadeOut(300, function(){ $(this).remove(); });
                            $('#badge-total-part').text(formatRupiah(res.data.total_bayar_part));
                            $('#text-total-keseluruhan').text(formatRupiah(res.data.total_bayar_keseluruhan));
                            Swal.fire({ toast:true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                            
                            // Note: We don't perfectly sync the select2 RFS dynamically on delete here
                            // to keep it simple, but a page reload would reflect it. Since this is an SPA-like 
                            // interaction, a user can just reload to see restored RFS if needed.
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Gagal menghapus part', 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    }
                });
            }
        });
    });

    // --- LOGIKA COMPLETE WO ---
    $('#formCompleteWo').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#btnSubmitComplete');
        let tipe_unit = $('#tipe_unit').val();

        Swal.fire({
            title: 'Selesaikan Work Order?',
            text: "Pastikan semua part dan jasa sudah benar. Aksi ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesaikan!'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
                
                $.ajax({
                    url: `/work-orders/${woId}/complete`,
                    type: 'POST',
                    data: { tipe_unit: tipe_unit },
                    success: function(res) {
                        $('#completeWoModal').modal('hide');
                        if(res.success && res.api_success) {
                            Swal.fire('Sukses!', res.message, 'success').then(() => {
                                window.location.href = "{{ route('work-orders.index') }}";
                            });
                        } else if (res.success && !res.api_success) {
                            Swal.fire('WO Selesai (API Tertunda)', res.message, 'warning').then(() => {
                                window.location.href = "{{ route('work-orders.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan";
                        Swal.fire('Gagal!', msg, 'error');
                        btn.prop('disabled', false).html('Selesaikan & Kirim');
                    }
                });
            }
        });
    });

    // Inisialisasi Select2 di dalam Modal sudah dihapus karena tidak ada select2-modal
});
</script>
@endpush
