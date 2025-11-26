@extends('layouts.app')

@section('title', 'Budget Realizations')

@section('content')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Realisasi Anggaran</h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="filterBtn">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="realisasiTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="small">Project</th>
                            <th class="small">Realisasi No</th>
                            <th class="small">Document Date</th>
                            <th class="small">Budget No</th>
                            <th class="small">Requestor</th>
                            <th class="small">Amount</th>
                            <th class="small">Status</th>
                            <th class="small">Action</th>
                        </tr>
                    </thead>
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
            <!-- <div class="mb-3">
                <label for="filterStatus" class="form-label small">Status</label>
                <select class="form-select form-select-sm" id="filterStatus" name="status">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div> -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-check-circle"></i> Apply Filter
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="resetFilter">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#realisasiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('realisasi-budgets.getData') }}',
            data: function(d) {
                d.project_id = $('#filterProject').val();
                d.user_id = $('#filterRequestor').val();
                d.status = $('#filterStatus').val();
            }
        },
        columns: [
            { data: 'project', name: 'projects.name' },
            { data: 'realisasi_no', name: 'realisasi_budgets.realisasi_no' },
            { data: 'realisasi_date', name: 'realisasi_budgets.realisasi_date' },
            { data: 'budget_no', name: 'budgets.request_no' },
            { data: 'requestor', name: 'users.name' },
            { data: 'amount', name: 'realisasi_budgets.total_amount', orderable: false },
            { data: 'status', name: 'realisasi_budgets.status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 10,
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading...'
        }
    });

    // Open filter sidebar
    $('#filterBtn').click(function() {
        var filterSidebar = new bootstrap.Offcanvas(document.getElementById('filterSidebar'));
        filterSidebar.show();
    });

    // Apply filter
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        table.draw();
        var filterSidebar = bootstrap.Offcanvas.getInstance(document.getElementById('filterSidebar'));
        filterSidebar.hide();
    });

    // Reset filters
    $('#resetFilter').click(function() {
        $('#filterProject').val('');
        $('#filterRequestor').val('');
        $('#filterStatus').val('');
        table.draw();
    });
});
</script>
@endpush
