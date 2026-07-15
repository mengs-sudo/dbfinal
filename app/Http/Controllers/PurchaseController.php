<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseItem;
use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $purchases = PurchaseOrder::with('supplier', 'createdBy')
            ->when($search, function ($query, $search) {
                return $query->where('purchase_number', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('supplier_name', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('purchases.index', compact('purchases', 'search'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $items = InventoryItem::orderBy('item_name')->get();
        $purchaseNumber = PurchaseOrder::generateCode();

        return view('purchases.create', compact('suppliers', 'items', 'purchaseNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $purchaseItems = [];

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_cost'];
                $totalAmount += $lineTotal;

                $purchaseItems[] = [
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $lineTotal,
                ];
            }

            $purchase = PurchaseOrder::create([
                'purchase_number' => PurchaseOrder::generateCode(),
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($purchaseItems as $item) {
                $purchase->purchaseItems()->create($item);

                // Increase inventory quantity
                $inventoryItem = InventoryItem::find($item['inventory_item_id']);
                $inventoryItem->increment('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(PurchaseOrder $purchase)
    {
        $purchase->load('supplier', 'purchaseItems.inventoryItem');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(PurchaseOrder $purchase)
    {
        //
    }

    public function update(Request $request, PurchaseOrder $purchase)
    {
        //
    }

    public function destroy(PurchaseOrder $purchase)
    {
        DB::beginTransaction();

        try {
            foreach ($purchase->purchaseItems as $item) {
                // Decrease inventory quantity
                $inventoryItem = InventoryItem::find($item->inventory_item_id);
                if ($inventoryItem) {
                    $inventoryItem->decrement('quantity', $item->quantity);
                }
            }

            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete purchase order.');
        }
    }
}

