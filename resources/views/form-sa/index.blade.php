@extends('layouts.app')

@section('title', 'Form Service Advisor (Pendaftaran Servis)')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-danger me-2"></i>Form Service Advisor</h4>
        <p class="text-muted small mb-0">Pendaftaran keluhan servis motor oleh Service Advisor dengan nomor registrasi otomatis.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Form Pendaftaran SA -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-pencil-square text-danger me-2"></i>Pendaftaran Baru</span>
                <span class="badge bg-danger-subtle text-danger font-monospace px-2 py-1" id="badgeNextId">
                    Next: {{ $nextIdPreview }}
                </span>
            </div>
            <div class="card-body p-4">
                <form id="formServiceAdvisor">
                    <div class="mb-3">
                        <label for="preview_id_sa" class="form-label text-muted small">Nomor ID Form SA (Otomatis)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-qr-code"></i></span>
                            <input type="text" class="form-control bg-light text-danger fw-bold font-monospace" id="preview_id_sa" value="{{ $nextIdPreview }}" readonly>
                        </div>
                        <div class="form-text">Format baku: <code>SA/TAHUN/36915/XXXX</code></div>
                    </div>

                    <div class="mb-3">
                        <label for="selectCustomer" class="form-label">Pilih Konsumen <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectCustomer" name="id_customer" required>
                            <option value="">-- Pilih atau Cari Konsumen --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id_customer }}" data-plat="{{ $c->no_plat }}" data-nama="{{ $c->nama_customer }}">
                                    {{ $c->id_customer }} — {{ $c->nama_customer }} ({{ $c->no_plat }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="no_plat" class="form-label">Nomor Plat Kendaraan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" id="no_plat" name="no_plat" placeholder="Contoh: BA 1234 AB" required>
                        </div>
                        <div class="form-text">Otomatis terisi saat konsumen dipilih, atau sesuaikan jika memakai unit lain.</div>
                    </div>

                    <div class="mb-4">
                        <label for="keluhan_konsumen" class="form-label">Keluhan Konsumen / Kerusakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keluhan_konsumen" name="keluhan_konsumen" rows="4" placeholder="Tuliskan keluhan kendaraan atau permintaan servis konsumen..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-honda w-100 py-2 fw-semibold" id="btnSubmitFormSa">
                        <i class="bi bi-check-circle me-1"></i> Terbitkan Form SA
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riwayat Form Service Advisor -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <div class="row g-2 align-items-center justify-content-between">
                    <div class="col-12 col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchFormSa" class="form-control bg-light border-start-0 ps-0" placeholder="Cari No SA, Plat, Konsumen, atau Keluhan...">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-secondary" id="btnRefreshHistory">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" id="tableFormSa">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 28%;">ID Form SA</th>
                                <th style="width: 25%;">Konsumen / Plat</th>
                                <th>Keluhan</th>
                                <th class="text-center pe-3" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyFormSa">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                                    Memuat riwayat pendaftaran...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Form SA -->
<div class="modal fade" id="modalDetailSa" tabindex="-1" aria-labelledby="modalDetailSaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalDetailSaLabel"><i class="bi bi-file-earmark-text text-danger me-2"></i>Detail Form SA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <span class="badge bg-danger fs-6 px-3 py-2 font-monospace" id="detailIdSa">-</span>
                    <div class="text-muted small mt-1" id="detailWaktuSa">-</div>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Nama Konsumen:</span>
                        <strong id="detailNamaCustomer">-</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">ID Customer:</span>
                        <span class="font-monospace fw-semibold" id="detailIdCustomer">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Nomor Plat:</span>
                        <span class="badge bg-dark px-2 py-1" id="detailNoPlat">-</span>
                    </div>
                    <div class="list-group-item px-0 py-2">
                        <div class="text-muted mb-1">Keluhan Konsumen:</div>
                        <div class="p-3 bg-light rounded border text-secondary" id="detailKeluhan">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const modalDetailSa = new bootstrap.Modal('#modalDetailSa');
    let searchDebounceTimer;

    // Initialize Select2 on customer dropdown
    $('#selectCustomer').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih atau Cari Konsumen --',
        allowClear: true,
        width: '100%'
    });

    // Auto-fill No Plat when Customer is selected
    $('#selectCustomer').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const plat = selectedOption.data('plat') || '';
        $('#no_plat').val(plat);
    });

    // Load Form SA List
    function loadFormSa(search = '') {
        $('#tbodyFormSa').html(`
            <tr>
                <td colspan="4" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    Memuat data...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('form-sa.data') }}",
            type: 'GET',
            data: { search: search },
            success: function(res) {
                if (res.success && res.data.items.length > 0) {
                    let rows = '';
                    res.data.items.forEach(function(item) {
                        const dateStr = item.waktu_form_sa ? new Date(item.waktu_form_sa).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        }) : '-';
                        const customerName = item.customer ? item.customer.nama_customer : '-';

                        rows += `
                            <tr>
                                <td class="ps-3">
                                    <div class="font-monospace fw-semibold text-danger">${item.id_form_sa}</div>
                                    <small class="text-muted">${dateStr}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">${customerName}</div>
                                    <span class="badge bg-dark" style="font-size: 0.75rem;">${item.no_plat}</span>
                                </td>
                                <td>
                                    <div class="text-truncate text-secondary" style="max-width: 180px;" title="${item.keluhan_konsumen}">
                                        ${item.keluhan_konsumen}
                                    </div>
                                </td>
                                <td class="text-center pe-3">
                                    <button class="btn btn-sm btn-outline-info btn-detail" data-id="${item.id_form_sa}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tbodyFormSa').html(rows);
                } else {
                    $('#tbodyFormSa').html(`
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Belum ada pendaftaran Form SA.
                            </td>
                        </tr>
                    `);
                }

                if (res.data.nextIdPreview) {
                    $('#badgeNextId').text('Next: ' + res.data.nextIdPreview);
                    $('#preview_id_sa').val(res.data.nextIdPreview);
                }
            },
            error: function() {
                $('#tbodyFormSa').html(`
                    <tr>
                        <td colspan="4" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                            Gagal memuat data Form SA.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Initial Load
    loadFormSa();

    // Live Search with Debounce
    $('#searchFormSa').on('keyup', function() {
        clearTimeout(searchDebounceTimer);
        const keyword = $(this).val();
        searchDebounceTimer = setTimeout(function() {
            loadFormSa(keyword);
        }, 300);
    });

    // Refresh Button
    $('#btnRefreshHistory').on('click', function() {
        $('#searchFormSa').val('');
        loadFormSa();
    });

    // Submit Form SA via AJAX
    $('#formServiceAdvisor').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#formServiceAdvisor');

        const btn = $('#btnSubmitFormSa');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

        $.ajax({
            url: "{{ route('form-sa.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Terbitkan Form SA');
                if (res.success) {
                    // Reset form
                    $('#formServiceAdvisor')[0].reset();
                    $('#selectCustomer').val('').trigger('change');
                    clearFormErrors('#formServiceAdvisor');

                    // Update Next ID Preview
                    if (res.next_id) {
                        $('#badgeNextId').text('Next: ' + res.next_id);
                        $('#preview_id_sa').val(res.next_id);
                    }

                    // Success alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Form SA Berhasil Diterbitkan!',
                        html: `Nomor Registrasi: <strong class="text-danger font-monospace fs-5 d-block mt-1">${res.data.id_form_sa}</strong>`,
                        confirmButtonColor: '#cc0000',
                        confirmButtonText: 'OK'
                    });

                    loadFormSa($('#searchFormSa').val());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Terbitkan Form SA');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    displayFormErrors('#formServiceAdvisor', xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menerbitkan Form SA.';
                    Swal.fire({ icon: 'error', title: 'Gagal Menerbitkan Form SA', text: msg });
                }
            }
        });
    });

    // View Detail Modal
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');

        $.ajax({
            url: `/form-sa/${encodeURIComponent(id)}`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    const data = res.data;
                    const dateStr = data.waktu_form_sa ? new Date(data.waktu_form_sa).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }) : '-';

                    $('#detailIdSa').text(data.id_form_sa);
                    $('#detailWaktuSa').text('Waktu Terbit: ' + dateStr);
                    $('#detailNamaCustomer').text(data.customer ? data.customer.nama_customer : '-');
                    $('#detailIdCustomer').text(data.id_customer);
                    $('#detailNoPlat').text(data.no_plat);
                    $('#detailKeluhan').text(data.keluhan_konsumen);

                    modalDetailSa.show();
                }
            },
            error: function() {
                Toast.fire({ icon: 'error', title: 'Gagal mengambil detail Form SA' });
            }
        });
    });
});
</script>
@endpush
