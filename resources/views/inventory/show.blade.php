@extends('layouts.app')

@section('title', 'Item Details - Inventory Management System')
@section('page-title', 'Item Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($inventory->image)
                            <img src="{{ asset('storage/' . $inventory->image) }}" alt="{{ $inventory->item_name }}"
                                 style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border-color);">
                        @else
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-box" style="font-size: 32px; color: var(--primary);"></i>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $inventory->item_name }}</h5>
                    <span class="code-badge">{{ $inventory->item_code }}</span>
                    <div class="mt-3">
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Stock Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-secondary d-block">Quantity in Stock</small>
                        <span class="{{ $inventory->isLowStock() ? 'low-stock' : '' }}" style="font-size: 24px; font-weight: 700;">
                            {{ $inventory->quantity }}
                        </span>
                        @if($inventory->isLowStock())
                            <span class="low-stock-badge ms-2">Low Stock</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Reorder Level</small>
                        <span>{{ $inventory->reorder_level }}</span>
                    </div>
                    <div>
                        <small class="text-secondary d-block">Inventory Value</small>
                        <span class="fw-medium">${{ number_format($inventory->inventory_value, 2) }}</span>
                        <small class="text-secondary">(qty &times; unit cost)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tags me-2 text-primary"></i>Item Details
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Category</label>
                            <p class="fw-medium">{{ $inventory->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Unit Cost</label>
                            <p class="fw-medium">${{ number_format($inventory->unit_cost, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Selling Price</label>
                            <p class="fw-medium">${{ number_format($inventory->selling_price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Expected Profit</label>
                            <p class="fw-medium text-success">
                                ${{ number_format($inventory->selling_price - $inventory->unit_cost, 2) }}
                                <small class="text-secondary">per unit</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Variants --}}
            <div class="table-container mt-4">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-layer-group me-2 text-primary"></i>Product Variants</h6>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVariantModal">
                        <i class="fas fa-plus"></i> Add Variant
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Variant</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory->variants as $variant)
                                <tr>
                                    <td class="fw-medium">{{ $variant->variant_name }}</td>
                                    <td>{{ $variant->sku ?? 'N/A' }}</td>
                                    <td class="{{ $variant->isLowStock() ? 'low-stock' : '' }}">{{ $variant->quantity }}</td>
                                    <td>{{ $variant->reorder_level }}</td>
                                    <td>
                                        @if($variant->quantity == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($variant->isLowStock())
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button type="button" class="btn btn-edit" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#editVariantModal"
                                                onclick="editVariant({{ $variant->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-delete" title="Delete"
                                                onclick="confirmDeleteVariant('{{ route('variants.destroy', [$inventory, $variant]) }}', '{{ $variant->variant_name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No variants yet. This item is tracked as a single stock line.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Variant Modal --}}
    <div class="modal fade" id="addVariantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('variants.store', $inventory) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Add Variant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Variant Name <span class="text-danger">*</span></label>
                                <input type="text" name="variant_name" class="form-control" placeholder="e.g. Small, Red / L, 500ml" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">SKU / Barcode</label>
                                <input type="text" name="sku" class="form-control" placeholder="Optional, must be unique">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" value="0" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_level" class="form-control" value="0" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Variant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Variant Modal --}}
    <div class="modal fade" id="editVariantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editVariantForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Variant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Variant Name <span class="text-danger">*</span></label>
                                <input type="text" name="variant_name" id="edit_variant_name" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">SKU / Barcode</label>
                                <input type="text" name="sku" id="edit_variant_sku" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="edit_variant_quantity" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_level" id="edit_variant_reorder_level" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Variant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Variant Confirmation Modal --}}
    <div class="modal fade" id="deleteVariantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <h6>Are you sure?</h6>
                    <p class="text-secondary mb-0">You are about to delete the variant <strong id="deleteVariantName"></strong>. This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <form id="deleteVariantForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const inventoryItemId = {{ $inventory->id }};

    function editVariant(variantId) {
        fetch(`/inventory/${inventoryItemId}/variants/${variantId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_variant_name').value = data.variant_name;
                document.getElementById('edit_variant_sku').value = data.sku || '';
                document.getElementById('edit_variant_quantity').value = data.quantity;
                document.getElementById('edit_variant_reorder_level').value = data.reorder_level;
                document.getElementById('editVariantForm').action = `/inventory/${inventoryItemId}/variants/${variantId}`;
            });
    }

    function confirmDeleteVariant(url, name) {
        document.getElementById('deleteVariantName').textContent = name;
        document.getElementById('deleteVariantForm').action = url;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteVariantModal'));
        deleteModal.show();
    }
</script>
@endpush