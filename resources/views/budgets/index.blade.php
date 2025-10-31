@extends('layouts.app')

@section('title', 'Budget Requests')

@section('content')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h5 class="card-title">Pengajuan Biaya</h5>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('budgets.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah
            </a>
            @endif
        </div>
        <div class="card-body">
            @if($budgets->count() > 0)
                <div class="table-responsive">
                    <table id="budgetsTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="small">Request No</th>
                                @if(Auth::user()->role !== 'admin')
                                    <th class="small">Requestor</th>
                                @endif
                                <th class="small">Amount</th>
                                <th class="small">Status</th>
                                <th class="small">Date</th>
                                <th class="small">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($budgets as $budget)
                                <tr>
                                    <td class="small">{{ $budget->request_no }}</td>
                                    @if(Auth::user()->role !== 'admin')
                                        <td class="small">{{ $budget->user->name }}</td>
                                    @endif
                                    <td class="small">Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</td>
                                    <td class="small">
                                        @if($budget->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($budget->status === 'submitted')
                                            <span class="badge bg-primary">Submitted</span>
                                        @elseif($budget->status === 'pm_approved')
                                            <span class="badge bg-info">PM Approved</span>
                                        @elseif($budget->status === 'finance_approved')
                                            <span class="badge bg-success">Finance Approved</span>
                                        @elseif($budget->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-dark">Completed</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $budget->created_at->format('d M Y') }}</td>
                                    <td class="small">
                                        <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">No budget requests found.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#budgetsTable').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": -1 }
        ],
        "language": {
            "search": "Search budgets:",
            "lengthMenu": "Show _MENU_ budgets per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ budgets",
            "infoEmpty": "No budgets available",
            "infoFiltered": "(filtered from _MAX_ total budgets)",
            "zeroRecords": "No matching budgets found"
        }
    });
});
</script>
@endpush
