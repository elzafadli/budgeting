@extends('layouts.app')

@section('title', isset($realisasiBudget) ? 'Edit Realisasi' : 'Create Realisasi')

@section('content')
<div class="container-fluid px-4">
    <form action="{{ isset($realisasiBudget) ? route('realisasi-budgets.update', $realisasiBudget) : route('realisasi-budgets.store') }}" method="POST">
        @csrf
        @if(isset($realisasiBudget))
        @method('PUT')
        @endif

        <input type="hidden" name="budget_id" value="{{ $budget->id }}">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Form Realisasi Anggaran</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="realisasi_no" class="form-label small">
                            No. Realisasi
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Nomor akan digenerate otomatis setelah disimpan"></i>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="realisasi_no"
                            value="{{ isset($realisasiBudget) ? $realisasiBudget->realisasi_no : 'Auto Generate' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="budget_no" class="form-label small">No. Budget</label>
                        <input type="text" class="form-control form-control-sm" id="budget_no"
                            value="{{ $budget->request_no }}" disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="project" class="form-label small">Project</label>
                        <input type="text" class="form-control form-control-sm" id="project"
                            value="{{ $budget->project ? $budget->project->no_project . ' - ' . $budget->project->name : '-' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="realisasi_date" class="form-label small required">Tanggal Realisasi</label>
                        <input type="date" class="form-control form-control-sm @error('realisasi_date') is-invalid @enderror"
                            id="realisasi_date" name="realisasi_date" value="{{ old('realisasi_date', isset($realisasiBudget) ? $realisasiBudget->realisasi_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('realisasi_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small">Deskripsi</label>
                    <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description', $realisasiBudget->description ?? '') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <h6 class="mb-3 small">Item Realisasi</h6>

                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle"></i> Item budget yang tersedia:
                    <ul class="mb-0 mt-2">
                        @foreach($budget->items as $budgetItem)
                        <li>{{ $budgetItem->account->account_description }} - Rp {{ number_format($budgetItem->total_price, 0, ',', '.') }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="small" width="35%">Kategori</th>
                                <th class="small" width="30%">Uraian</th>
                                <th class="small" width="25%">Jumlah</th>
                                <th class="small text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="realisasi-items">
                            @if(isset($realisasiBudget) && $realisasiBudget->items->count() > 0)
                            @foreach($realisasiBudget->items as $index => $item)
                            <tr class="realisasi-item">
                                <td>
                                    <select class="form-select form-select-sm" name="items[{{ $index }}][account_id]" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old("items.$index.account_id", $item->account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_description }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][budget_item_id]" value="{{ old("items.$index.budget_item_id", $item->budget_item_id) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        name="items[{{ $index }}][remarks]" value="{{ old("items.$index.remarks", $item->remarks) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm currency-input total-price"
                                        name="items[{{ $index }}][total_price]" value="{{ old("items.$index.total_price", $item->total_price) }}" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            @foreach($budget->items as $index => $budgetItem)
                            <tr class="realisasi-item">
                                <td>
                                    <select class="form-select form-select-sm" name="items[{{ $index }}][account_id]" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $budgetItem->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_description }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][budget_item_id]" value="{{ $budgetItem->id }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="items[{{ $index }}][remarks]" value="{{ $budgetItem->remarks }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm currency-input total-price" name="items[{{ $index }}][total_price]" value="{{ $budgetItem->total_price }}" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="add-item">
                    <i class="bi bi-plus"></i> Tambah Item
                </button>

                <div class="alert alert-info py-2">
                    <strong class="small">Total: Rp <span id="grand-total">0</span></strong>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save"></i> {{ isset($realisasiBudget) ? 'Update' : 'Simpan' }}
                    </button>
                    <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ isset($realisasiBudget) ? $realisasiBudget->items->count() : $budget->items->count() }};
    let currencyInstances = [];

    // Currency format options
    const currencyOptions = {
        currencySymbol: '',
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        decimalPlaces: 2,
        minimumValue: '0',
        unformatOnSubmit: true
    };

    // Initialize currency mask for existing inputs
    function initCurrencyMask(element) {
        const instance = new AutoNumeric(element, currencyOptions);
        currencyInstances.push(instance);
        return instance;
    }

    // Initialize all currency inputs and tooltips
    $(document).ready(function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize currency inputs
        document.querySelectorAll('.total-price').forEach(input => {
            initCurrencyMask(input);
        });
        updateGrandTotal();
    });

    document.getElementById('add-item').addEventListener('click', function() {
        const tbody = document.getElementById('realisasi-items');
        const newRow = document.createElement('tr');
        newRow.className = 'realisasi-item';
        newRow.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="items[${itemIndex}][account_id]" required>
                <option value="">-- Pilih --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->account_description }}</option>
                @endforeach
            </select>
            <input type="hidden" name="items[${itemIndex}][budget_item_id]" value="">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${itemIndex}][remarks]">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm currency-input total-price" name="items[${itemIndex}][total_price]" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
        tbody.appendChild(newRow);

        // Initialize Select2 on the new select element
        $(newRow).find('select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true
        });

        // Initialize currency mask for the new price input
        const newPrice = newRow.querySelector('.total-price');
        initCurrencyMask(newPrice);
        itemIndex++;
        updateGrandTotal();
    });

    document.getElementById('realisasi-items').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            // Only allow removal if there's more than one row
            if (document.querySelectorAll('#realisasi-items tr').length > 1) {
                row.remove();
                updateGrandTotal();
            } else {
                alert('Minimal harus ada 1 item realisasi');
            }
        }
    });

    document.getElementById('realisasi-items').addEventListener('input', function(e) {
        if (e.target.classList.contains('total-price')) {
            updateGrandTotal();
        }
    });

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.total-price').forEach(input => {
            const amount = AutoNumeric.getNumber(input) || 0;
            grandTotal += amount;
        });
        document.getElementById('grand-total').textContent = grandTotal.toLocaleString('id-ID');
    }
</script>
@endpush
