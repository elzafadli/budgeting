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
                                    <th width="150" class="small">Project:</th>
                                    <td class="small">{{ $budget->project ? $budget->project->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="small">Requestor:</th>
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

                            <hr>

                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start ps-3">
                                <h6 class="mb-3 small">Approval Action</h6>

                                <div class="mb-3">
                                    <label for="approval_note_{{ $budget->id }}" class="form-label small">Note <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-sm" id="approval_note_{{ $budget->id }}" rows="2" required placeholder="Add your note..."></textarea>
                                </div>

                                <form action="{{ route('approvals.approve', $budget) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="note" id="approve_note_hidden_{{ $budget->id }}">
                                    <button type="submit" class="btn btn-sm btn-success w-100 mb-2" onclick="var note = document.getElementById('approval_note_{{ $budget->id }}').value.trim(); if(!note) { alert('Please add a note before approving.'); return false; } document.getElementById('approve_note_hidden_{{ $budget->id }}').value = note; return confirm('Approve this budget request?')">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>

                                <form action="{{ route('approvals.reject', $budget) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="note" id="reject_note_hidden_{{ $budget->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger w-100" onclick="var note = document.getElementById('approval_note_{{ $budget->id }}').value.trim(); if(!note) { alert('Please add a note before rejecting.'); return false; } document.getElementById('reject_note_hidden_{{ $budget->id }}').value = note; return confirm('Reject this budget request?')">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </form>
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
