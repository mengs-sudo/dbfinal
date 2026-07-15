@extends('layouts.app')

@section('title', 'Payment Details - Inventory Management System')
@section('page-title', 'Purchase Payment Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(15, 118, 110, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-credit-card" style="font-size: 32px; color: var(--primary);"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $payment->payment_number }}</h5>
                    <span class="badge bg-danger"><i class="fas fa-arrow-down me-1"></i> Purchase Payment</span>
                    <div class="mt-3">
                        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Payment Information
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Purchase Order</label>
                            <p class="fw-medium">
                                <span class="code-badge">{{ $payment->purchaseOrder->purchase_number ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Supplier</label>
                            <p class="fw-medium">{{ $payment->entity_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Payment Date</label>
                            <p class="fw-medium">{{ $payment->payment_date->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Payment Method</label>
                            <p class="fw-medium">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Amount</label>
                            <p class="fw-bold text-primary" style="font-size: 24px;">${{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Status</label>
                            <p><span class="badge bg-success">Completed</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Recorded By</label>
                            <p class="fw-medium">{{ $payment->createdBy->name ?? 'System' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
