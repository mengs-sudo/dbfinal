@extends('layouts.app')

@section('title', 'Stock In - Inventory Management System')
@section('page-title', 'Stock In')

@section('content')

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-number">{{ number_format($totalTransactions) }}</div>
                    <div class="stat-desc">All stock-in records</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Units Received</div>
                    <div class="stat-number">{{ number_format($totalReceived) }}</div>
                    <div class="stat-desc">All time</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Today Received</div>
                    <div class="stat-number">{{ number_format($todayReceived) }}</div>
                    <div class="stat-desc">Units received today</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Value Received</div>
                    <div class="stat-number">${{ number_format($totalValue, 2) }}</div>
                    <div class="stat-desc">All time purchase value</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title">
                <i class="fas fa-arrow-circle-down me-2 text-primary"></i>Stock In History
            </h6>
            <div class="table-toolbar">
                <form action="{{ route('stock.in') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search item or order no..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('stock.in') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Purchase Order
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Purchase No.</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Qty Received</th>
                        <th>Unit Cost</th>
                        <th>Total</th>
                        <th>Supplier</th>
                        <th>Received By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $item)
                        <tr>
                            <td>
                                <span class="code-badge">{{ $item->purchaseOrder->purchase_number ?? 'N/A' }}</span>
                            </td>
                            <td><span class="code-badge">{{ $item->inventoryItem->item_code ?? 'N/A' }}</span></td>
                            <td>{{ $item->inventoryItem->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->inventoryItem->category ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success" style="font-size: 13px; font-weight: 500; padding: 4px 10px; border-radius: 6px;">
                                    +{{ $item->quantity }}
                                </span>
                            </td>
                            <td>${{ number_format($item->unit_cost, 2) }}</td>
                            <td>${{ number_format($item->total, 2) }}</td>
                            <td>{{ $item->purchaseOrder->supplier->supplier_name ?? 'N/A' }}</td>
                            <td>{{ $item->purchaseOrder->createdBy->name ?? 'N/A' }}</td>
                            <td>{{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View Purchase Order"
                                        onclick="window.location.href='{{ route('purchases.show', $item->purchase_order_id) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <i class="fas fa-arrow-circle-down"></i>
                                    <h6>No Stock In Records Found</h6>
                                    <p>Stock in records will appear here when purchase orders are created.</p>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Purchase Order
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
                Showing {{ $stockIns->firstItem() ?? 0 }} to {{ $stockIns->lastItem() ?? 0 }}
                of {{ $stockIns->total() }} records
            </div>
            {{ $stockIns->appends(['search' => $search])->links() }}
        </div>
    </div>

@endsection
