@extends('layouts.app')

@section('title', 'Master Jasa Servis')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-tools text-danger me-2"></i>Master Data Jasa</h4>
        <p class="text-muted small mb-0">Kelola tarif dan daftar jasa pengerjaan servis motor bengkel AHASS.</p>
    </div>
    <div>
        <button class="btn btn-honda d-flex align-items-center gap-2" id="btnTambahJasa">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Jasa</span>
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchJasa" class="form-control bg-light border-start-0 ps-0" placeholder="Cari ID Job, Kode Motor, atau Keterangan...">
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
            <table class="table table-hover table-striped align-middle mb-0" id="tableJasa">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 15%;">ID Job</th>
                        <th style="width: 20%;">Kode Motor</th>
                        <th>Keterangan Jasa</th>
                        <th style="width: 20%;">Harga Servis</th>
                        <th class="text-center pe-4" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyJasa">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                            Memuat data jasa...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Jasa -->
<div class="modal fade" id="modalJasa" tabindex="-1" aria-labelledby="modalJasaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formJasa">
                <input type="hidden" id="formMode" value="create">
                <input type="hidden" id="originalIdJob" name="original_id_job">
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalJasaLabel">Tambah Master Jasa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_job" class="form-label">ID Job <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="id_job" name="id_job" placeholder="Contoh: 00000BL063" required>
                        <div class="form-text">Kode unik pengerjaan jasa AHASS.</div>
                    </div>
                    <div class="mb-3">
                        <label for="kode_motor" class="form-label">Kode / Tipe Motor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode_motor" name="kode_motor" placeholder="Contoh: Beat, Vario 125, PCX 160" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan Jasa <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Deskripsi pekerjaan servis..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Servis (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga" name="harga" min="0" placeholder="Contoh: 85000" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-honda" id="btnSubmitJasa">
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
    const modalJasa = new bootstrap.Modal('#modalJasa');
    let searchDebounceTimer;

    // Load data Master Jasa
    function loadJasa(search = '') {
        $('#tbodyJasa').html(`
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    Memuat data jasa...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('master-jasa.data') }}",
            type: 'GET',
            data: { search: search },
            success: function(res) {
                if (res.success && res.data.items.length > 0) {
                    let rows = '';
                    res.data.items.forEach(function(item) {
                        rows += `
                            <tr>
                                <td class="ps-4 font-monospace fw-semibold text-danger">${item.id_job}</td>
                                <td><span class="badge bg-light text-dark border">${item.kode_motor}</span></td>
                                <td>${item.keterangan}</td>
                                <td class="fw-semibold">${formatRupiah(item.harga)}</td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="${item.id_job}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${item.id_job}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tbodyJasa').html(rows);
                } else {
                    $('#tbodyJasa').html(`
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada data master jasa ditemukan.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr) {
                $('#tbodyJasa').html(`
                    <tr>
                        <td colspan="5" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                            Gagal memuat data master jasa.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Initial Load
    loadJasa();

    // Live Search with Debounce
    $('#searchJasa').on('keyup', function() {
        clearTimeout(searchDebounceTimer);
        const keyword = $(this).val();
        searchDebounceTimer = setTimeout(function() {
            loadJasa(keyword);
        }, 300);
    });

    // Refresh Button
    $('#btnRefresh').on('click', function() {
        $('#searchJasa').val('');
        loadJasa();
    });

    // Open Modal Tambah
    $('#btnTambahJasa').on('click', function() {
        $('#formJasa')[0].reset();
        clearFormErrors('#formJasa');
        $('#formMode').val('create');
        $('#originalIdJob').val('');
        $('#id_job').prop('readonly', false);
        $('#modalJasaLabel').text('Tambah Master Jasa');
        modalJasa.show();
    });

    // Open Modal Edit
    $(document).on('click', '.btn-edit', function() {
        const idJob = $(this).data('id');
        clearFormErrors('#formJasa');

        $.ajax({
            url: `/master-jasa/${encodeURIComponent(idJob)}`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    const data = res.data;
                    $('#formMode').val('edit');
                    $('#originalIdJob').val(data.id_job);
                    $('#id_job').val(data.id_job).prop('readonly', true);
                    $('#kode_motor').val(data.kode_motor);
                    $('#keterangan').val(data.keterangan);
                    $('#harga').val(data.harga);
                    $('#modalJasaLabel').text(`Edit Master Jasa: ${data.id_job}`);
                    modalJasa.show();
                }
            },
            error: function() {
                Toast.fire({ icon: 'error', title: 'Gagal mengambil data jasa' });
            }
        });
    });

    // Submit Form (Create / Update)
    $('#formJasa').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#formJasa');

        const mode = $('#formMode').val();
        const idJob = $('#originalIdJob').val() || $('#id_job').val();
        const url = mode === 'create' ? "{{ route('master-jasa.store') }}" : `/master-jasa/${encodeURIComponent(idJob)}`;
        const method = mode === 'create' ? 'POST' : 'PUT';

        const btn = $('#btnSubmitJasa');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (res.success) {
                    modalJasa.hide();
                    Toast.fire({ icon: 'success', title: res.message });
                    loadJasa($('#searchJasa').val());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayFormErrors('#formJasa', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: msg });
                }
            }
        });
    });

    // Delete Jasa
    $(document).on('click', '.btn-delete', function() {
        const idJob = $(this).data('id');

        Swal.fire({
            title: 'Hapus Master Jasa?',
            text: `Data jasa "${idJob}" akan dihapus permanen dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cc0000',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/master-jasa/${encodeURIComponent(idJob)}`,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.success) {
                            Toast.fire({ icon: 'success', title: res.message });
                            loadJasa($('#searchJasa').val());
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal menghapus data jasa.';
                        Swal.fire({ icon: 'error', title: 'Penghapusan Ditolak', text: msg });
                    }
                });
            }
        });
    });
});
</script>
@endpush
