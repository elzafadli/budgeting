@extends('layouts.app')

@section('title', isset($account) ? 'Edit Account' : 'Create Account')

@section('content')
<div class="container-fluid px-4">
    <form action="{{ isset($account) ? route('accounts.update', $account) : route('accounts.store') }}" method="POST">
        @csrf
        @if(isset($account))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Kategori</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <!-- <div class="col-md-6">
                        <label for="account_number" class="form-label small required">Account Number</label>
                        <input type="text" class="form-control form-control-sm @error('account_number') is-invalid @enderror"
                               id="account_number" name="account_number" value="{{ old('account_number', $account->account_number ?? '') }}" required>
                        @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->

                    <div class="col-md-6">
                        <label for="account_description" class="form-label small required">Kategori</label>
                        <input type="text" class="form-control form-control-sm @error('account_description') is-invalid @enderror"
                               id="account_description" name="account_description" value="{{ old('account_description', $account->account_description ?? '') }}" required>
                        @error('account_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- <div class="col-md-6">
                        <label for="account_type" class="form-label small required">Tipe</label>
                        <select class="form-select form-select-sm @error('account_type') is-invalid @enderror"
                                id="account_type" name="account_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="asset" {{ old('account_type', $account->account_type ?? '') == 'asset' ? 'selected' : '' }}>Aset</option>
                            <option value="liability" {{ old('account_type', $account->account_type ?? '') == 'liability' ? 'selected' : '' }}>Hutang</option>
                            <option value="equity" {{ old('account_type', $account->account_type ?? '') == 'equity' ? 'selected' : '' }}>Modal</option>
                            <option value="revenue" {{ old('account_type', $account->account_type ?? '') == 'revenue' ? 'selected' : '' }}>Pendapatan</option>
                            <option value="expense" {{ old('account_type', $account->account_type ?? '') == 'expense' ? 'selected' : '' }}>Biaya</option>
                        </select>
                        @error('account_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->
                </div>

                <div class="row mb-3">

                    <!-- <div class="col-md-4">
                        <label for="account_level" class="form-label small required">Account Level</label>
                        <input type="number" class="form-control form-control-sm @error('account_level') is-invalid @enderror"
                               id="account_level" name="account_level" value="{{ old('account_level', $account->account_level ?? 1) }}" min="1" required>
                        @error('account_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->

                    <!-- <div class="col-md-4">
                        <label for="account_number_parent" class="form-label small">Parent Account</label>
                        <select class="form-select form-select-sm @error('account_number_parent') is-invalid @enderror"
                                id="account_number_parent" name="account_number_parent">
                            <option value="">-- No Parent --</option>
                            @foreach($parentAccounts as $parent)
                                <option value="{{ $parent->account_number }}"
                                    {{ old('account_number_parent', $account->account_number_parent ?? '') == $parent->account_number ? 'selected' : '' }}>
                                    {{ $parent->formatted_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('account_number_parent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->
                </div>
<!--
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="active_indicator" name="active_indicator"
                               {{ old('active_indicator', $account->active_indicator ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="active_indicator">
                            Active Account
                        </label>
                    </div>
                </div> -->
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save"></i> {{ isset($account) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection
