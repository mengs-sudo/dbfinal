<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventory Management System')</title>

    {{-- 1. Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- 2. FontAwesome Icons CDN (for fa-cubes, fa-th-large, etc.) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- 3. Custom Stylesheet (loads styles from public/css/app.css) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Additional Styles --}}
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        {{-- Sidebar --}}
        <div class="sidebar" id="sidebar">
            {{-- Brand --}}
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="brand-text">
                    Inventory
                    <small>Management System</small>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="sidebar-nav">
                <div class="nav-label">Main Menu</div>

                <a href="{{ route('dashboard') }}" class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    Dashboard
                </a>

                <a href="{{ route('inventory.index') }}" class="nav-item {{ Request::routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    Inventory
                </a>

                <a href="{{ route('categories.index') }}" class="nav-item {{ Request::routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    Categories
                </a>

                <a href="{{ route('stock.low-stock') }}" class="nav-item {{ Request::routeIs('stock.low-stock') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    Low Stock Alerts
                </a>

                <a href="{{ route('suppliers.index') }}" class="nav-item {{ Request::routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    Suppliers
                </a>

                <a href="{{ route('customers.index') }}" class="nav-item {{ Request::routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Customers
                </a>

                <div class="nav-label">Transactions</div>

                <a href="{{ route('purchases.index') }}" class="nav-item {{ Request::routeIs('purchases.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    Purchases
                </a>

                <a href="{{ route('sales.index') }}" class="nav-item {{ Request::routeIs('sales.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    Sales
                </a>

                <a href="{{ route('payments.index') }}" class="nav-item {{ Request::routeIs('payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    Purchase Payments
                </a>

                <a href="{{ route('receipts.index') }}" class="nav-item {{ Request::routeIs('receipts.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i>
                    Sales Receipts
                </a>

                <div class="nav-label">Reports</div>

                <a href="{{ route('reports.valuation') }}" class="nav-item {{ Request::routeIs('reports.valuation') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    Inventory Valuation
                </a>
            </div>

            {{-- Sidebar Footer --}}
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </form>
            </div>
        </div>

        {{-- Sidebar Overlay (Mobile) --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        {{-- Main Content --}}
        <div class="main-content">
            {{-- Top Navbar --}}
            <nav class="top-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5>@yield('page-title', 'Dashboard')</h5>
                </div>
                <div class="navbar-right">
                    {{-- Notifications --}}
                    <a href="#" class="btn-icon" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </a>

                    {{-- User Dropdown --}}
                    <div class="dropdown">
                        <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-sm">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                            <i class="fas fa-chevron-down" style="font-size: 12px; color: var(--text-secondary);"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            {{-- Page Content --}}
            <div class="page-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Toggle Sidebar on Mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>