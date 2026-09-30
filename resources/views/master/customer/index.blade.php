@extends('layouts.app')

@section('title', 'Master Data Konsumen')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-people text-danger me-2"></i>Master Customer</h4>
        <p class="text-muted small mb-0">Kelola database pelanggan dan nomor plat kendaraan bermotor.</p>
    </div>
    <div>
        <button class="btn btn-honda d-flex align-items-center gap-2" id="btnTambahCustomer">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Customer</span>
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchCustomer" class="form-control bg-light border-start-0 ps-0" placeholder="Cari ID Customer, Nama, atau No Plat...">
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
            <table class="table table-hover table-striped align-middle mb-0" id="tableCustomer">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 8%;">#</th>
                        <th style="width: 20%;">ID Customer</th>
                        <th>Nama Konsumen</th>
                        <th style="width: 20%;">Nomor Plat</th>
                        <th style="width: 18%;">Tgl Registrasi</th>
                        <th class="text-center pe-4" style="width: 14%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyCustomer">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                            Memuat data customer...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Customer -->
<div class="modal fade" id="modalCustomer" tabindex="-1" aria-labelledby="modalCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formCustomer">
                <input type="hidden" id="formMode" value="create">
                <input type="hidden" id="customerId" name="id">
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalCustomerLabel">Tambah Master Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_customer" class="form-label">ID Customer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace" id="id_customer" name="id_customer" placeholder="Contoh: CUST-0001" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_customer" class="form-label">Nama Lengkap Konsumen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer" placeholder="Contoh: Ahmad Fauzi" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_plat" class="form-label">Nomor Plat Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="no_plat" name="no_plat" placeholder="Contoh: BA 1234 AB" required>
                        <div class="form-text">Format standar plat nomor kendaraan (Sumatera Barat: BA).</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-honda" id="btnSubmitCustomer">
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
    const modalCustomer = new bootstrap.Modal('#modalCustomer');
    let searchDebounceTimer;

    // Load data Master Customer
    function loadCustomer(search = '') {
        $('#tbodyCustomer').html(`
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    Memuat data customer...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('master-customer.data') }}",
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
                                <td class="font-monospace fw-semibold text-danger">${item.id_customer}</td>
                                <td class="fw-semibold">${item.nama_customer}</td>
                                <td><span class="badge bg-dark px-2 py-1">${item.no_plat}</span></td>
                                <td class="text-muted small">${dateStr}</td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="${item.id}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${item.id}" data-name="${item.nama_customer}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tbodyCustomer').html(rows);
                } else {
                    $('#tbodyCustomer').html(`
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Tidak ada data customer ditemukan.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function() {
                $('#tbodyCustomer').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                            Gagal memuat data master customer.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Initial Load
    loadCustomer();

    // Live Search with Debounce
    $('#searchCustomer').on('keyup', function() {
        clearTimeout(searchDebounceTimer);
        const keyword = $(this).val();
        searchDebounceTimer = setTimeout(function() {
            loadCustomer(keyword);
        }, 300);
    });

    // Refresh Button
    $('#btnRefresh').on('click', function() {
        $('#searchCustomer').val('');
        loadCustomer();
    });

    // Open Modal Tambah
    $('#btnTambahCustomer').on('click', function() {
        $('#formCustomer')[0].reset();
        clearFormErrors('#formCustomer');
        $('#formMode').val('create');
        $('#customerId').val('');
        $('#id_customer').prop('readonly', false);
        $('#modalCustomerLabel').text('Tambah Master Customer');
        modalCustomer.show();
    });

    // Open Modal Edit
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        clearFormErrors('#formCustomer');

        $.ajax({
            url: `/master-customer/${id}`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    const data = res.data;
                    $('#formMode').val('edit');
                    $('#customerId').val(data.id);
                    $('#id_customer').val(data.id_customer).prop('readonly', true);
                    $('#nama_customer').val(data.nama_customer);
                    $('#no_plat').val(data.no_plat);
                    $('#modalCustomerLabel').text(`Edit Customer: ${data.nama_customer}`);
                    modalCustomer.show();
                }
            },
            error: function() {
                Toast.fire({ icon: 'error', title: 'Gagal mengambil data customer' });
            }
        });
    });

    // Submit Form (Create / Update)
    $('#formCustomer').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#formCustomer');

        const mode = $('#formMode').val();
        const id = $('#customerId').val();
        const url = mode === 'create' ? "{{ route('master-customer.store') }}" : `/master-customer/${id}`;
        const method = mode === 'create' ? 'POST' : 'PUT';

        const btn = $('#btnSubmitCustomer');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (res.success) {
                    modalCustomer.hide();
                    Toast.fire({ icon: 'success', title: res.message });
                    loadCustomer($('#searchCustomer').val());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayFormErrors('#formCustomer', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';
                    Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: msg });
                }
            }
        });
    });

    // Delete Customer
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Hapus Master Customer?',
            text: `Data customer "${name}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cc0000',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/master-customer/${id}`,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.success) {
                            Toast.fire({ icon: 'success', title: res.message });
                            loadCustomer($('#searchCustomer').val());
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal menghapus data customer.';
                        Swal.fire({ icon: 'error', title: 'Penghapusan Ditolak', text: msg });
                    }
                });
            }
        });
    });
});
</script>
@endpush
