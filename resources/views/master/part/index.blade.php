@extends('layouts.app')

@section('title', 'Master Suku Cadang (Part)')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-box-seam text-danger me-2"></i>Master Suku Cadang</h4>
        <p class="text-muted small mb-0">Kelola master suku cadang Honda, harga, dan kontrol stok fisik, RFS, serta Booked.</p>
    </div>
    <div>
        <button class="btn btn-honda d-flex align-items-center gap-2" id="btnTambahPart">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Part</span>
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchPart" class="form-control bg-light border-start-0 ps-0" placeholder="Cari Part Number atau Nama Part...">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-secondary" id="btnRefresh">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" id="tablePart">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 18%;">Part Number</th>
                        <th>Nama Suku Cadang</th>
                        <th style="width: 15%;">Harga Satuan</th>
                        <th class="text-center" style="width: 10%;">Qty Fisik</th>
                        <th class="text-center" style="width: 10%;">Qty RFS</th>
                        <th class="text-center" style="width: 10%;">Qty Book</th>
                        <th class="text-center pe-4" style="width: 12%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyPart">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                            Memuat data suku cadang...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Part -->
<div class="modal fade" id="modalPart" tabindex="-1" aria-labelledby="modalPartLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formPart">
                <input type="hidden" id="formMode" value="create">
                <input type="hidden" id="originalPartNumber" name="original_part_number">
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalPartLabel">Tambah Master Suku Cadang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="part_number" class="form-label">Part Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace" id="part_number" name="part_number" placeholder="Contoh: 42711K59A12" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_part" class="form-label">Nama Suku Cadang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_part" name="nama_part" placeholder="Contoh: Ban Luar Belakang Vario Tubeless" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga" name="harga" min="0" placeholder="Contoh: 260000" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label for="qty_stock" class="form-label">Qty Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="qty_stock" name="qty_stock" min="0" value="0" required>
                        </div>
                        <div class="col-4">
                            <label for="qty_rfs" class="form-label">Qty RFS <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="qty_rfs" name="qty_rfs" min="0" value="0" required>
                        </div>
                        <div class="col-4">
                            <label for="qty_book" class="form-label">Qty Book <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="qty_book" name="qty_book" min="0" value="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-honda" id="btnSubmitPart">
                        <span class="btn-text"><i class="bi bi-save me-1"></i> Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const modalPart = new bootstrap.Modal('#modalPart');
    let searchDebounceTimer;

    // Load data Master Part
    function loadPart(search = '') {
        $('#tbodyPart').html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    Memuat data suku cadang...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('master-part.data') }}",
            type: 'GET',
            data: { search: search },
            success: function(res) {
                if (res.success && res.data.items.length > 0) {
                    let rows = '';
                    res.data.items.forEach(function(item) {
                        const rfsBadgeClass = item.qty_rfs > 0 ? 'bg-success' : 'bg-danger';
                        rows += `
                            <tr>
                                <td class="ps-4 font-monospace fw-semibold text-danger">${item.part_number}</td>
                                <td>${item.nama_part}</td>
                                <td class="fw-semibold">${formatRupiah(item.harga)}</td>
                                <td class="text-center"><span class="badge bg-secondary">${item.qty_stock}</span></td>
                                <td class="text-center"><span class="badge ${rfsBadgeClass}">${item.qty_rfs}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark">${item.qty_book}</span></td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="${item.part_number}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${item.part_number}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tbodyPart').html(rows);
                } else {
                    $('#tbodyPart').html(`
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada data master part ditemukan.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#tbodyPart').html(`
                    <tr>
                        <td colspan="7" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                            Gagal memuat data master part.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Initial Load
    loadPart();

    // Live Search with Debounce
    $('#searchPart').on('keyup', function() {
        clearTimeout(searchDebounceTimer);
        const keyword = $(this).val();
        searchDebounceTimer = setTimeout(function() {
            loadPart(keyword);
        }, 300);
    });

    // Refresh Button
    $('#btnRefresh').on('click', function() {
        $('#searchPart').val('');
        loadPart();
    });

    // Open Modal Tambah
    $('#btnTambahPart').on('click', function() {
        $('#formPart')[0].reset();
        clearFormErrors('#formPart');
        $('#formMode').val('create');
        $('#originalPartNumber').val('');
        $('#part_number').prop('readonly', false);
        $('#modalPartLabel').text('Tambah Master Suku Cadang');
        modalPart.show();
    });

    // Open Modal Edit
    $(document).on('click', '.btn-edit', function() {
        const partNumber = $(this).data('id');
        clearFormErrors('#formPart');

        $.ajax({
            url: `/master-part/${encodeURIComponent(partNumber)}`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    const data = res.data;
                    $('#formMode').val('edit');
                    $('#originalPartNumber').val(data.part_number);
                    $('#part_number').val(data.part_number).prop('readonly', true);
                    $('#nama_part').val(data.nama_part);
                    $('#harga').val(data.harga);
                    $('#qty_stock').val(data.qty_stock);
                    $('#qty_rfs').val(data.qty_rfs);
                    $('#qty_book').val(data.qty_book);
                    $('#modalPartLabel').text(`Edit Master Part: ${data.part_number}`);
                    modalPart.show();
                }
            },
            error: function() {
                Toast.fire({ icon: 'error', title: 'Gagal mengambil data part' });
            }
        });
    });

    // Submit Form (Create / Update)
    $('#formPart').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#formPart');

        const mode = $('#formMode').val();
        const partNumber = $('#originalPartNumber').val() || $('#part_number').val();
        const url = mode === 'create' ? "{{ route('master-part.store') }}" : `/master-part/${encodeURIComponent(partNumber)}`;
        const method = mode === 'create' ? 'POST' : 'PUT';

        const btn = $('#btnSubmitPart');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (res.success) {
                    modalPart.hide();
                    Toast.fire({ icon: 'success', title: res.message });
                    loadPart($('#searchPart').val());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayFormErrors('#formPart', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: msg });
                }
            }
        });
    });

    // Delete Part
    $(document).on('click', '.btn-delete', function() {
        const partNumber = $(this).data('id');

        Swal.fire({
            title: 'Hapus Master Part?',
            text: `Data suku cadang "${partNumber}" akan dihapus permanen dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cc0000',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/master-part/${encodeURIComponent(partNumber)}`,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.success) {
                            Toast.fire({ icon: 'success', title: res.message });
                            loadPart($('#searchPart').val());
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal menghapus data suku cadang.';
                        Swal.fire({ icon: 'error', title: 'Penghapusan Ditolak', text: msg });
                    }
                });
            }
        });
    });
});
</script>
@endpush
