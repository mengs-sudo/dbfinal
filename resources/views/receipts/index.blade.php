@extends('layouts.app')

@section('title', 'Receipts - Inventory Management System')
@section('page-title', 'Receipts')

@section('content')
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title"><i class="fas fa-receipt me-2 text-primary"></i>Receipts</h6>
            <div class="table-toolbar">
                <form action="{{ route('receipts.index') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search receipts..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <a href="{{ route('receipts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Receipt
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Sales Order</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                        <tr>
                            <td><span class="code-badge">{{ $receipt->receipt_number }}</span></td>
                            <td>{{ $receipt->salesOrder->sales_number ?? 'N/A' }}</td>
                            <td>{{ $receipt->salesOrder->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $receipt->receipt_date->format('d M Y') }}</td>
                            <td>${{ number_format($receipt->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</span>
                            </td>
                            <td>
                                @if($receipt->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($receipt->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $receipt->createdBy->name ?? 'System' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View" onclick="window.location.href='{{ route('receipts.show', $receipt) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-delete" title="Delete"
                                        onclick="confirmDelete('{{ route('receipts.destroy', $receipt) }}', '{{ $receipt->receipt_number }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    <h6>No Receipts Found</h6>
                                    <p>Get started by recording your first receipt.</p>
                                    <a href="{{ route('receipts.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> New Receipt
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
                Showing {{ $receipts->firstItem() ?? 0 }} to {{ $receipts->lastItem() ?? 0 }} of {{ $receipts->total() }} receipts
            </div>
            {{ $receipts->appends(['search' => $search])->links() }}
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
                    <p class="text-secondary mb-0">You are about to delete receipt <strong id="deleteItemName"></strong>. This action cannot be undone.</p>
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
