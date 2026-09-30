@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-receipt me-2 text-danger"></i>Daftar Nota Jasa & Suku Cadang</h1>
        <span class="badge bg-danger px-3 py-2 fs-6">Dealer ID: 36915 · AHASS Menara Agung</span>
    </div>

    <div class="row">
        <!-- Kolom NJB -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-wrench me-2"></i>Nota Jasa Bengkel (NJB)</h6>
                    <span class="badge bg-primary rounded-pill">{{ count($njbList) }} Nota</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No NJB</th>
                                    <th>Work Order</th>
                                    <th>Tipe Unit</th>
                                    <th>Waktu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($njbList as $index => $njb)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $njb->id_njb }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $njb->id_wo }}</span></td>
                                    <td>{{ $njb->tipe_unit ?? '-' }}</td>
                                    <td><small class="text-muted">{{ $njb->waktu_njb->format('d/m/Y H:i') }}</small></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNjb{{ $index }}">
                                            <i class="fas fa-print me-1"></i> Detail / Cetak
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                        Belum ada Nota Jasa Bengkel yang diterbitkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom NSC -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-cogs me-2"></i>Nota Suku Cadang (NSC)</h6>
                    <span class="badge bg-danger rounded-pill">{{ count($nscList) }} Nota</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No NSC</th>
                                    <th>Customer</th>
                                    <th>Total Bayar</th>
                                    <th>Waktu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nscList as $index => $nsc)
                                <tr>
                                    <td class="fw-bold text-danger">{{ $nsc->no_nsc }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $nsc->customer->nama_customer ?? '-' }}</div>
                                        <small class="text-muted">{{ $nsc->customer->no_plat ?? '' }}</small>
                                    </td>
                                    <td class="fw-bold text-success">Rp {{ number_format($nsc->total_amount, 0, ',', '.') }}</td>
                                    <td><small class="text-muted">{{ $nsc->tgl_nsc->format('d/m/Y H:i') }}</small></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalNsc{{ $index }}">
                                            <i class="fas fa-print me-1"></i> Detail / Cetak
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                        Belum ada Nota Suku Cadang yang diterbitkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODALS NJB ==================== -->
@foreach($njbList as $index => $njb)
<div class="modal fade" id="modalNjb{{ $index }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Struk Nota Jasa Bengkel (NJB)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="printNjb{{ $index }}">
                <div class="text-center pb-3 border-bottom">
                    <h4 class="fw-bold text-danger mb-1">AHASS MENARA AGUNG</h4>
                    <p class="small text-muted mb-0">Dealer ID: 36915 · Bengkel Resmi Sepeda Motor Honda</p>
                    <p class="small text-muted mb-0">Jl. Veteran No. 30, Padang, Sumatera Barat</p>
                </div>

                <div class="row mt-3 small">
                    <div class="col-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted p-0" style="width: 100px;">No. NJB</td><td class="fw-bold p-0">: {{ $njb->id_njb }}</td></tr>
                            <tr><td class="text-muted p-0">No. WO</td><td class="p-0">: {{ $njb->id_wo }}</td></tr>
                            <tr><td class="text-muted p-0">Tipe Unit</td><td class="p-0">: {{ $njb->tipe_unit ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted p-0" style="width: 100px;">Waktu Transaksi</td><td class="p-0">: {{ $njb->waktu_njb->format('d/m/Y H:i:s') }}</td></tr>
                            <tr><td class="text-muted p-0">Mekanik</td><td class="p-0">: {{ $njb->workOrder->mekanik->nama_mekanik ?? '-' }} ({{ $njb->id_mekanik }})</td></tr>
                            <tr><td class="text-muted p-0">Customer</td><td class="p-0">: {{ $njb->workOrder->formServiceAdvisor->customer->nama_customer ?? '-' }} ({{ $njb->workOrder->formServiceAdvisor->no_plat ?? '-' }})</td></tr>
                        </table>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-2"><i class="fas fa-list me-1"></i>Rincian Penanganan Jasa Servis</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Keterangan Jasa</th>
                                <th class="text-center" style="width: 60px;">Qty</th>
                                <th class="text-end" style="width: 120px;">Tarif Satuan</th>
                                <th class="text-end" style="width: 130px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalJasa = 0; @endphp
                            @forelse($njb->workOrder->detailWorkOrders as $i => $item)
                                @php 
                                    $subtotal = $item->biaya * $item->qty; 
                                    $totalJasa += $subtotal;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $item->deskripsi }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($item->biaya, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada rincian jasa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="4" class="text-end fw-bold">TOTAL BIAYA JASA:</th>
                                <th class="text-end fw-bold text-primary">Rp {{ number_format($totalJasa, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4 pt-3 border-top small text-muted text-center">
                    <div class="col-6">
                        <p class="mb-5">Service Advisor / Kasir</p>
                        <p class="fw-bold mb-0">( _______________________ )</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-5">Konsumen</p>
                        <p class="fw-bold mb-0">( {{ $njb->workOrder->formServiceAdvisor->customer->nama_customer ?? 'Konsumen' }} )</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printElement('printNjb{{ $index }}')">
                    <i class="fas fa-print me-1"></i> Cetak Nota Jasa
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- ==================== MODALS NSC ==================== -->
@foreach($nscList as $index => $nsc)
<div class="modal fade" id="modalNsc{{ $index }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Struk Nota Suku Cadang (NSC)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="printNsc{{ $index }}">
                <div class="text-center pb-3 border-bottom">
                    <h4 class="fw-bold text-danger mb-1">AHASS MENARA AGUNG</h4>
                    <p class="small text-muted mb-0">Dealer ID: 36915 · Penjualan Suku Cadang Asli Honda (HGP)</p>
                    <p class="small text-muted mb-0">Jl. Veteran No. 30, Padang, Sumatera Barat</p>
                </div>

                <div class="row mt-3 small">
                    <div class="col-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted p-0" style="width: 100px;">No. NSC</td><td class="fw-bold p-0">: {{ $nsc->no_nsc }}</td></tr>
                            <tr><td class="text-muted p-0">No. WO</td><td class="p-0">: {{ $nsc->id_wo ?? '-' }}</td></tr>
                            <tr><td class="text-muted p-0">Customer ID</td><td class="p-0">: {{ $nsc->id_customer }}</td></tr>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted p-0" style="width: 100px;">Tanggal / Waktu</td><td class="p-0">: {{ $nsc->tgl_nsc->format('d/m/Y H:i:s') }}</td></tr>
                            <tr><td class="text-muted p-0">Nama Konsumen</td><td class="p-0">: {{ $nsc->customer->nama_customer ?? '-' }}</td></tr>
                            <tr><td class="text-muted p-0">Nomor Plat</td><td class="p-0">: {{ $nsc->customer->no_plat ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-2"><i class="fas fa-boxes me-1"></i>Rincian Suku Cadang (Honda Genuine Parts)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Part Number</th>
                                <th>Nama Suku Cadang</th>
                                <th class="text-center" style="width: 60px;">Qty</th>
                                <th class="text-end" style="width: 120px;">Harga Satuan</th>
                                <th class="text-end" style="width: 130px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nsc->notaSukuCadangH3 as $i => $h3)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td class="fw-bold">{{ $h3->part_number }}</td>
                                    <td>{{ $h3->part->nama_part ?? $h3->part_number }}</td>
                                    <td class="text-center">{{ $h3->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($h3->harga, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($h3->jumlah_bayar, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada rincian suku cadang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end fw-bold">Subtotal Amount:</th>
                                <th class="text-end fw-bold">Rp {{ number_format($nsc->amount, 0, ',', '.') }}</th>
                            </tr>
                            @if($nsc->diskon_part > 0)
                            <tr>
                                <th colspan="5" class="text-end text-danger fw-bold">Diskon Part:</th>
                                <th class="text-end text-danger fw-bold">- Rp {{ number_format($nsc->diskon_part, 0, ',', '.') }}</th>
                            </tr>
                            @endif
                            <tr class="table-light">
                                <th colspan="5" class="text-end fw-bold">TOTAL PEMBAYARAN PART:</th>
                                <th class="text-end fw-bold text-danger">Rp {{ number_format($nsc->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4 pt-3 border-top small text-muted text-center">
                    <div class="col-6">
                        <p class="mb-5">Kasir / Partsman</p>
                        <p class="fw-bold mb-0">( _______________________ )</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-5">Pembeli / Konsumen</p>
                        <p class="fw-bold mb-0">( {{ $nsc->customer->nama_customer ?? 'Konsumen' }} )</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" onclick="printElement('printNsc{{ $index }}')">
                    <i class="fas fa-print me-1"></i> Cetak Nota Part
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function printElement(elementId) {
    var printContent = document.getElementById(elementId).innerHTML;
    var originalContent = document.body.innerHTML;

    var printWindow = window.open('', '', 'height=700,width=900');
    printWindow.document.write('<html><head><title>Cetak Nota AHASS Menara Agung</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">');
    printWindow.document.write('<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: sans-serif; padding: 20px; color: #333; }');
    printWindow.document.write('.table-bordered th, .table-bordered td { border: 1px solid #dee2e6 !important; }');
    printWindow.document.write('@media print { body { margin: 0; padding: 15px; } .btn { display: none; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>
@endpush
