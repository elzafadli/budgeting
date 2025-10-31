@extends('layouts.app')

@section('title', 'Realization Detail')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h4"><i class="bi bi-cash-coin"></i> Realization Detail</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('budgets.show', $realization->budget) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Budget
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Realization Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="200" class="small">Realization No:</th>
                            <td class="small">{{ $realization->realization_no }}</td>
                        </tr>
                        <tr>
                            <th class="small">Budget Request:</th>
                            <td class="small">{{ $realization->budget->request_no }} - {{ $realization->budget->title }}</td>
                        </tr>
                        <tr>
                            <th class="small">Realization Date:</th>
                            <td class="small">{{ $realization->realization_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th class="small">Realized By:</th>
                            <td class="small">{{ $realization->realizedByUser->name }}</td>
                        </tr>
                        <tr>
                            <th class="small">Total Realized:</th>
                            <td class="small"><strong class="text-success">Rp {{ number_format($realization->total_realized, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th class="small">Approved Amount:</th>
                            <td class="small">Rp {{ number_format($realization->budget->approved_total, 0, ',', '.') }}</td>
                        </tr>
                        @if($realization->note)
                        <tr>
                            <th class="small">Note:</th>
                            <td class="small">{{ $realization->note }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th class="small">Status:</th>
                            <td class="small"><span class="badge bg-dark">{{ ucfirst($realization->status) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Realization Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th width="50" class="small">#</th>
                                    <th class="small">Description</th>
                                    <th width="200" class="small">Amount</th>
                                    <th width="150" class="small">Proof File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($realization->items as $index => $item)
                                    <tr>
                                        <td class="small">{{ $index + 1 }}</td>
                                        <td class="small">{{ $item->description }}</td>
                                        <td class="small"><strong>Rp {{ number_format($item->amount, 0, ',', '.') }}</strong></td>
                                        <td class="small">
                                            @if($item->proof_file)
                                                <a href="{{ asset('storage/' . $item->proof_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> View File
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td colspan="2" class="text-end small"><strong>Total:</strong></td>
                                    <td colspan="2" class="small"><strong class="text-success">Rp {{ number_format($realization->total_realized, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 small">Related Budget</h6>
                </div>
                <div class="card-body">
                    <h6 class="small">{{ $realization->budget->title }}</h6>
                    <p class="text-muted small mb-2">{{ $realization->budget->request_no }}</p>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="small">Requestor:</th>
                            <td class="small">{{ $realization->budget->user->name }}</td>
                        </tr>
                        <tr>
                            <th class="small">Status:</th>
                            <td class="small">
                                @if($realization->budget->status === 'completed')
                                    <span class="badge bg-dark">Completed</span>
                                @else
                                    <span class="badge bg-success">Finance Approved</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <a href="{{ route('budgets.show', $realization->budget) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        View Budget Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
