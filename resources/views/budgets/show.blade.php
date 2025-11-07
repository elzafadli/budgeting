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
                <i class="bi bi-printer"></i> Print
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
                            <label class="form-label small text-muted mb-1">Transfer Dari</label>
                            <div class="small">
                                @if($budget->accountFrom)
                                    {{ $budget->accountFrom->bank_name }} - {{ $budget->accountFrom->account_number }}
                                    <br><span class="text-muted">a/n {{ $budget->accountFrom->account_holder_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Transfer Ke</label>
                            <div class="small">{{ $budget->account_to ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
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
                                    <th class="small text-end" width="25%">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budget->items as $item)
                                    <tr>
                                        <td class="small">{{ $item->account ? $item->account->account_description : '-' }}</td>
                                        <td class="small">{{ $item->remarks ?? '-' }}</td>
                                        <td class="small text-end"><strong>Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary">
                                    <td colspan="2" class="small fw-bold text-end">Total:</td>
                                    <td class="small fw-bold text-end">Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info me-1">
                                        Preview
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if((Auth::user()->role === 'admin' || Auth::user()->role === 'cashier') && Auth::user()->id === $budget->user_id)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Actions</h6>
                    </div>
                    <div class="card-body">
                        @if(in_array($budget->status, ['draft', 'rejected']))
                            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-warning w-100 mb-2">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        @endif

                        @if(in_array($budget->status, ['draft']))
                            <form action="{{ route('budgets.submit', $budget) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary w-100" onclick="return confirm('Submit this budget request?')">
                                    <i class="bi bi-send"></i> {{ $budget->status === 'rejected' ? 'Resubmit' : 'Submit' }} for Approval
                                </button>
                            </form>
                        @endif

                        @if(in_array($budget->status, ['draft', 'rejected']))
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

            @if(Auth::user()->role === 'cashier' && $budget->status === 'finance_approved')
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Cashier Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('budgets.cashier-edit', $budget) }}" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-cash-coin"></i> Process Payment
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

            @php
                $userRole = Auth::user()->role;
                $canApprove = false;
                $pendingApproval = null;

                if (in_array($userRole, ['project_manager', 'finance'])) {
                    foreach($budget->approvals as $approval) {
                        if ($approval->status === 'pending' &&
                            (($userRole === 'project_manager' && $approval->role === 'project_manager') ||
                             ($userRole === 'finance' && $approval->role === 'finance'))) {
                            $canApprove = true;
                            $pendingApproval = $approval;
                            break;
                        }
                    }
                }
            @endphp

            @if($canApprove && $pendingApproval)
            <div class="card mb-3">
                <div class="card-header py-2 bg-warning bg-opacity-10">
                    <h6 class="mb-0 small text-warning"><i class="bi bi-exclamation-triangle"></i> Pending Your Approval</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">This budget request requires your approval as <strong>{{ ucfirst(str_replace('_', ' ', $pendingApproval->role)) }}</strong>.</p>

                    <form action="{{ route('approvals.approve', $budget) }}" method="POST" class="d-inline">
                        @csrf
                        <div class="mb-3">
                            <label for="approve_note" class="form-label small">Note (Optional)</label>
                            <textarea class="form-control form-control-sm" id="approve_note" name="note" rows="2" placeholder="Add approval note..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success me-2" onclick="return confirm('Approve this budget request?')">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </form>

                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('approvals.reject', $budget) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel">Reject Budget Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="reject_note" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="reject_note" name="note" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x-circle"></i> Reject Budget
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
