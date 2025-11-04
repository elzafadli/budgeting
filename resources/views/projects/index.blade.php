@extends('layouts.app')

@section('title', 'Project')

@section('content')
<div class="container-fluid px-4">

    <div class="card">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h5 class="card-title">Project</h5>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah
            </a>
            @endif
        </div>
        <div class="card-body">
            @if($projects->count() > 0)
            <div class="table-responsive">
                <table id="projectsTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="small">Project No.</th>
                            <th class="small">Nama</th>
                            <th class="small">Vendor</th>
                            <th class="small">Tgl. Mulai</th>
                            <th class="small">Tgl. Selesai</th>
                            <th class="small">Total Anggaran</th>
                            <th class="small">Status</th>
                            <th class="small">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td class="small">{{ $project->no_project }}</td>
                            <td class="small">{{ $project->name }}</td>
                            <td class="small">{{ $project->vendor }}</td>
                            <td class="small">{{ $project->start_date->format('d M Y') }}</td>
                            <td class="small">{{ $project->end_date ? $project->end_date->format('d M Y') : '-' }}</td>
                            <td class="small">Rp {{ number_format($project->amount, 0, ',', '.') }}</td>
                            <td class="small">
                                <span class="badge bg-{{ $project->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td class="small">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted small mb-0">No projects found.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#projectsTable').DataTable({
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ],
            "columnDefs": [{
                "orderable": false,
                "targets": -1
            }],
            "language": {
                "search": "Search projects:",
                "lengthMenu": "Show _MENU_ projects per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ projects",
                "infoEmpty": "No projects available",
                "infoFiltered": "(filtered from _MAX_ total projects)",
                "zeroRecords": "No matching projects found"
            }
        });
    });
</script>
@endpush
