@extends('layouts.app')

@section('title', 'Inventory - Inventory Management System')
@section('page-title', 'Inventory')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title"><i class="fas fa-boxes me-2 text-primary"></i>Inventory Items</h6>
            <div class="table-toolbar">
                <form action="{{ route('inventory.index') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search items..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Selling Price</th>
                        <th>Reorder Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->item_name }}" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                                @else
                                    <div style="width: 48px; height: 48px; border-radius: 8px; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-box text-secondary" style="font-size: 18px;"></i>
                                    </div>
                                @endif
                            </td>
                            <td><span class="code-badge">{{ $item->item_code }}</span></td>
                            <td>
                                {{ $item->item_name }}
                                @if($item->variants->isNotEmpty())
                                    <a href="{{ route('inventory.show', $item) }}" class="badge bg-secondary text-decoration-none ms-1" title="Has variants">
                                        {{ $item->variants->count() }} {{ Str::plural('variant', $item->variants->count()) }}
                                    </a>
                                @endif
                            </td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td class="{{ $item->isLowStock() ? 'low-stock' : '' }}">
                                {{ $item->quantity }}
                                @if($item->isLowStock())
                                    <span class="low-stock-badge ms-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </span>
                                @endif
                            </td>
                            <td>${{ number_format($item->unit_cost, 2) }}</td>
                            <td>${{ number_format($item->selling_price, 2) }}</td>
                            <td>{{ $item->reorder_level }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View" onclick="window.location.href='{{ route('inventory.show', $item) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-edit" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#editItemModal"
                                        onclick="editItem({{ $item->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-delete" title="Delete"
                                        onclick="confirmDelete('{{ route('inventory.destroy', $item) }}', '{{ $item->item_name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-boxes"></i>
                                    <h6>No Inventory Items Found</h6>
                                    <p>Get started by adding your first inventory item.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="pagination-info">
                Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items
            </div>
            {{ $items->appends(['search' => $search])->links() }}
        </div>
    </div>

    {{-- Add Item Modal --}}
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Add Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Item Code</label>
                                <input type="text" class="form-control" value="{{ \App\Models\InventoryItem::generateCode() }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror" value="{{ old('item_name') }}" required>
                                @error('item_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">-- None --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-secondary">Don't see the right category? <a href="{{ route('categories.index') }}">Manage categories</a>.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 0) }}" min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unit Cost <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="unit_cost" class="form-control @error('unit_cost') is-invalid @enderror" value="{{ old('unit_cost', 0) }}" min="0" required>
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', 0) }}" min="0" required>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_level" class="form-control @error('reorder_level') is-invalid @enderror" value="{{ old('reorder_level', 0) }}" min="0" required>
                                @error('reorder_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Item Image</label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                <small class="text-secondary">Accepted: JPG, PNG, GIF, WebP. Max 2MB.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Item Modal --}}
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="editItemForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Item Code</label>
                                <input type="text" id="edit_item_code" class="form-control" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="item_name" id="edit_item_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" id="edit_category_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unit Cost <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="unit_cost" id="edit_unit_cost" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="selling_price" id="edit_selling_price" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_level" id="edit_reorder_level" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Item Image</label>
                                <div id="editImagePreview" class="mb-2" style="display: none;">
                                    <img id="editImageTag" src="" alt="Current image" style="max-width: 150px; max-height: 150px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">
                                    <small class="d-block text-secondary mt-1">Current image. Upload a new one to replace.</small>
                                </div>
                                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                <small class="text-secondary">Accepted: JPG, PNG, GIF, WebP. Max 2MB. Leave empty to keep current image.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <h6>Are you sure?</h6>
                    <p class="text-secondary mb-0">You are about to delete <strong id="deleteItemName"></strong>. This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <form id="deleteForm" method="POST">
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
    function editItem(id) {
        fetch(`/inventory/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_item_code').value = data.item_code;
                document.getElementById('edit_item_name').value = data.item_name;
                document.getElementById('edit_category_id').value = data.category_id || '';
                document.getElementById('edit_quantity').value = data.quantity;
                document.getElementById('edit_unit_cost').value = data.unit_cost;
                document.getElementById('edit_selling_price').value = data.selling_price;
                document.getElementById('edit_reorder_level').value = data.reorder_level;
                document.getElementById('editItemForm').action = `/inventory/${id}`;

                const preview = document.getElementById('editImagePreview');
                const tag = document.getElementById('editImageTag');
                if (data.image_url) {
                    tag.src = data.image_url;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            });
    }

    function confirmDelete(url, name) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('deleteForm').action = url;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush