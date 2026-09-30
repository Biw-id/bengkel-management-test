@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Manajemen Work Order</h1>
            <p class="mb-4">Kelola konversi Form SA ke Work Order dan pengerjaan servis.</p>
        </div>
    </div>

    <!-- Menunggu Konversi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-clipboard-list me-2"></i>Form SA Menunggu Konversi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTableFormSa" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID Form SA</th>
                            <th>Tanggal</th>
                            <th>No Plat</th>
                            <th>Keluhan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formSaList as $sa)
                        <tr>
                            <td>{{ $sa->id_form_sa }}</td>
                            <td>{{ $sa->waktu_form_sa->format('d/m/Y H:i') }}</td>
                            <td>{{ $sa->no_plat }}</td>
                            <td>{{ $sa->keluhan_konsumen }}</td>
                            <td>
                                @if($sa->workOrder)
                                    <span class="badge bg-secondary">Sudah Jadi WO</span>
                                @else
                                    <a href="{{ route('work-orders.create', ['id_form_sa' => urlencode($sa->id_form_sa)]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-hammer me-1"></i> Buat WO
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada Form SA yang menunggu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Work Order Berjalan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-tools me-2"></i>Work Order Berjalan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTableWo" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID WO</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Mekanik</th>
                            <th>Total Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrders as $wo)
                        <tr>
                            <td>{{ $wo->id_wo }}</td>
                            <td>{{ $wo->waktu_wo->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-warning">{{ $wo->status_wo }}</span></td>
                            <td>{{ $wo->mekanik->nama_mekanik }}</td>
                            <td>Rp {{ number_format($wo->total_bayar_keseluruhan, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('work-orders.edit', ['id_wo' => urlencode($wo->id_wo)]) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-edit me-1"></i> Edit/Cart
                                </a>
                                <!-- Tombol Selesai (Task T7 nanti) -->
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada Work Order.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
