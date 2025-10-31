@extends('layouts.app')

@section('title', 'Create Realization')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h4"><i class="bi bi-cash-coin"></i> Create Budget Realization</h1>
            <p class="text-muted small">For Budget: {{ $budget->request_no }} - {{ $budget->title }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header py-2">
            <h6 class="mb-0 small">Budget Summary</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong class="small">Approved Amount:</strong> <span class="small">Rp {{ number_format($budget->approved_total, 0, ',', '.') }}</span>
                </div>
                <div class="col-md-6">
                    <strong class="small">Requestor:</strong> <span class="small">{{ $budget->user->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('realizations.store', $budget) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="realization_date" class="form-label small required">Realization Date</label>
                        <input type="date" class="form-control form-control-sm @error('realization_date') is-invalid @enderror" 
                               id="realization_date" name="realization_date" value="{{ old('realization_date', date('Y-m-d')) }}" required>
                        @error('realization_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label small">Note</label>
                    <textarea class="form-control form-control-sm @error('note') is-invalid @enderror" 
                              id="note" name="note" rows="2">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <h6 class="mb-3 small">Realization Items</h6>

                <div id="realization-items">
                    <div class="realization-item border p-3 mb-3 rounded">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label small required">Description</label>
                                <input type="text" class="form-control form-control-sm" name="items[0][description]" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small required">Amount</label>
                                <input type="text" class="form-control form-control-sm item-amount currency-input" name="items[0][amount]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Proof File (PDF/Image)</label>
                                <input type="file" class="form-control form-control-sm" name="items[0][proof_file]" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="add-realization-item">
                    <i class="bi bi-plus"></i> Add Item
                </button>

                <div class="alert alert-info py-2">
                    <strong class="small">Total Realized: Rp <span id="grand-total">0</span></strong>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-save"></i> Create Realization
                    </button>
                    <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;
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

// Initialize first item's currency input
$(document).ready(function() {
    initCurrencyMask('.item-amount');
});

document.getElementById('add-realization-item').addEventListener('click', function() {
    const container = document.getElementById('realization-items');
    const newItem = document.createElement('div');
    newItem.className = 'realization-item border p-3 mb-3 rounded position-relative';
    newItem.innerHTML = `
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-item">
            <i class="bi bi-x"></i>
        </button>
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label small required">Description</label>
                <input type="text" class="form-control form-control-sm" name="items[${itemIndex}][description]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small required">Amount</label>
                <input type="text" class="form-control form-control-sm item-amount currency-input" name="items[${itemIndex}][amount]" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Proof File (PDF/Image)</label>
                <input type="file" class="form-control form-control-sm" name="items[${itemIndex}][proof_file]" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
    `;
    container.appendChild(newItem);
    // Initialize currency mask for the new amount input
    const newAmount = newItem.querySelector('.item-amount');
    initCurrencyMask(newAmount);
    itemIndex++;
    updateTotal();
});

document.getElementById('realization-items').addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        e.target.closest('.realization-item').remove();
        updateTotal();
    }
});

document.getElementById('realization-items').addEventListener('input', function(e) {
    if (e.target.classList.contains('item-amount')) {
        updateTotal();
    }
});

function updateTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.item-amount').forEach(input => {
        const amount = AutoNumeric.getNumber(input) || 0;
        grandTotal += amount;
    });
    document.getElementById('grand-total').textContent = grandTotal.toLocaleString('id-ID');
}

updateTotal();
</script>
@endpush
