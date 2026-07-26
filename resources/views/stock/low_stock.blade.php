@extends('layouts.app')

@section('title', 'Low Stock Alerts - Inventory Management System')
@section('page-title', 'Low Stock Alerts')

@section('content')

    {{-- Alert Banner --}}
    @if($totalLow > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert"
             style="border-radius: var(--radius-md); border: none; background: #FEF3C7; color: #92400E; padding: 16px 20px;">
            <i class="fas fa-exclamation-triangle me-3" style="font-size: 20px;"></i>
            <div>
                <strong>{{ $totalLow }} product(s) are below reorder level</strong>
                <div style="font-size: 13px; margin-top: 2px;">These items are running low and need to be restocked soon.</div>
            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Low Stock Items</div>
                    <div class="stat-number">{{ $lowStockCount }}</div>
                    <div class="stat-desc">Below reorder level</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Out of Stock</div>
                    <div class="stat-number">{{ $outOfStock }}</div>
                    <div class="stat-desc">Zero quantity</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Alerts</div>
                    <div class="stat-number">{{ $totalLow }}</div>
                    <div class="stat-desc">Needs attention</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <div class="table-header">
            <h6 class="table-title">
                <i class="fas fa-exclamation-circle me-2 text-warning"></i>Items Below Reorder Level
            </h6>
            <div class="table-toolbar">
                <form action="{{ route('stock.low-stock') }}" method="GET" class="d-flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search items..." value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if($search)
                        <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </form>
                <a href="{{ route('purchases.create') }}" class="btn btn-warning btn-sm" style="color: #fff;">
                    <i class="fas fa-shopping-cart"></i> Restock Now
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Current Qty</th>
                        <th>Reorder Level</th>
                        <th>Shortage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $shortage = $item->reorder_level - $item->quantity;
                            $isOutOfStock = $item->quantity == 0;
                        @endphp
                        <tr>
                            <td><span class="code-badge">{{ $item->item_code }}</span></td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->category ?? 'N/A' }}</td>
                            <td>
                                <span style="font-weight: 600; color: {{ $isOutOfStock ? 'var(--danger)' : 'var(--warning)' }};">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td>{{ $item->reorder_level }}</td>
                            <td>
                                <span style="color: var(--danger); font-weight: 600;">
                                    -{{ $shortage }}
                                </span>
                            </td>
                            <td>
                                @if($isOutOfStock)
                                    <span class="badge"
                                          style="background: #FEE2E2; color: #991B1B; font-size: 12px; padding: 4px 10px; border-radius: 6px;">
                                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                                    </span>
                                @else
                                    <span class="badge"
                                          style="background: #FEF3C7; color: #92400E; font-size: 12px; padding: 4px 10px; border-radius: 6px;">
                                        <i class="fas fa-exclamation-circle me-1"></i>Low Stock
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-view" title="View Item"
                                        onclick="window.location.href='{{ route('inventory.show', $item) }}'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-edit" title="Restock">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                    <h6>All Items Are Well Stocked</h6>
                                    <p>No items are currently below their reorder level.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="pagination-info">
                Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }}
                of {{ $items->total() }} items
            </div>
            {{ $items->appends(['search' => $search])->links() }}
        </div>
    </div>

@endsection
