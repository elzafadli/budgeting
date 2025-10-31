@extends('layouts.app')

@section('title', 'Pending Approvals')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h4"><i class="bi bi-check-circle"></i> Pending Approvals</h1>
            <p class="text-muted small">Review and approve/reject budget requests</p>
        </div>
    </div>

    @if($pendingApprovals->count() > 0)
        @foreach($pendingApprovals as $budget)
            <div class="card mb-3">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 small">{{ $budget->title }}</h6>
                            <small class="text-muted">{{ $budget->request_no }}</small>
                        </div>
                        <div>
                            @if($budget->status === 'submitted')
                                <span class="badge bg-primary">Submitted</span>
                            @else
                                <span class="badge bg-info">PM Approved</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th width="150" class="small">Requestor:</th>
                                    <td class="small">{{ $budget->user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="small">Description:</th>
                                    <td class="small">{{ $budget->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="small">Total Amount:</th>
                                    <td class="small"><strong>Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="small">Items:</th>
                                    <td class="small">{{ $budget->items->count() }} item(s)</td>
                                </tr>
                                <tr>
                                    <th class="small">Created:</th>
                                    <td class="small">{{ $budget->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>

                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start ps-3">
                                <h6 class="mb-3 small">Approval Action</h6>

                                <form action="{{ route('approvals.approve', $budget) }}" method="POST" class="mb-2">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small">Note (optional)</label>
                                        <textarea class="form-control form-control-sm" name="note" rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success w-100" onclick="return confirm('Approve this budget?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $budget->id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>

                                {{-- Reject Modal --}}
                                <div class="modal fade" id="rejectModal{{ $budget->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title small">Reject Budget Request</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('approvals.reject', $budget) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small required">Reason for Rejection</label>
                                                        <textarea class="form-control form-control-sm" name="note" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-danger">Reject Budget</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info py-2">
            <i class="bi bi-info-circle"></i> No pending approvals at the moment.
        </div>
    @endif
</div>
@endsection
