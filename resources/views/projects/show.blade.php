@extends('layouts.app')

@section('title', 'Project Detail')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h4"><i class="bi bi-folder"></i> Project Detail</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Project Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="200" class="small">Project Number:</th>
                            <td class="small">{{ $project->no_project }}</td>
                        </tr>
                        <tr>
                            <th class="small">Project Name:</th>
                            <td class="small">{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <th class="small">User:</th>
                            <td class="small">{{ $project->vendor }}</td>
                        </tr>
                        <tr>
                            <th class="small">Start Date:</th>
                            <td class="small">{{ $project->start_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="small">End Date:</th>
                            <td class="small">{{ $project->end_date ? $project->end_date->format('d M Y') : 'Ongoing' }}</td>
                        </tr>
                        <tr>
                            <th class="small">Project Value:</th>
                            <td class="small"><strong>Rp {{ number_format($project->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th class="small">Status:</th>
                            <td class="small">
                                <span class="badge bg-{{ $project->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                        </tr>
                        @if($project->description)
                        <tr>
                            <th class="small">Description:</th>
                            <td class="small">{{ $project->description }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th class="small">Created:</th>
                            <td class="small">{{ $project->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($project->budgets->count() > 0)
                <div class="card">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Related Budgets</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="small">No. Pengajuan</th>
                                        <th class="small">No. Realisasi</th>
                                        <th class="small">Dibuat Oleh</th>
                                        <th class="small">Total Pengajuan</th>
                                        <th class="small">Total Realisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->budgets as $budget)
                                        @php
                                            $realisasi = $budget->realisasiBudgets->first();
                                        @endphp
                                        <tr>
                                            <td class="small">
                                                <a href="{{ route('budgets.show', $budget) }}" class="text-decoration-none">
                                                    {{ $budget->request_no }}
                                                </a>
                                            </td>
                                            <td class="small">
                                                @if($realisasi)
                                                    <a href="{{ route('realisasi-budgets.show', $realisasi) }}" class="text-decoration-none">
                                                        {{ $realisasi->realisasi_no }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="small">{{ $budget->user->name }}</td>
                                            <td class="small">Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</td>
                                            <td class="small">
                                                @if($realisasi)
                                                    Rp {{ number_format($realisasi->total_amount, 0, ',', '.') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Project Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Total Pengajuan:</span>
                            <strong class="small">{{ $project->budgets->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Total Pengajuan:</span>
                            <strong class="small">Rp {{ number_format($project->budgets->sum('total_amount'), 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('budgets.create', ['project' => $project->id]) }}" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Create Budget for this Project
                        </a>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Budget Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="small text-muted">Submitted:</span>
                        <span class="badge bg-primary float-end">{{ $project->budgets->where('status', 'submitted')->count() }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="small text-muted">Finance Approved:</span>
                        <span class="badge bg-success float-end">{{ $project->budgets->whereIn('status', ['finance_approved'])->count() }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="small text-muted">PM Approved:</span>
                        <span class="badge bg-success float-end">{{ $project->budgets->whereIn('status', ['pm_approved'])->count() }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="small text-muted">Completed:</span>
                        <span class="badge bg-success float-end">{{ $project->budgets->where('status', 'completed')->count() }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="small text-muted">Rejected:</span>
                        <span class="badge bg-danger float-end">{{ $project->budgets->where('status', 'rejected')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
