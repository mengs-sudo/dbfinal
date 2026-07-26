@extends('layouts.app')

@section('title', 'Stock Out - Inventory Management System')
@section('page-title', 'Stock Out')

@section('content')

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-number">{{ number_format($totalTransactions) }}</div>
                    <div class="stat-desc">All stock-out records</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Units Issued</div>
                    <div class="stat-number">{{ number_format($totalIssued) }}</div>
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
                    <div class="stat-label">Today Issued</div>
                    <div class="stat-number">{{ number_format($todayIssued) }}</div>
                    <div class="stat-desc">Units sold today</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-number">${{ number_format($totalRevenue, 2) }}</div>
                    <div class="stat-desc">All time sales value</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title">
                <i class="fas fa-arrow-circle-up me-2 text-danger"></i>Stock Out History
            </h6>
            <div class="table-toolbar">
                <form action="{{ route('stock.out') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search item or order no..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('stock.out') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Sales Order
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sales No.</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Qty Issued</th>
                        <th>Selling Price</th>
                        <th>Total</th>
                        <th>Customer</th>
                        <th>Sold By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $item)
                        <tr>
                            <td>
                                <span class="code-badge">{{ $item->salesOrder->sales_number ?? 'N/A' }}</span>
                            </td>
                            <td><span class="code-badge">{{ $item->inventoryItem->item_code ?? 'N/A' }}</span></td>
                            <td>{{ $item->inventoryItem->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->inventoryItem->category ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-danger" style="font-size: 13px; font-weight: 500; padding: 4px 10px; border-radius: 6px;">
                                    -{{ $item->quantity }}
                                </span>
                            </td>
                            <td>${{ number_format($item->selling_price, 2) }}</td>
                            <td>${{ number_format($item->total, 2) }}</td>
                            <td>{{ $item->salesOrder->customer->customer_name ?? 'N/A' }}</td>
                            <td>{{ $item->salesOrder->createdBy->name ?? 'N/A' }}</td>
                            <td>{{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View Sales Order"
                                        onclick="window.location.href='{{ route('sales.show', $item->sales_order_id) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-state">
                                    <i class="fas fa-arrow-circle-up"></i>
                                    <h6>No Stock Out Records Found</h6>
                                    <p>Stock out records will appear here when sales orders are created.</p>
                                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Sales Order
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
                Showing {{ $stockOuts->firstItem() ?? 0 }} to {{ $stockOuts->lastItem() ?? 0 }}
                of {{ $stockOuts->total() }} records
            </div>
            {{ $stockOuts->appends(['search' => $search])->links() }}
        </div>
    </div>

@endsection
