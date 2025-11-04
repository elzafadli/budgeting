@extends('layouts.app')

@section('title', isset($isCashier) && $isCashier ? 'Complete Pengajuan' : (isset($budget) ? 'Edit Budget' : 'Create Budget'))

@section('content')
<div class="container-fluid px-4">
    <form action="{{ isset($isCashier) && $isCashier ? route('budgets.cashier-update', $budget) : (isset($budget) ? route('budgets.update', $budget) : route('budgets.store')) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($budget))
        @method('PUT')
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ isset($isCashier) && $isCashier ? 'Form Pengajuan' : 'Form Pengajuan' }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="request_no" class="form-label small">
                            No. Dokumen
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Nomor akan digenerate otomatis setelah disimpan"></i>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="request_no"
                            value="{{ isset($budget) ? $budget->request_no : 'Auto Generate' }}" disabled>
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="project_id" class="form-label small required">Project</label>
                        <select {{ isset($isCashier) && $isCashier ? 'disabled' : 'required' }} class="form-select form-select-sm @error('project_id') is-invalid @enderror"
                            id="project_id" name="project_id">
                            <option value="">-- Pilih Project --</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', isset($budget) ? $budget->project_id : $selectedProject ?? '') == $project->id ? 'selected' : '' }}>
                                {{ $project->no_project }} - {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="document_date" class="form-label small required">Tanggal Dokumen</label>
                        <input type="date" class="form-control form-control-sm @error('document_date') is-invalid @enderror"
                            id="document_date" name="document_date" value="{{ old('document_date', isset($budget) ? $budget->document_date->format('Y-m-d') : date('Y-m-d')) }}" {{ isset($isCashier) && $isCashier ? 'disabled' : 'required' }}>
                        @error('document_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="account_to" class="form-label small">Transfer Ke</label>
                        <input type="text" class="form-control form-control-sm @error('account_to') is-invalid @enderror"
                            id="account_to" name="account_to" value="{{ old('account_to', $budget->account_to ?? '') }}" placeholder="Masukkan tujuan transfer" {{ isset($isCashier) && $isCashier ? 'disabled' : '' }}>
                        @error('account_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if(isset($isCashier) && $isCashier)
                    <div class="col-md-6">
                        <label for="account_from_id" class="form-label small required">Transfer Dari (Pilih Rekening Bank)</label>
                        <select class="form-select form-select-sm @error('account_from_id') is-invalid @enderror"
                            id="account_from_id" name="account_from_id" required>
                            <option value="">-- Pilih Rekening Bank --</option>
                            @foreach($accountBanks as $accountBank)
                            <option value="{{ $accountBank->id }}" {{ old('account_from_id', $budget->account_from_id ?? '') == $accountBank->id ? 'selected' : '' }}>
                                {{ $accountBank->bank_name }} - {{ $accountBank->account_number }} ({{ $accountBank->account_holder_name }})
                            </option>
                            @endforeach
                        </select>
                        @error('account_from_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih rekening bank sumber pembayaran</small>
                    </div>
                    @else
                    <div class="col-md-6"></div>
                    @endif

                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small">Deskripsi</label>
                    <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3" {{ isset($isCashier) && $isCashier ? 'disabled' : '' }}>{{ old('description', $budget->description ?? '') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small">Upload Files (Optional)</label>
                    <div id="file-upload-container">
                        <div class="file-upload-row mb-2">
                            <div class="input-group input-group-sm">
                                <input type="file" class="form-control form-control-sm @error('files.*') is-invalid @enderror"
                                    name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <button type="button" class="btn btn-sm btn-danger remove-file-btn" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-file-btn">
                        <i class="bi bi-plus"></i> Add File
                    </button>
                    <div class="form-text small">Max 2MB per file. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</div>
                    @error('files.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if(isset($budget) && $budget->files->count() > 0)
                    <div class="mt-2">
                        <small class="text-muted">Existing files:</small>
                        <ul class="list-unstyled small">
                            @foreach($budget->files as $file)
                            <li>
                                <i class="bi bi-file-earmark"></i>
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">{{ $file->file_name }}</a>
                                <span class="text-muted">({{ $file->file_size_formatted }})</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                @if(!(isset($isCashier) && $isCashier))
                <hr>

                <h6 class="mb-3 small">Item Anggaran</h6>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="small" width="30%">Uraian</th>
                                <th class="small" width="35%">Kategori</th>
                                <th class="small" width="25%">Jumlah</th>
                                <th class="small text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="budget-items">
                            @if(isset($budget) && $budget->items->count() > 0)
                            @foreach($budget->items as $index => $item)
                            <tr class="budget-item">
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        name="items[{{ $index }}][remarks]" value="{{ old("items.$index.remarks", $item->remarks) }}">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" name="items[{{ $index }}][account_id]" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old("items.$index.account_id", $item->account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_description }}
                                        </option>
                                        @endforeach
                                    </select>
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
                            <tr class="budget-item">
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="items[0][remarks]">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" name="items[0][account_id]" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_description }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm currency-input total-price" name="items[0][total_price]" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
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
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save"></i> {{ isset($isCashier) && $isCashier ? 'Complete & Save' : (isset($budget) ? 'Update' : 'Simpan') }}
                    </button>
                    <a href="{{ isset($budget) ? route('budgets.show', $budget) : route('budgets.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ isset($budget) ? $budget->items->count() : 1 }};
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
        const tbody = document.getElementById('budget-items');
        const newRow = document.createElement('tr');
        newRow.className = 'budget-item';
        newRow.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm" name="items[${itemIndex}][remarks]">
        </td>
        <td>
            <select class="form-select form-select-sm" name="items[${itemIndex}][account_id]" required>
                <option value="">-- Pilih --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->account_description }}</option>
                @endforeach
            </select>
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

    document.getElementById('budget-items').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            // Only allow removal if there's more than one row
            if (document.querySelectorAll('#budget-items tr').length > 1) {
                row.remove();
                updateGrandTotal();
            } else {
                alert('Minimal harus ada 1 item anggaran');
            }
        }
    });

    document.getElementById('budget-items').addEventListener('input', function(e) {
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

    // File upload functionality
    document.getElementById('add-file-btn').addEventListener('click', function() {
        const container = document.getElementById('file-upload-container');
        const newRow = document.createElement('div');
        newRow.className = 'file-upload-row mb-2';
        newRow.innerHTML = `
            <div class="input-group input-group-sm">
                <input type="file" class="form-control form-control-sm"
                    name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                <button type="button" class="btn btn-sm btn-danger remove-file-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        updateRemoveFileButtons();
    });

    // Remove file input
    document.getElementById('file-upload-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-file-btn')) {
            const row = e.target.closest('.file-upload-row');
            row.remove();
            updateRemoveFileButtons();
        }
    });

    // Update visibility of remove buttons
    function updateRemoveFileButtons() {
        const rows = document.querySelectorAll('.file-upload-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-file-btn');
            if (rows.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
</script>
@endpush
