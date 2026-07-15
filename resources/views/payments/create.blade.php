@extends('layouts.app')

@section('title', 'Create Payment - Inventory Management System')
@section('page-title', 'Create Payment')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2 text-primary"></i>New Payment
        </div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                @csrf

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Payment Number</label>
                        <input type="text" class="form-control" value="{{ $paymentNumber }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                        <select name="type" id="paymentType" class="form-select @error('type') is-invalid @enderror" required onchange="togglePaymentType()">
                            <option value="">Select Type</option>
                            <option value="purchase" {{ old('type') == 'purchase' ? 'selected' : '' }}>Pay to Supplier (Purchase)</option>
                            <option value="sales" {{ old('type') == 'sales' ? 'selected' : '' }}>Receive from Customer (Sales)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2" id="purchaseSection" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Purchase Order <span class="text-danger">*</span></label>
                        <select name="purchase_order_id" class="form-select @error('purchase_order_id') is-invalid @enderror" onchange="loadPurchaseOrder(this)">
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

                <div class="row g-3 mt-2" id="salesSection" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label">Sales Order <span class="text-danger">*</span></label>
                        <select name="sales_order_id" class="form-select @error('sales_order_id') is-invalid @enderror" onchange="loadSalesOrder(this)">
                            <option value="">Select Sales Order</option>
                            @foreach($salesOrders as $so)
                                <option value="{{ $so->id }}" {{ old('sales_order_id') == $so->id ? 'selected' : '' }}>
                                    {{ $so->sales_number }} - {{ $so->customer->customer_name ?? 'N/A' }} (${{ number_format($so->total_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('sales_order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6" id="salesInfo" style="display: none;">
                        <div class="bg-light p-3 rounded">
                            <small class="text-secondary d-block">Customer: <strong id="soCustomer">-</strong></small>
                            <small class="text-secondary d-block">Total: <strong id="soTotal">-</strong></small>
                            <small class="text-secondary d-block">Remaining: <strong id="soRemaining" class="text-danger">-</strong></small>
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
    function togglePaymentType() {
        const type = document.getElementById('paymentType').value;
        document.getElementById('purchaseSection').style.display = type === 'purchase' ? 'flex' : 'none';
        document.getElementById('salesSection').style.display = type === 'sales' ? 'flex' : 'none';

        // Reset required attributes
        document.querySelector('[name="purchase_order_id"]').required = type === 'purchase';
        document.querySelector('[name="sales_order_id"]').required = type === 'sales';
    }

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

    function loadSalesOrder(select) {
        const soId = select.value;
        const infoDiv = document.getElementById('salesInfo');

        if (soId) {
            fetch(`/payments/sales-order/${soId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('soCustomer').textContent = data.sales_order.customer.customer_name;
                    document.getElementById('soTotal').textContent = `$${parseFloat(data.sales_order.total_amount).toFixed(2)}`;
                    document.getElementById('soRemaining').textContent = `$${parseFloat(data.remaining).toFixed(2)}`;
                    document.getElementById('amount').max = data.remaining;
                    infoDiv.style.display = 'block';
                });
        } else {
            infoDiv.style.display = 'none';
        }
    }

    // Restore state on validation error
    document.addEventListener('DOMContentLoaded', function() {
        const type = document.getElementById('paymentType').value;
        if (type) {
            togglePaymentType();
            if (type === 'purchase' && document.querySelector('[name="purchase_order_id"]').value) {
                loadPurchaseOrder(document.querySelector('[name="purchase_order_id"]'));
            }
            if (type === 'sales' && document.querySelector('[name="sales_order_id"]').value) {
                loadSalesOrder(document.querySelector('[name="sales_order_id"]'));
            }
        }
    });
</script>
@endpush
