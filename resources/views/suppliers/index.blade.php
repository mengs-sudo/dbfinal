@extends('layouts.app')

@section('title', 'Suppliers - Inventory Management System')
@section('page-title', 'Suppliers')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title"><i class="fas fa-truck me-2 text-primary"></i>Suppliers List</h6>
            <div class="table-toolbar">
                <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search suppliers..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Supplier Code</th>
                        <th>Supplier Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td><span class="code-badge">{{ $supplier->supplier_code }}</span></td>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td>{{ $supplier->phone ?? 'N/A' }}</td>
                            <td>{{ $supplier->email ?? 'N/A' }}</td>
                            <td>{{ $supplier->city ?? 'N/A' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View" onclick="window.location.href='{{ route('suppliers.show', $supplier) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-edit" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#editSupplierModal"
                                        onclick="editSupplier({{ $supplier->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-delete" title="Delete"
                                        onclick="confirmDelete('{{ route('suppliers.destroy', $supplier) }}', '{{ $supplier->supplier_name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-truck"></i>
                                    <h6>No Suppliers Found</h6>
                                    <p>Get started by adding your first supplier.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                        <i class="fas fa-plus"></i> Add Supplier
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
                Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} suppliers
            </div>
            {{ $suppliers->appends(['search' => $search])->links() }}
        </div>
    </div>

    {{-- Add Supplier Modal --}}
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Add Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" value="{{ \App\Models\Supplier::generateCode() }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name') }}" required>
                                @error('supplier_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                                @error('address')
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
                            <i class="fas fa-save"></i> Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Supplier Modal --}}
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="editSupplierForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier Code</label>
                                <input type="text" id="edit_supplier_code" class="form-control" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" name="supplier_name" id="edit_supplier_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" id="edit_phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" id="edit_state" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" id="edit_city" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Supplier
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
    function editSupplier(id) {
        fetch(`/suppliers/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_supplier_code').value = data.supplier_code;
                document.getElementById('edit_supplier_name').value = data.supplier_name;
                document.getElementById('edit_phone').value = data.phone || '';
                document.getElementById('edit_email').value = data.email || '';
                document.getElementById('edit_state').value = data.state || '';
                document.getElementById('edit_city').value = data.city || '';
                document.getElementById('edit_address').value = data.address || '';
                document.getElementById('editSupplierForm').action = `/suppliers/${id}`;
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
