@extends('layouts.app')

@section('title', 'Budget Detail')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h4"><i class="bi bi-journal-text"></i> Budget Detail</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('budgets.print', $budget) }}" class="btn btn-sm btn-primary me-2" target="_blank">
                <i class="bi bi-printer"></i> Print PDF
            </a>
            <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Informasi Anggaran</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">No. Dokumen</label>
                            <div class="small fw-bold">{{ $budget->request_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <div>
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
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Project</label>
                            <div class="small">
                                @if($budget->project)
                                    <a href="{{ route('projects.show', $budget->project) }}">
                                        {{ $budget->project->no_project }} - {{ $budget->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Tanggal Dokumen</label>
                            <div class="small">{{ $budget->document_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Rekening Bank</label>
                            <div class="small">
                                @if($budget->accountBank)
                                    {{ $budget->accountBank->bank_name }} - {{ $budget->accountBank->account_number }}
                                    <br><span class="text-muted">a/n {{ $budget->accountBank->account_holder_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Pemohon</label>
                            <div class="small">{{ $budget->user->name }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Deskripsi</label>
                        <div class="small">{{ $budget->description ?? '-' }}</div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Total Amount</label>
                            <div class="small fw-bold">Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</div>
                        </div>
                        @if($budget->approved_total > 0)
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Approved Amount</label>
                            <div class="small fw-bold text-success">Rp {{ number_format($budget->approved_total, 0, ',', '.') }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">Dibuat: {{ $budget->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($budget->files->count() > 0)
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Attached Files</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($budget->files as $file)
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-file-earmark-{{ 
                                        str_contains($file->file_type, 'pdf') ? 'pdf' : 
                                        (str_contains($file->file_type, 'word') || str_contains($file->file_type, 'document') ? 'word' : 
                                        (str_contains($file->file_type, 'excel') || str_contains($file->file_type, 'spreadsheet') ? 'excel' : 
                                        (str_contains($file->file_type, 'image') ? 'image' : 'text'))) 
                                    }}"></i>
                                    <strong class="small">{{ $file->file_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $file->file_size_formatted }} • Uploaded {{ $file->created_at->format('d M Y H:i') }}</small>
                                </div>
                                <div>
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Rincian Item Anggaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="small" width="35%">Kategori</th>
                                    <th class="small" width="30%">Uraian</th>
                                    <th class="small" width="25%">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budget->items as $item)
                                    <tr>
                                        <td class="small">{{ $item->account ? $item->account->account_description : '-' }}</td>
                                        <td class="small">{{ $item->remarks ?? '-' }}</td>
                                        <td class="small"><strong>Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td colspan="2" class="small fw-bold text-end">Total:</td>
                                    <td class="small fw-bold">Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($budget->realizations->count() > 0)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Realizations</h6>
                    </div>
                    <div class="card-body">
                        @foreach($budget->realizations as $realization)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong class="small">{{ $realization->realization_no }}</strong><br>
                                        <small class="text-muted">{{ $realization->realization_date->format('d M Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="small text-success">Rp {{ number_format($realization->total_realized, 0, ',', '.') }}</strong><br>
                                        <a href="{{ route('realizations.show', $realization) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($budget->realisasiBudgets->count() > 0)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Realisasi Budgets</h6>
                    </div>
                    <div class="card-body">
                        @foreach($budget->realisasiBudgets as $realisasi)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong class="small">{{ $realisasi->realisasi_no }}</strong><br>
                                        <small class="text-muted">{{ $realisasi->realisasi_date->format('d M Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="small text-success">Rp {{ number_format($realisasi->total_amount, 0, ',', '.') }}</strong><br>
                                        <a href="{{ route('realisasi-budgets.show', $realisasi) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            @if(Auth::user()->role === 'admin' && Auth::user()->id === $budget->user_id)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Actions</h6>
                    </div>
                    <div class="card-body">
                        @if($budget->status === 'draft')
                            <form action="{{ route('budgets.submit', $budget) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary w-100" onclick="return confirm('Submit this budget request?')">
                                    <i class="bi bi-send"></i> Submit for Approval
                                </button>
                            </form>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Delete this budget?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'finance' && $budget->status === 'finance_approved')
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Finance Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('realizations.create', $budget) }}" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-cash-coin"></i> Create Realization
                        </a>
                    </div>
                </div>
            @endif

            @if($budget->status === 'completed')
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Realisasi Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('realisasi-budgets.create', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-cash-coin"></i> Create Realisasi Budget
                        </a>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Approval History</h6>
                </div>
                <div class="card-body">
                    @forelse($budget->approvals as $approval)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="small">{{ ucfirst(str_replace('_', ' ', $approval->role)) }}</strong>
                                    @if($approval->approver)
                                        <br><small class="text-muted">{{ $approval->approver->name }}</small>
                                    @endif
                                </div>
                                <div>
                                    @if($approval->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($approval->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>
                            @if($approval->note)
                                <p class="mb-1 mt-2"><small><em>"{{ $approval->note }}"</em></small></p>
                            @endif
                            @if($approval->approved_at)
                                <small class="text-muted">{{ $approval->approved_at->format('d M Y H:i') }}</small>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No approval records yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
