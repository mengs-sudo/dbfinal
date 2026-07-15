@extends('layouts.app')

@section('title', 'Create Sale - Inventory Management System')
@section('page-title', 'Create Sales Order')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2 text-primary"></i>New Sales Order
        </div>
        <div class="card-body">
            <form action="{{ route('sales.store') }}" method="POST" id="salesForm">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Sales Number</label>
                        <input type="text" class="form-control" value="{{ $salesNumber }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->customer_name }} ({{ $customer->customer_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sales Date <span class="text-danger">*</span></label>
                        <input type="date" name="sales_date" class="form-control @error('sales_date') is-invalid @enderror" value="{{ old('sales_date', date('Y-m-d')) }}" required>
                        @error('sales_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Sales Items</h6>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Item <span class="text-danger">*</span></th>
                                <th style="width: 15%;">Quantity <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Selling Price <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Total</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                    <tr>
                                        <td>
                                            <select name="items[{{ $index }}][inventory_item_id]" class="form-select form-select-sm" required>
                                                <option value="">Select Item</option>
                                                @foreach($items as $inventoryItem)
                                                    <option value="{{ $inventoryItem->id }}" {{ $item['inventory_item_id'] == $inventoryItem->id ? 'selected' : '' }}>
                                                        {{ $inventoryItem->item_name }} ({{ $inventoryItem->item_code }}) - Stock: {{ $inventoryItem->quantity }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm quantity" value="{{ $item['quantity'] }}" min="1" required oninput="calculateRowTotal(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $index }}][selling_price]" class="form-control form-control-sm selling-price" value="{{ $item['selling_price'] }}" min="0" required oninput="calculateRowTotal(this)">
                                        </td>
                                        <td>
                                            <span class="row-total">$0.00</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-delete btn-sm" onclick="removeItemRow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                @error('items')
                    <div class="text-danger mb-3">{{ $message }}</div>
                @enderror
                @error('items.*.inventory_item_id')
                    <div class="text-danger mb-3">Please select an item for each row.</div>
                @enderror
                @error('items.*.quantity')
                    <div class="text-danger mb-3">Please enter a valid quantity for each item.</div>
                @enderror
                @error('items.*.selling_price')
                    <div class="text-danger mb-3">Please enter a valid selling price for each item.</div>
                @enderror

                <div class="section-divider"></div>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="total-display">
                            <small class="text-secondary d-block" style="font-size: 13px; font-weight: 400;">Total Amount</small>
                            <span id="grandTotal">$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Sales Order
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let itemIndex = {{ old('items') ? count(old('items')) : 0 }};

    function addItemRow() {
        const items = @json($items);
        let html = `<tr>
            <td>
                <select name="items[${itemIndex}][inventory_item_id]" class="form-select form-select-sm" required>
                    <option value="">Select Item</option>`;
        items.forEach(item => {
            html += `<option value="${item.id}" data-stock="${item.quantity}" data-cost="${item.unit_cost}" data-price="${item.selling_price}">${item.item_name} (${item.item_code}) - Stock: ${item.quantity}</option>`;
        });
        html += `</select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm quantity" value="1" min="1" required oninput="calculateRowTotal(this)">
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][selling_price]" class="form-control form-control-sm selling-price" value="0" min="0" required oninput="calculateRowTotal(this)">
            </td>
            <td>
                <span class="row-total">$0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-delete btn-sm" onclick="removeItemRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

        document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
        itemIndex++;
        updateGrandTotal();
    }

    function removeItemRow(button) {
        if (document.querySelectorAll('#itemsBody tr').length > 1) {
            button.closest('tr').remove();
            updateGrandTotal();
        } else {
            alert('You need at least one item.');
        }
    }

    function calculateRowTotal(input) {
        const row = input.closest('tr');
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const sellingPrice = parseFloat(row.querySelector('.selling-price').value) || 0;
        const total = quantity * sellingPrice;
        row.querySelector('.row-total').textContent = `$${total.toFixed(2)}`;
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.row-total').forEach(span => {
            const total = parseFloat(span.textContent.replace('$', '')) || 0;
            grandTotal += total;
        });
        document.getElementById('grandTotal').textContent = `$${grandTotal.toFixed(2)}`;
    }
</script>
@endpush
