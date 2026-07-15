@extends('layouts.app')

@section('title', 'Item Details - Inventory Management System')
@section('page-title', 'Item Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($inventory->image)
                            <img src="{{ asset('storage/' . $inventory->image) }}" alt="{{ $inventory->item_name }}"
                                 style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border-color);">
                        @else
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-box" style="font-size: 32px; color: var(--primary);"></i>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $inventory->item_name }}</h5>
                    <span class="code-badge">{{ $inventory->item_code }}</span>
                    <div class="mt-3">
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Stock Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-secondary d-block">Quantity in Stock</small>
                        <span class="{{ $inventory->isLowStock() ? 'low-stock' : '' }}" style="font-size: 24px; font-weight: 700;">
                            {{ $inventory->quantity }}
                        </span>
                        @if($inventory->isLowStock())
                            <span class="low-stock-badge ms-2">Low Stock</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Reorder Level</small>
                        <span>{{ $inventory->reorder_level }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tags me-2 text-primary"></i>Item Details
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Category</label>
                            <p class="fw-medium">{{ $inventory->category ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Unit Cost</label>
                            <p class="fw-medium">${{ number_format($inventory->unit_cost, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Selling Price</label>
                            <p class="fw-medium">${{ number_format($inventory->selling_price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Expected Profit</label>
                            <p class="fw-medium text-success">
                                ${{ number_format($inventory->selling_price - $inventory->unit_cost, 2) }}
                                <small class="text-secondary">per unit</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
