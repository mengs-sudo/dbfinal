@extends('layouts.app')

@section('title', 'Purchase Details - Inventory Management System')
@section('page-title', 'Purchase Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-shopping-cart" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $purchase->purchase_number }}</h5>
                    <span class="badge bg-{{ $purchase->status == 'paid' ? 'success' : ($purchase->status == 'partial' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                    <div class="mt-3">
                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Order Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-secondary d-block">Supplier</small>
                        <span class="fw-medium">{{ $purchase->supplier->supplier_name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Purchase Date</small>
                        <span>{{ $purchase->purchase_date->format('d M Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Total Amount</small>
                        <span class="fw-bold text-primary">${{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Paid Amount</small>
                        <span class="fw-bold text-success">${{ number_format($purchase->paid_amount, 2) }}</span>
                    </div>
                    <div>
                        <small class="text-secondary d-block">Balance</small>
                        <span class="fw-bold {{ $purchase->total_amount - $purchase->paid_amount > 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($purchase->total_amount - $purchase->paid_amount, 2) }}
                        </span>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-secondary d-block">Recorded By</small>
                        <span class="fw-medium">{{ $purchase->createdBy->name ?? 'System' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-boxes me-2 text-primary"></i>Purchase Items</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->purchaseItems as $item)
                                <tr>
                                    <td><span class="code-badge">{{ $item->inventoryItem->item_code ?? 'N/A' }}</span></td>
                                    <td>{{ $item->inventoryItem->item_name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_cost, 2) }}</td>
                                    <td>${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                                <td class="fw-bold text-primary">${{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
