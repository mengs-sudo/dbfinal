@extends('layouts.app')

@section('title', 'Create Receipt - Inventory Management System')
@section('page-title', 'Create Sales Receipt')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2 text-primary"></i>New Sales Receipt
        </div>
        <div class="card-body">
            <form action="{{ route('receipts.store') }}" method="POST" id="receiptForm">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" class="form-control" value="{{ $receiptNumber }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sales Order <span class="text-danger">*</span></label>
                        <select name="sales_order_id" id="sales_order_id" class="form-select @error('sales_order_id') is-invalid @enderror" required onchange="loadSalesOrder()">
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
                    <div class="col-md-4">
                        <label class="form-label">Receipt Date <span class="text-danger">*</span></label>
                        <input type="date" name="receipt_date" class="form-control @error('receipt_date') is-invalid @enderror" value="{{ old('receipt_date', date('Y-m-d')) }}" required>
                        @error('receipt_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
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

                <div class="section-divider"></div>

                <div class="row g-3" id="salesOrderInfo" style="display: none;">
                    <div class="col-md-4">
                        <label class="form-label text-secondary">Customer</label>
                        <p class="fw-medium" id="soCustomer">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary">Total Amount</label>
                        <p class="fw-medium" id="soTotal">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary">Remaining Balance</label>
                        <p class="fw-medium text-danger" id="soRemaining">-</p>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('receipts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Record Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function loadSalesOrder() {
        const soId = document.getElementById('sales_order_id').value;
        const infoDiv = document.getElementById('salesOrderInfo');

        if (soId) {
            fetch(`/receipts/sales-order/${soId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('soCustomer').textContent = data.sales_order.customer.customer_name;
                    document.getElementById('soTotal').textContent = `$${parseFloat(data.sales_order.total_amount).toFixed(2)}`;
                    document.getElementById('soRemaining').textContent = `$${parseFloat(data.remaining).toFixed(2)}`;
                    document.getElementById('amount').max = data.remaining;
                    infoDiv.style.display = 'flex';
                });
        } else {
            infoDiv.style.display = 'none';
        }
    }

    // Load if already selected (on validation error)
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('sales_order_id').value) {
            loadSalesOrder();
        }
    });
</script>
@endpush
