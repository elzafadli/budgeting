@extends('layouts.app')

@section('title', 'Budget Realizations')

@section('content')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Realisasi Anggaran</h5>
        </div>
        <div class="card-body">
            @if($realisasiBudgets->count() > 0)
                <div class="table-responsive">
                    <table id="realisasiTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="small">Realisasi No</th>
                                <th class="small">Budget No</th>
                                <th class="small">User</th>
                                <th class="small">Amount</th>
                                <th class="small">Status</th>
                                <th class="small">Date</th>
                                <th class="small">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($realisasiBudgets as $realisasi)
                                <tr>
                                    <td class="small">{{ $realisasi->realisasi_no }}</td>
                                    <td class="small">{{ $realisasi->budget->request_no }}</td>
                                    <td class="small">{{ $realisasi->user->name }}</td>
                                    <td class="small">Rp {{ number_format($realisasi->total_amount, 0, ',', '.') }}</td>
                                    <td class="small">
                                        @if($realisasi->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($realisasi->status === 'submitted')
                                            <span class="badge bg-primary">Submitted</span>
                                        @elseif($realisasi->status === 'pm_approved')
                                            <span class="badge bg-info">PM Approved</span>
                                        @elseif($realisasi->status === 'finance_approved')
                                            <span class="badge bg-success">Finance Approved</span>
                                        @elseif($realisasi->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-dark">Completed</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $realisasi->realisasi_date->format('d M Y') }}</td>
                                    <td class="small">
                                        <a href="{{ route('realisasi-budgets.show', $realisasi) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">No budget realizations found.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#realisasiTable').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": -1 }
        ],
        "language": {
            "search": "Search realizations:",
            "lengthMenu": "Show _MENU_ realizations per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ realizations",
            "infoEmpty": "No realizations available",
            "infoFiltered": "(filtered from _MAX_ total realizations)",
            "zeroRecords": "No matching realizations found"
        }
    });
});
</script>
@endpush
