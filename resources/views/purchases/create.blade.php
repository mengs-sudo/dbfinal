@extends('layouts.app')

@section('title', 'Create Purchase - Inventory Management System')
@section('page-title', 'Create Purchase Order')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2 text-primary"></i>New Purchase Order
        </div>
        <div class="card-body">
            <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Purchase Number</label>
                        <input type="text" class="form-control" value="{{ $purchaseNumber }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }} ({{ $supplier->supplier_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Purchase Items</h6>
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
                                <th style="width: 20%;">Unit Cost <span class="text-danger">*</span></th>
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
                                                        {{ $inventoryItem->item_name }} ({{ $inventoryItem->item_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm quantity" value="{{ $item['quantity'] }}" min="1" required oninput="calculateRowTotal(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $index }}][unit_cost]" class="form-control form-control-sm unit-cost" value="{{ $item['unit_cost'] }}" min="0" required oninput="calculateRowTotal(this)">
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
                @error('items.*.unit_cost')
                    <div class="text-danger mb-3">Please enter a valid unit cost for each item.</div>
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
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Purchase Order
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
            html += `<option value="${item.id}" data-cost="${item.unit_cost}" data-price="${item.selling_price}">${item.item_name} (${item.item_code}) - Stock: ${item.quantity}</option>`;
        });
        html += `</select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm quantity" value="1" min="1" required oninput="calculateRowTotal(this)">
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][unit_cost]" class="form-control form-control-sm unit-cost" value="0" min="0" required oninput="calculateRowTotal(this)">
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
        const unitCost = parseFloat(row.querySelector('.unit-cost').value) || 0;
        const total = quantity * unitCost;
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
