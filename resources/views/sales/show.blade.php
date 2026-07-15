@extends('layouts.app')

@section('title', 'Sales Details - Inventory Management System')
@section('page-title', 'Sales Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-cash-register" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $sales->sales_number }}</h5>
                    <span class="badge bg-{{ $sales->status == 'paid' ? 'success' : ($sales->status == 'partial' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($sales->status) }}
                    </span>
                    <div class="mt-3">
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
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
                        <small class="text-secondary d-block">Customer</small>
                        <span class="fw-medium">{{ $sales->customer->customer_name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Sales Date</small>
                        <span>{{ $sales->sales_date->format('d M Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Total Amount</small>
                        <span class="fw-bold text-primary">${{ number_format($sales->total_amount, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Paid Amount</small>
                        <span class="fw-bold text-success">${{ number_format($sales->paid_amount, 2) }}</span>
                    </div>
                    <div>
                        <small class="text-secondary d-block">Balance</small>
                        <span class="fw-bold {{ $sales->total_amount - $sales->paid_amount > 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($sales->total_amount - $sales->paid_amount, 2) }}
                        </span>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-secondary d-block">Recorded By</small>
                        <span class="fw-medium">{{ $sales->createdBy->name ?? 'System' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-boxes me-2 text-primary"></i>Sales Items</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Selling Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales->salesItems as $item)
                                <tr>
                                    <td><span class="code-badge">{{ $item->inventoryItem->item_code ?? 'N/A' }}</span></td>
                                    <td>{{ $item->inventoryItem->item_name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->selling_price, 2) }}</td>
                                    <td>${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                                <td class="fw-bold text-primary">${{ number_format($sales->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
