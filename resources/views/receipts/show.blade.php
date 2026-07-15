@extends('layouts.app')

@section('title', 'Receipt Details - Inventory Management System')
@section('page-title', 'Receipt Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-receipt" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $receipt->receipt_number }}</h5>
                    <span class="badge bg-success">Completed</span>
                    <div class="mt-3">
                        <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Receipt Information
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Sales Order</label>
                            <p class="fw-medium">
                                <span class="code-badge">{{ $receipt->salesOrder->sales_number ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Customer</label>
                            <p class="fw-medium">{{ $receipt->salesOrder->customer->customer_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Receipt Date</label>
                            <p class="fw-medium">{{ $receipt->receipt_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Payment Method</label>
                            <p class="fw-medium">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Amount</label>
                            <p class="fw-bold text-primary" style="font-size: 24px;">${{ number_format($receipt->amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Status</label>
                            <p><span class="badge bg-success">Completed</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
