@extends('layouts.app')

@section('title', isset($accountBank) ? 'Edit Rekening Bank' : 'Tambah Rekening Bank')

@section('content')
<div class="container-fluid px-4">


    <form action="{{ isset($accountBank) ? route('account-banks.update', $accountBank) : route('account-banks.store') }}" method="POST">
        @csrf
        @if(isset($accountBank))
        @method('PUT')
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">{{ isset($accountBank) ? 'Edit' : 'Tambah' }} Rekening Bank</h5>

                <div class="mb-3">
                    <a href="{{ route('account-banks.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="account_holder_name" class="form-label small required">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control form-control-sm @error('account_holder_name') is-invalid @enderror"
                            id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $accountBank->account_holder_name ?? '') }}" required>
                        @error('account_holder_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="bank_name" class="form-label small required">Nama Bank</label>
                        <input type="text" class="form-control form-control-sm @error('bank_name') is-invalid @enderror"
                            id="bank_name" name="bank_name" value="{{ old('bank_name', $accountBank->bank_name ?? '') }}"
                            placeholder="Contoh: BCA, Mandiri, BNI, BRI" required>
                        @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="account_number" class="form-label small required">Nomor Rekening</label>
                        <input type="text" class="form-control form-control-sm @error('account_number') is-invalid @enderror"
                            id="account_number" name="account_number" value="{{ old('account_number', $accountBank->account_number ?? '') }}" required>
                        @error('account_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-save"></i> {{ isset($accountBank) ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
