@extends('layouts.app')

@section('title', 'Laporan Pengajuan')

@section('content')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Laporan Pengajuan</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="filterBtn">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <button type="button" class="btn btn-sm btn-success" id="exportBtn">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="budgetDetailsTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="small">Project</th>
                            <th class="small">No. Document</th>
                            <th class="small">Tanggal</th>
                            <th class="small">Requestor</th>
                            <th class="small">Kategori</th>
                            <th class="small">Uraian</th>
                            <th class="small text-end">Qty</th>
                            <th class="small text-end">Total</th>
                            <th class="small">Status</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end small"><strong>Grand Total:</strong></th>
                            <th class="small" id="grandTotal">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Filter Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterSidebar" aria-labelledby="filterSidebarLabel" style="z-index: 1050;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="filterSidebarLabel">Filter Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="filterForm">
            <div class="mb-3">
                <label for="filterProject" class="form-label small">Project</label>
                <select class="form-select form-select-sm" id="filterProject" name="project_id">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="filterRequestor" class="form-label small">Requestor</label>
                <select class="form-select form-select-sm" id="filterRequestor" name="user_id">
                    <option value="">All Requestors</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="filterAccount" class="form-label small">Kategori</label>
                <select class="form-select form-select-sm" id="filterAccount" name="account_id">
                    <option value="">All Categories</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="filterStatus" class="form-label small">Status</label>
                <select class="form-select form-select-sm" id="filterStatus" name="status">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="pm_approved">PM Approved</option>
                    <option value="finance_approved">Finance Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-check-circle"></i> Apply Filter
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="resetFilter">
                    <i class="bi bi-x-circle"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let table;

$(document).ready(function() {
    // Initialize DataTable with server-side processing
    table = $('#budgetDetailsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('budget-details.getData') }}',
            data: function(d) {
                d.project_id = $('#filterProject').val();
                d.user_id = $('#filterRequestor').val();
                d.account_id = $('#filterAccount').val();
                d.status = $('#filterStatus').val();
            }
        },
        columns: [
            { data: 'project', name: 'projects.name' },
            { data: 'request_no', name: 'budgets.request_no' },
            { data: 'date', name: 'budgets.document_date' },
            { data: 'requestor', name: 'users.name' },
            { data: 'account', name: 'accounts.account_description' },
            { data: 'remarks', name: 'budget_items.remarks' },
            { data: 'qty', name: 'budget_items.qty' },
            { data: 'total_price', name: 'budget_items.total_price' },
            { data: 'status', name: 'budgets.status' }
        ],
        columnDefs: [
            { className: 'text-end', targets: [6, 7] }
        ],
        order: [[2, 'desc']],
        pageLength: 10,
        language: {
            processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();

            // Get grand total from server response
            const json = api.ajax.json();
            if (json && json.grandTotal !== undefined) {
                $('#grandTotal').html('<strong>Rp ' + new Intl.NumberFormat('id-ID').format(json.grandTotal) + '</strong>');
            }
        },
    });

    // Export button click
    $('#exportBtn').on('click', function() {
        const params = new URLSearchParams();
        const projectId = $('#filterProject').val();
        const userId = $('#filterRequestor').val();
        const accountId = $('#filterAccount').val();
        const status = $('#filterStatus').val();

        if (projectId) params.append('project_id', projectId);
        if (userId) params.append('user_id', userId);
        if (accountId) params.append('account_id', accountId);
        if (status) params.append('status', status);

        const url = '{{ route('budget-details.export') }}' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    });

    // Filter button click
    $('#filterBtn').on('click', function() {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('filterSidebar'));
        offcanvas.show();
    });

    // Apply filter
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('filterSidebar'));
        offcanvas.hide();
    });

    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#filterForm')[0].reset();
        table.ajax.reload();
        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('filterSidebar'));
        offcanvas.hide();
    });
});
</script>
@endpush
