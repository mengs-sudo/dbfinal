<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSuppliers = Supplier::count();
        $totalCustomers = Customer::count();
        $totalInventoryItems = InventoryItem::count();
        $totalPurchases = PurchaseOrder::count();
        $totalSales = SalesOrder::count();

        $lowStockItems = InventoryItem::with('category')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity', 'asc')
            ->get();

        $recentPurchases = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentSales = SalesOrder::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalSuppliers',
            'totalCustomers',
            'totalInventoryItems',
            'totalPurchases',
            'totalSales',
            'lowStockItems',
            'recentPurchases',
            'recentSales'
        ));
    }
}
