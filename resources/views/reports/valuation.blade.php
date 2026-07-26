@extends('layouts.app')

@section('title', 'Inventory Valuation - Inventory Management System')
@section('page-title', 'Inventory Valuation')

@section('content')
    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-sack-dollar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Inventory Value</div>
                    <div class="stat-number">${{ number_format($totals->total_valuation, 2) }}</div>
                    <div class="stat-desc">Quantity &times; unit cost, all items</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Items Counted</div>
                    <div class="stat-number">{{ $totals->total_items }}</div>
                    <div class="stat-desc">Distinct products in stock</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Units on Hand</div>
                    <div class="stat-number">{{ number_format($totals->total_quantity) }}</div>
                    <div class="stat-desc">Sum of all quantities</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Valuation by category --}}
        <div class="col-lg-5">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-tags me-2 text-primary"></i>Value by Category</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($valuationByCategory as $row)
                                <tr>
                                    <td>{{ $row->category_name }}</td>
                                    <td>{{ number_format($row->total_quantity) }}</td>
                                    <td>${{ number_format($row->total_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No categorized items yet
                                    </td>
                                </tr>
                            @endforelse
                            @if($uncategorizedValue->total_quantity > 0)
                                <tr>
                                    <td class="text-secondary">Uncategorized</td>
                                    <td>{{ number_format($uncategorizedValue->total_quantity) }}</td>
                                    <td>${{ number_format($uncategorizedValue->total_value, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Per-item valuation table --}}
        <div class="col-lg-7">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-list me-2 text-primary"></i>Item Valuation</h6>
                    <div class="table-toolbar">
                        <form action="{{ route('reports.valuation') }}" method="GET" class="d-flex gap-2">
                            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Unit Cost</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td><span class="code-badge">{{ $item->item_code }}</span></td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="fw-medium">${{ number_format($item->inventory_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No inventory items found
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
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection