<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\PurchaseItem;
use App\Models\SalesItem;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // Stock In — shows all items received from purchase orders
    public function stockIn(Request $request)
    {
        $search = $request->get('search');

        $stockIns = PurchaseItem::with(['purchaseOrder.supplier', 'purchaseOrder.createdBy', 'inventoryItem.category'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('inventoryItem', function ($q) use ($search) {
                    $q->where('item_name', 'like', '%' . $search . '%')
                      ->orWhere('item_code', 'like', '%' . $search . '%');
                })->orWhereHas('purchaseOrder', function ($q) use ($search) {
                    $q->where('purchase_number', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalReceived    = PurchaseItem::sum('quantity');
        $totalValue       = PurchaseItem::sum('total');
        $todayReceived    = PurchaseItem::whereDate('created_at', today())->sum('quantity');
        $totalTransactions = PurchaseItem::count();

        return view('stock.in', compact(
            'stockIns', 'search',
            'totalReceived', 'totalValue',
            'todayReceived', 'totalTransactions'
        ));
    }

    // Stock Out — shows all items sold through sales orders
    public function stockOut(Request $request)
    {
        $search = $request->get('search');

        $stockOuts = SalesItem::with(['salesOrder.customer', 'salesOrder.createdBy', 'inventoryItem.category'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('inventoryItem', function ($q) use ($search) {
                    $q->where('item_name', 'like', '%' . $search . '%')
                      ->orWhere('item_code', 'like', '%' . $search . '%');
                })->orWhereHas('salesOrder', function ($q) use ($search) {
                    $q->where('sales_number', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalIssued       = SalesItem::sum('quantity');
        $totalRevenue      = SalesItem::sum('total');
        $todayIssued       = SalesItem::whereDate('created_at', today())->sum('quantity');
        $totalTransactions = SalesItem::count();

        return view('stock.out', compact(
            'stockOuts', 'search',
            'totalIssued', 'totalRevenue',
            'todayIssued', 'totalTransactions'
        ));
    }

    // Low Stock Alerts — items at or below reorder level
    public function lowStock(Request $request)
    {
        $search = $request->get('search');

        $items = InventoryItem::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('item_name', 'like', '%' . $search . '%')
                            ->orWhere('item_code', 'like', '%' . $search . '%')
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%');
                            });
            })
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity', 'asc')
            ->paginate(10);

        $lowStockCount  = InventoryItem::whereColumn('quantity', '<=', 'reorder_level')
                                       ->where('quantity', '>', 0)->count();
        $outOfStock     = InventoryItem::where('quantity', 0)->count();
        $totalLow       = InventoryItem::whereColumn('quantity', '<=', 'reorder_level')->count();

        return view('stock.low_stock', compact(
            'items', 'search',
            'lowStockCount', 'outOfStock', 'totalLow'
        ));
    }
}
