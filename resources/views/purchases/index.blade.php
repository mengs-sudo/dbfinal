@extends('layouts.app')

@section('title', 'Purchases - Inventory Management System')
@section('page-title', 'Purchases')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title"><i class="fas fa-shopping-cart me-2 text-primary"></i>Purchase Orders</h6>
            <div class="table-toolbar">
                <form action="{{ route('purchases.index') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search purchases..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Purchase
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Purchase #</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td><span class="code-badge">{{ $purchase->purchase_number }}</span></td>
                            <td>{{ $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                            <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td>${{ number_format($purchase->total_amount, 2) }}</td>
                            <td>${{ number_format($purchase->paid_amount, 2) }}</td>
                            <td>
                                @if($purchase->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($purchase->status == 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>{{ $purchase->createdBy->name ?? 'System' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View" onclick="window.location.href='{{ route('purchases.show', $purchase) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-delete" title="Delete"
                                        onclick="confirmDelete('{{ route('purchases.destroy', $purchase) }}', '{{ $purchase->purchase_number }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h6>No Purchase Orders Found</h6>
                                    <p>Get started by creating your first purchase order.</p>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> New Purchase
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="pagination-info">
                Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} purchases
            </div>
            {{ $purchases->appends(['search' => $search])->links() }}
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
                    <p class="text-secondary mb-0">You are about to delete purchase <strong id="deleteItemName"></strong>. This action cannot be undone.</p>
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
    function confirmDelete(url, name) {
        document.getElementById('deleteItemName').textContent = name;
        document.getElementById('deleteForm').action = url;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush
