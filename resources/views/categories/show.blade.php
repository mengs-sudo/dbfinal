@extends('layouts.app')

@section('title', 'Category Details - Inventory Management System')
@section('page-title', 'Category Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-tags" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $category->name }}</h5>
                    <p class="text-secondary mb-0">{{ $category->description ?? 'No description' }}</p>
                    <div class="mt-3">
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-boxes me-2 text-primary"></i>Items in this Category</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Selling Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td><span class="code-badge">{{ $item->item_code }}</span></td>
                                    <td>{{ $item->item_name }}</td>
                                    <td class="{{ $item->isLowStock() ? 'low-stock' : '' }}">{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_cost, 2) }}</td>
                                    <td>${{ number_format($item->selling_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No items in this category yet
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