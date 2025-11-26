@extends('layouts.app')

@section('title', 'Realisasi Detail')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h4"><i class="bi bi-cash-coin"></i> Realisasi Detail</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('realisasi-budgets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Informasi Realisasi</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">No. Realisasi</label>
                            <div class="small fw-bold">{{ $realisasiBudget->realisasi_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <div>
                                @if($realisasiBudget->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($realisasiBudget->status === 'submitted')
                                    <span class="badge bg-primary">Submitted</span>
                                @elseif($realisasiBudget->status === 'pm_approved')
                                    <span class="badge bg-info">PM Approved</span>
                                @elseif($realisasiBudget->status === 'finance_approved')
                                    <span class="badge bg-success">Finance Approved</span>
                                @elseif($realisasiBudget->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-dark">Completed</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">No. Budget</label>
                            <div class="small">
                                <a href="{{ route('budgets.show', $realisasiBudget->budget) }}" target="_blank">
                                    {{ $realisasiBudget->budget->request_no }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Tanggal Realisasi</label>
                            <div class="small">{{ $realisasiBudget->realisasi_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Project</label>
                            <div class="small">
                                @if($realisasiBudget->budget->project)
                                    <a href="{{ route('projects.show', $realisasiBudget->budget->project) }}" target="_blank">
                                        {{ $realisasiBudget->budget->project->no_project }} - {{ $realisasiBudget->budget->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">User</label>
                            <div class="small">{{ $realisasiBudget->user->name }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Deskripsi</label>
                        <div class="small">{{ $realisasiBudget->description ?? '-' }}</div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Total Amount</label>
                            <div class="small fw-bold">Rp {{ number_format($realisasiBudget->total_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Dibuat: {{ $realisasiBudget->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Rincian Item Realisasi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="small">Kategori</th>
                                    <th class="small">Uraian</th>
                                    <th class="small text-end">QTY</th>
                                    <th class="small text-end">Harga Satuan</th>
                                    <th class="small text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($realisasiBudget->items as $item)
                                    <tr>
                                        <td class="small">{{ $item->account ? $item->account->account_description : '-' }}</td>
                                        <td class="small">{{ $item->remarks ?? '-' }}</td>
                                        <td class="small text-end"><strong>{{ $item->qty ?? 1 }}</strong></td>
                                        <td class="small text-end">Rp {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</td>
                                        <td class="small text-end"><strong>Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td colspan="4" class="small fw-bold text-end">Total:</td>
                                    <td class="small fw-bold text-end">Rp {{ number_format($realisasiBudget->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if(Auth::user()->id === $realisasiBudget->user_id)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('realisasi-budgets.edit', $realisasiBudget) }}" class="btn btn-sm btn-warning w-100 mb-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> You can edit your own realization anytime
                        </small>
                    </div>
                </div>
            @endif

            @if($realisasiBudget->files->count() > 0)
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">File Pendukung</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($realisasiBudget->files as $file)
                        <li class="list-group-item px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small">
                                    <i class="bi bi-file-earmark"></i>
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="text-decoration-none">
                                        {{ $file->file_name }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ number_format($file->file_size / 1024, 2) }} KB
                                    </div>
                                </div>
                                <a href="{{ Storage::url($file->file_path) }}" download class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Budget Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Budget Amount</label>
                        <div class="small fw-bold">Rp {{ number_format($realisasiBudget->budget->total_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Budget Status</label>
                        <div>
                            @if($realisasiBudget->budget->status === 'completed')
                                <span class="badge bg-dark">Completed</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($realisasiBudget->budget->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('budgets.show', $realisasiBudget->budget) }}" class="btn btn-sm btn-outline-primary w-100 mt-2" target="_blank">
                        <i class="bi bi-eye"></i> View Budget
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
