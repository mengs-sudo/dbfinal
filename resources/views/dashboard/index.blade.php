@extends('layouts.app')

@section('title', 'Dashboard - Inventory Management System')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Suppliers</div>
                    <div class="stat-number">{{ $totalSuppliers }}</div>
                    <div class="stat-desc">Registered suppliers</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Customers</div>
                    <div class="stat-number">{{ $totalCustomers }}</div>
                    <div class="stat-desc">Registered customers</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Inventory Items</div>
                    <div class="stat-number">{{ $totalInventoryItems }}</div>
                    <div class="stat-desc">Items in stock</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Purchases</div>
                    <div class="stat-number">{{ $totalPurchases }}</div>
                    <div class="stat-desc">Purchase orders</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Sales</div>
                    <div class="stat-number">{{ $totalSales }}</div>
                    <div class="stat-desc">Sales orders</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Low Stock Items</div>
                    <div class="stat-number">{{ $lowStockItems->count() }}</div>
                    <div class="stat-desc">Items below reorder level</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Purchases --}}
        <div class="col-lg-6">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-shopping-cart me-2 text-primary"></i>Recent Purchases</h6>
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Purchase #</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPurchases as $purchase)
                                <tr>
                                    <td><span class="code-badge">{{ $purchase->purchase_number }}</span></td>
                                    <td>{{ $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                                    <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                                    <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                    <td>
                                        @if($purchase->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($purchase->status == 'partial')
                                            <span class="badge bg-warning">Partial</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No purchases yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Sales --}}
        <div class="col-lg-6">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-cash-register me-2 text-success"></i>Recent Sales</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sales #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td><span class="code-badge">{{ $sale->sales_number }}</span></td>
                                    <td>{{ $sale->customer->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $sale->sales_date->format('d M Y') }}</td>
                                    <td>${{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        @if($sale->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($sale->status == 'partial')
                                            <span class="badge bg-warning">Partial</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No sales yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Low Stock Items --}}
        <div class="col-12">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Low Stock Items</h6>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td><span class="code-badge">{{ $item->item_code }}</span></td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->category ?? 'N/A' }}</td>
                                    <td class="low-stock">{{ $item->quantity }}</td>
                                    <td>{{ $item->reorder_level }}</td>
                                    <td>
                                        @if($item->quantity == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @else
                                            <span class="badge bg-warning">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">
                                        <i class="fas fa-check-circle me-2 text-success"></i>All items are well-stocked
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
