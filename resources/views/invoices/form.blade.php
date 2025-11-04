@extends('layouts.app')

@section('title', isset($invoice) ? 'Edit Invoice' : 'Create Invoice')

@section('content')
<div class="container-fluid px-4">
    <form action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($invoice))
        @method('PUT')
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ isset($invoice) ? 'Edit Invoice' : 'Form Invoice' }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label small">
                            Invoice Number
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Nomor akan digenerate otomatis setelah disimpan"></i>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="invoice_number"
                            value="{{ isset($invoice) ? $invoice->invoice_number : 'Auto Generate' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="invoice_date" class="form-label small required">Invoice Date</label>
                        <input type="date" class="form-control form-control-sm @error('invoice_date') is-invalid @enderror"
                            id="invoice_date" name="invoice_date" value="{{ old('invoice_date', isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('invoice_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label for="project_id" class="form-label small required">Project</label>
                        <select class="form-select form-select-sm @error('project_id') is-invalid @enderror"
                            id="project_id" name="project_id" required>
                            <option value="">-- Pilih Project --</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', isset($invoice) ? $invoice->project_id : '') == $project->id ? 'selected' : '' }}>
                                {{ $project->no_project }} - {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6"></div>

                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small">Description</label>
                    <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description', $invoice->description ?? '') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small">Upload Files (Optional)</label>
                    <div id="file-upload-container">
                        <div class="file-upload-row mb-2">
                            <div class="input-group input-group-sm">
                                <input type="file" class="form-control form-control-sm @error('files.*') is-invalid @enderror"
                                    name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <button type="button" class="btn btn-sm btn-danger remove-file-btn" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-file-btn">
                        <i class="bi bi-plus"></i> Add File
                    </button>
                    <div class="form-text small">Max 10MB per file. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</div>
                    @error('files.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if(isset($invoice) && $invoice->files->count() > 0)
                    <div class="mt-2">
                        <label class="form-label small">Existing Files:</label>
                        <ul class="list-group list-group-sm">
                            @foreach($invoice->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-1 small">
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank">
                                    <i class="bi bi-file-earmark"></i> {{ $file->file_name }}
                                </a>
                                <span class="badge bg-secondary">{{ number_format($file->file_size / 1024, 2) }} KB</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save"></i> {{ isset($invoice) ? 'Update' : 'Save' }}
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
        // File upload functionality
        let fileCount = 1;

        $('#add-file-btn').click(function() {
            fileCount++;
            const newRow = `
                <div class="file-upload-row mb-2">
                    <div class="input-group input-group-sm">
                        <input type="file" class="form-control form-control-sm"
                            name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <button type="button" class="btn btn-sm btn-danger remove-file-btn">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#file-upload-container').append(newRow);
        });

        $(document).on('click', '.remove-file-btn', function() {
            $(this).closest('.file-upload-row').remove();
        });

        // Show remove button on first row when there are multiple rows
        $(document).on('change', 'input[type="file"]', function() {
            if ($('.file-upload-row').length > 1) {
                $('.remove-file-btn').show();
            }
        });
    });
</script>
@endpush
