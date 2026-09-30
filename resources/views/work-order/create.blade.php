@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Work Order</h1>
        <a href="{{ route('work-orders.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Konversi Form SA: {{ $formSa->id_form_sa }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('work-orders.store') }}" method="POST" id="formCreateWo">
                        @csrf
                        <input type="hidden" name="id_form_sa" value="{{ $formSa->id_form_sa }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Konsumen</label>
                                <input type="text" class="form-control" value="{{ $formSa->customer->nama_customer }} ({{ $formSa->no_plat }})" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keluhan</label>
                                <textarea class="form-control" readonly rows="2">{{ $formSa->keluhan_konsumen }}</textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="id_mekanik" class="form-label fw-bold">Pilih Mekanik <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="id_mekanik" name="id_mekanik" required>
                                <option value="">-- Pilih Mekanik --</option>
                                @foreach($mekaniks as $mekanik)
                                    <option value="{{ $mekanik->honda_id_mekanik }}">{{ $mekanik->nama_mekanik }} ({{ $mekanik->honda_id_mekanik }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Buat Work Order & Lanjut ke Keranjang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endpush
