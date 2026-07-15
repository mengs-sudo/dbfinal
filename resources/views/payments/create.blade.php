@extends('layouts.app')

@section('title', 'Create Payment - Inventory Management System')
@section('page-title', 'Create Purchase Payment')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2 text-primary"></i>New Purchase Payment
        </div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Payment Number</label>
                        <input type="text" class="form-control" value="{{ $paymentNumber }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Purchase Order <span class="text-danger">*</span></label>
                        <select name="purchase_order_id" class="form-select @error('purchase_order_id') is-invalid @enderror" onchange="loadPurchaseOrder(this)" required>
                            <option value="">Select Purchase Order</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                    {{ $po->purchase_number }} - {{ $po->supplier->supplier_name ?? 'N/A' }} (${{ number_format($po->total_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6" id="purchaseInfo" style="display: none;">
                        <div class="bg-light p-3 rounded">
                            <small class="text-secondary d-block">Supplier: <strong id="poSupplier">-</strong></small>
                            <small class="text-secondary d-block">Total: <strong id="poTotal">-</strong></small>
                            <small class="text-secondary d-block">Remaining: <strong id="poRemaining" class="text-danger">-</strong></small>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="0" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function loadPurchaseOrder(select) {
        const poId = select.value;
        const infoDiv = document.getElementById('purchaseInfo');

        if (poId) {
            fetch(`/payments/purchase-order/${poId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('poSupplier').textContent = data.purchase_order.supplier.supplier_name;
                    document.getElementById('poTotal').textContent = `$${parseFloat(data.purchase_order.total_amount).toFixed(2)}`;
                    document.getElementById('poRemaining').textContent = `$${parseFloat(data.remaining).toFixed(2)}`;
                    document.getElementById('amount').max = data.remaining;
                    infoDiv.style.display = 'block';
                });
        } else {
            infoDiv.style.display = 'none';
        }
    }

    // Restore state on validation error
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('[name="purchase_order_id"]').value) {
            loadPurchaseOrder(document.querySelector('[name="purchase_order_id"]'));
        }
    });
</script>
@endpush

