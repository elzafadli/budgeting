@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Daftar Invoice</h5>
            <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Invoice
            </a>
        </div>
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table id="invoicesTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="small">Invoice Number</th>
                                <th class="small">Project</th>
                                <th class="small">Invoice Date</th>
                                <th class="small">Status</th>
                                <th class="small">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="small">{{ $invoice->invoice_number }}</td>
                                    <td class="small">{{ $invoice->project->no_project }} - {{ $invoice->project->name }}</td>
                                    <td class="small">{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td class="small">
                                        @if($invoice->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($invoice->status === 'sent')
                                            <span class="badge bg-primary">Sent</span>
                                        @elseif($invoice->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($invoice->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-dark">{{ ucfirst($invoice->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">No invoices found.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#invoicesTable').DataTable({
            order: [[2, 'desc']],
            pageLength: 25
        });
    });
</script>
@endpush
