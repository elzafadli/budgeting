@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="container-fluid px-4">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h5">Daftar Kategori</h1>
            <a href="{{ route('accounts.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> Tambah</a>
        </div>
        <div class="card-body">
            @if($accounts->count() > 0)
            <div class="table-responsive">
                <table id="accountsTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="small">Kategori</th>
                            <th class="small">Tipe</th>
                            <th class="small" width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td class="small">{{ $account->account_description }}</td>
                            <td class="small">{{ [
                                'asset' => 'Aset',
                                'liability' => 'Hutang',
                                'equity' => 'Modal',
                                'revenue' => 'Pendapatan',
                                'expense' => 'Biaya',
                            ][$account->account_type ?? ''] }}</td>
                            <td class="small">
                                <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">
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
            <p class="text-muted small mb-0">No accounts found. Create your first account to get started.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#accountsTable').DataTable({
            "pageLength": 10,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "search": "Search accounts:",
                "lengthMenu": "Show _MENU_ accounts per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ accounts",
                "infoEmpty": "No accounts available",
                "infoFiltered": "(filtered from _MAX_ total accounts)",
                "zeroRecords": "No matching accounts found"
            }
        });
    });
</script>
@endpush
