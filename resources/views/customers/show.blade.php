@extends('layouts.app')

@section('title', 'Customer Details - Inventory Management System')
@section('page-title', 'Customer Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-user" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $customer->customer_name }}</h5>
                    <span class="code-badge">{{ $customer->customer_code }}</span>
                    <div class="mt-3">
                        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" onclick="window.location.href='{{ route('customers.edit', $customer) }}'">
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
                        <span>{{ $customer->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">Email</small>
                        <span>{{ $customer->email ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">State</small>
                        <span>{{ $customer->state ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-secondary d-block">City</small>
                        <span>{{ $customer->city ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <small class="text-secondary d-block">Address</small>
                        <span>{{ $customer->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-cash-register me-2 text-primary"></i>Sales Orders
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sales #</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->salesOrders as $sale)
                                    <tr>
                                        <td><span class="code-badge">{{ $sale->sales_number }}</span></td>
                                        <td>{{ $sale->sales_date->format('d M Y') }}</td>
                                        <td>${{ number_format($sale->total_amount, 2) }}</td>
                                        <td>${{ number_format($sale->paid_amount, 2) }}</td>
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
                                            <i class="fas fa-inbox me-2"></i>No sales orders
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
