@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h4"><i class="bi bi-speedometer2"></i> Dashboard</h1>
            <p class="text-muted small">Welcome, {{ Auth::user()->name }} ({{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }})</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        @if(Auth::user()->role === 'admin')
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Total Budgets</h6>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Draft</h6>
                        <h4 class="mb-0">{{ $stats['draft'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Submitted</h6>
                        <h4 class="mb-0">{{ $stats['submitted'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Approved</h6>
                        <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Pending Approvals</h6>
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Approved</h6>
                        <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body py-2">
                        <h6 class="card-title small mb-1">Rejected</h6>
                        <h4 class="mb-0">{{ $stats['rejected'] }}</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Recent Budgets Table --}}
    <div class="card">
        <div class="card-header py-2">
            <h6 class="mb-0">Recent Budget Requests</h6>
        </div>
        <div class="card-body">
            @if($recentBudgets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="small">Request No</th>
                                @if(Auth::user()->role !== 'admin')
                                    <th class="small">Requestor</th>
                                @endif
                                <th class="small">Title</th>
                                <th class="small">Amount</th>
                                <th class="small">Status</th>
                                <th class="small">Date</th>
                                <th class="small">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBudgets as $budget)
                                <tr>
                                    <td class="small">{{ $budget->request_no }}</td>
                                    @if(Auth::user()->role !== 'admin')
                                        <td class="small">{{ $budget->user->name }}</td>
                                    @endif
                                    <td class="small">{{ $budget->title }}</td>
                                    <td class="small">Rp {{ number_format($budget->total_amount, 0, ',', '.') }}</td>
                                    <td class="small">
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
                                    </td>
                                    <td class="small">{{ $budget->created_at->format('d M Y') }}</td>
                                    <td class="small">
                                        <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small mb-0">No budget requests found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
