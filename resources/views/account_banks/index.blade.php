@extends('layouts.app')

@section('title', 'Rekening Bank')

@section('content')
<div class="container-fluid px-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Rekening Bank</h5>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('account-banks.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah
            </a>
            @endif
        </div>
        <div class="card-body">
            @if($accountBanks->count() > 0)
            <div class="table-responsive">
                <table id="accountBanksTable" class="table table-striped table-bordered table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="small">Nama Pemilik</th>
                            <th class="small">Nomor Rekening</th>
                            <th class="small">Bank</th>
                            <th class="small">Dibuat</th>
                            <th class="small">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accountBanks as $accountBank)
                        <tr>
                            <td class="small">{{ $accountBank->account_holder_name }}</td>
                            <td class="small">{{ $accountBank->account_number }}</td>
                            <td class="small">{{ $accountBank->bank_name }}</td>
                            <td class="small">{{ $accountBank->created_at->format('d M Y') }}</td>
                            <td class="small">
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('account-banks.edit', $accountBank) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('account-banks.destroy', $accountBank) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus rekening ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted small mb-0">Belum ada rekening bank.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#accountBanksTable').DataTable({
            "pageLength": 10,
            "order": [
                [3, "desc"]
            ],
            "columnDefs": [{
                "orderable": false,
                "targets": -1
            }],
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "zeroRecords": "Tidak ada data yang cocok"
            }
        });
    });
</script>
@endpush
