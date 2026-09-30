@extends('layouts.app')

@section('title', 'Master Mekanik')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-person-badge text-danger me-2"></i>Master Mekanik</h4>
        <p class="text-muted small mb-0">Kelola data mekanik resmi AHASS beserta Honda ID Mekanik.</p>
    </div>
    <div>
        <button class="btn btn-honda d-flex align-items-center gap-2" id="btnTambahMekanik">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Mekanik</span>
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchMekanik" class="form-control bg-light border-start-0 ps-0" placeholder="Cari Honda ID atau Nama Mekanik...">
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
            <table class="table table-hover table-striped align-middle mb-0" id="tableMekanik">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 10%;">#</th>
                        <th style="width: 25%;">Honda ID Mekanik</th>
                        <th>Nama Lengkap Mekanik</th>
                        <th style="width: 20%;">Terdaftar Sejak</th>
                        <th class="text-center pe-4" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyMekanik">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                            Memuat data mekanik...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Mekanik -->
<div class="modal fade" id="modalMekanik" tabindex="-1" aria-labelledby="modalMekanikLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formMekanik">
                <input type="hidden" id="formMode" value="create">
                <input type="hidden" id="mekanikId" name="id">
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalMekanikLabel">Tambah Master Mekanik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="honda_id_mekanik" class="form-label">Honda ID Mekanik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace" id="honda_id_mekanik" name="honda_id_mekanik" placeholder="Contoh: MEK-001" required>
                        <div class="form-text">ID resmi mekanik dari Main Dealer / AHASS.</div>
                    </div>
                    <div class="mb-3">
                        <label for="nama_mekanik" class="form-label">Nama Lengkap Mekanik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_mekanik" name="nama_mekanik" placeholder="Contoh: Budi Santoso" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-honda" id="btnSubmitMekanik">
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
    const modalMekanik = new bootstrap.Modal('#modalMekanik');
    let searchDebounceTimer;

    // Load data Master Mekanik
    function loadMekanik(search = '') {
        $('#tbodyMekanik').html(`
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    Memuat data mekanik...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('master-mekanik.data') }}",
            type: 'GET',
            data: { search: search },
            success: function(res) {
                if (res.success && res.data.items.length > 0) {
                    let rows = '';
                    res.data.items.forEach(function(item, idx) {
                        const dateStr = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : '-';
                        rows += `
                            <tr>
                                <td class="ps-4 text-muted">${idx + 1}</td>
                                <td class="font-monospace fw-semibold text-danger">${item.honda_id_mekanik}</td>
                                <td class="fw-semibold">${item.nama_mekanik}</td>
                                <td class="text-muted small">${dateStr}</td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="${item.id}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${item.id}" data-name="${item.nama_mekanik}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tbodyMekanik').html(rows);
                } else {
                    $('#tbodyMekanik').html(`
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada data mekanik ditemukan.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#tbodyMekanik').html(`
                    <tr>
                        <td colspan="5" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                            Gagal memuat data master mekanik.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Initial Load
    loadMekanik();

    // Live Search with Debounce
    $('#searchMekanik').on('keyup', function() {
        clearTimeout(searchDebounceTimer);
        const keyword = $(this).val();
        searchDebounceTimer = setTimeout(function() {
            loadMekanik(keyword);
        }, 300);
    });

    // Refresh Button
    $('#btnRefresh').on('click', function() {
        $('#searchMekanik').val('');
        loadMekanik();
    });

    // Open Modal Tambah
    $('#btnTambahMekanik').on('click', function() {
        $('#formMekanik')[0].reset();
        clearFormErrors('#formMekanik');
        $('#formMode').val('create');
        $('#mekanikId').val('');
        $('#modalMekanikLabel').text('Tambah Master Mekanik');
        modalMekanik.show();
    });

    // Open Modal Edit
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        clearFormErrors('#formMekanik');

        $.ajax({
            url: `/master-mekanik/${id}`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    const data = res.data;
                    $('#formMode').val('edit');
                    $('#mekanikId').val(data.id);
                    $('#honda_id_mekanik').val(data.honda_id_mekanik);
                    $('#nama_mekanik').val(data.nama_mekanik);
                    $('#modalMekanikLabel').text(`Edit Mekanik: ${data.nama_mekanik}`);
                    modalMekanik.show();
                }
            },
            error: function() {
                Toast.fire({ icon: 'error', title: 'Gagal mengambil data mekanik' });
            }
        });
    });

    // Submit Form (Create / Update)
    $('#formMekanik').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#formMekanik');

        const mode = $('#formMode').val();
        const id = $('#mekanikId').val();
        const url = mode === 'create' ? "{{ route('master-mekanik.store') }}" : `/master-mekanik/${id}`;
        const method = mode === 'create' ? 'POST' : 'PUT';

        const btn = $('#btnSubmitMekanik');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (res.success) {
                    modalMekanik.hide();
                    Toast.fire({ icon: 'success', title: res.message });
                    loadMekanik($('#searchMekanik').val());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayFormErrors('#formMekanik', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: msg });
                }
            }
        });
    });

    // Delete Mekanik
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Master Mekanik?',
            text: `Data mekanik "${name}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cc0000',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/master-mekanik/${id}`,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.success) {
                            Toast.fire({ icon: 'success', title: res.message });
                            loadMekanik($('#searchMekanik').val());
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal menghapus data mekanik.';
                        Swal.fire({ icon: 'error', title: 'Penghapusan Ditolak', text: msg });
                    }
                });
            }
        });
    });
});
</script>
@endpush
