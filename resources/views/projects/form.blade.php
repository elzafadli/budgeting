@extends('layouts.app')

@section('title', isset($project) ? 'Edit Project' : 'Create Project')

@section('content')
<div class="container-fluid px-4">
    <form action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}" method="POST">
        @csrf
        @if(isset($project))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Project</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small required">Nama Project</label>
                        <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $project->name ?? '') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="vendor" class="form-label small required">Vendor</label>
                        <input type="text" class="form-control form-control-sm @error('vendor') is-invalid @enderror"
                            id="vendor" name="vendor" value="{{ old('vendor', $project->vendor ?? '') }}" required>
                        @error('vendor')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="no_project" class="form-label small required">No. Project</label>
                        <input type="text" class="form-control form-control-sm @error('no_project') is-invalid @enderror"
                            id="no_project" name="no_project" value="{{ old('no_project', $project->no_project ?? '') }}" required>
                        @error('no_project')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label small required">Tanggal Mulai</label>
                        <input type="date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                            id="start_date" name="start_date" value="{{ old('start_date', isset($project) ? $project->start_date->format('Y-m-d') : '') }}" required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label small">Tanggal Berakhir</label>
                        <input type="date" class="form-control form-control-sm @error('end_date') is-invalid @enderror"
                            id="end_date" name="end_date" value="{{ old('end_date', isset($project) && $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                        @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label small required">Total Anggaran</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control form-control-sm currency-input @error('amount') is-invalid @enderror"
                                id="amount" name="amount" value="{{ old('amount', $project->amount ?? '') }}" required>
                            @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label small required">Status Project</label>
                        <select class="form-select form-select-sm @error('status') is-invalid @enderror"
                            id="status" name="status" required>
                            <option value="in_progress" {{ old('status', $project->status ?? 'in_progress') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $project->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="canceled" {{ old('status', $project->status ?? '') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small">Deskripsi</label>
                    <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description', $project->description ?? '') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save"></i> {{ isset($project) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize currency input mask
    new AutoNumeric('#amount', {
        currencySymbol: '',
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        decimalPlaces: 2,
        minimumValue: '0',
        unformatOnSubmit: true
    });
});
</script>
@endpush
