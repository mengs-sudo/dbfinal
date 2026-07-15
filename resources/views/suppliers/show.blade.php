@extends('layouts.app')

@section('title', 'Supplier Details - Inventory Management System')
@section('page-title', 'Supplier Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-truck" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $supplier->supplier_name }}</h5>
                    <span class="code-badge">{{ $supplier->supplier_code }}</span>
                    <div class="mt-3">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('suppliers.edit', $supplier) }}'">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-address-card me-2 text-primary"></i>Contact Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-secondary d-block">Phone</small>
                        <span>{{ $supplier->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Email</small>
                        <span>{{ $supplier->email ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">State</small>
                        <span>{{ $supplier->state ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">City</small>
                        <span>{{ $supplier->city ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <small class="text-secondary d-block">Address</small>
                        <span>{{ $supplier->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>Purchase Orders
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Purchase #</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->purchaseOrders as $purchase)
                                    <tr>
                                        <td><span class="code-badge">{{ $purchase->purchase_number }}</span></td>
                                        <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                                        <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                        <td>${{ number_format($purchase->paid_amount, 2) }}</td>
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
                                            <i class="fas fa-inbox me-2"></i>No purchase orders
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
