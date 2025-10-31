@extends('layouts.app')

@section('title', 'Account Detail')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h4"><i class="bi bi-book"></i> Account Detail</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Account Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="200" class="small">Account Number:</th>
                            <td class="small">{{ $account->account_number }}</td>
                        </tr>
                        <tr>
                            <th class="small">Description:</th>
                            <td class="small">{{ $account->account_description }}</td>
                        </tr>
                        <tr>
                            <th class="small">Account Type:</th>
                            <td class="small">
                                <span class="badge bg-secondary">{{ ucfirst($account->account_type) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="small">Account Level:</th>
                            <td class="small">{{ $account->account_level }}</td>
                        </tr>
                        <tr>
                            <th class="small">Parent Account:</th>
                            <td class="small">
                                @if($account->parent)
                                    <a href="{{ route('accounts.show', $account->parent) }}">
                                        {{ $account->parent->formatted_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="small">Status:</th>
                            <td class="small">
                                @if($account->active_indicator)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="small">Created:</th>
                            <td class="small">{{ $account->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($account->children->count() > 0)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0 small">Child Accounts</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="small">Account Number</th>
                                        <th class="small">Description</th>
                                        <th class="small">Type</th>
                                        <th class="small">Status</th>
                                        <th class="small">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($account->children as $child)
                                        <tr>
                                            <td class="small">{{ $child->account_number }}</td>
                                            <td class="small">{{ $child->account_description }}</td>
                                            <td class="small"><span class="badge bg-secondary">{{ ucfirst($child->account_type) }}</span></td>
                                            <td class="small">
                                                @if($child->active_indicator)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <a href="{{ route('accounts.show', $child) }}" class="btn btn-sm btn-outline-primary">View</a>
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
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Usage Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Budget Items:</span>
                            <strong class="small">{{ $account->budgetItems->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Child Accounts:</span>
                            <strong class="small">{{ $account->children->count() }}</strong>
                        </div>
                        @if($account->budgetItems->count() > 0)
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted">Total Amount:</span>
                                <strong class="small">Rp {{ number_format($account->budgetItems->sum('total_price'), 0, ',', '.') }}</strong>
                            </div>
                        @endif
                    </div>

                    @if($account->budgetItems->count() === 0 && $account->children->count() === 0)
                        <div class="alert alert-info py-2 mb-0">
                            <small><i class="bi bi-info-circle"></i> This account is not being used yet.</small>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-0">
                            <small><i class="bi bi-exclamation-triangle"></i> Cannot delete this account as it's in use.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
