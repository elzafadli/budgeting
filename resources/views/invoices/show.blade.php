@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 small">Invoice Information</h6>
                    <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'sent' ? 'bg-primary' : ($invoice->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <small class="text-muted">Invoice Number</small>
                            <p class="mb-0 small fw-bold">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Invoice Date</small>
                            <p class="mb-0 small">{{ $invoice->invoice_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Created At</small>
                            <p class="mb-0 small">{{ $invoice->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <small class="text-muted">Project</small>
                            <p class="mb-0 small">
                                <a href="{{ route('projects.show', $invoice->project) }}">
                                    {{ $invoice->project->no_project }} - {{ $invoice->project->name }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Vendor</small>
                            <p class="mb-0 small">{{ $invoice->project->vendor }}</p>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row">
                        <div class="col-md-12">
                            <small class="text-muted">Description</small>
                            <p class="mb-0 small">{{ $invoice->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($invoice->files->count() > 0)
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Attached Files</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($invoice->files as $file)
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-file-earmark-{{ 
                                    str_contains($file->file_type, 'pdf') ? 'pdf' : 
                                    (str_contains($file->file_type, 'image') ? 'image' : 
                                    (str_contains($file->file_type, 'word') ? 'word' : 
                                    (str_contains($file->file_type, 'excel') || str_contains($file->file_type, 'spreadsheet') ? 'excel' : 'text'))) 
                                }}"></i>
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="small">
                                    {{ $file->file_name }}
                                </a>
                                <small class="text-muted">({{ number_format($file->file_size / 1024, 2) }} KB)</small>
                            </div>
                            <a href="{{ Storage::url($file->file_path) }}" download class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Delete this invoice?')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Navigation</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
