<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesItem;
use App\Models\InventoryItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $sales = SalesOrder::with('customer', 'createdBy')
            ->when($search, function ($query, $search) {
                return $query->where('sales_number', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('customer_name', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sales.index', compact('sales', 'search'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $items = InventoryItem::orderBy('item_name')->get();
        $salesNumber = SalesOrder::generateCode();

        return view('sales.create', compact('customers', 'items', 'salesNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $salesItems = [];

            foreach ($request->items as $item) {
                $inventoryItem = InventoryItem::findOrFail($item['inventory_item_id']);

                // Check if enough stock is available
                if ($inventoryItem->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for '{$inventoryItem->item_name}'. Available: {$inventoryItem->quantity}, Requested: {$item['quantity']}");
                }

                $lineTotal = $item['quantity'] * $item['selling_price'];
                $totalAmount += $lineTotal;

                $salesItems[] = [
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'selling_price' => $item['selling_price'],
                    'total' => $lineTotal,
                ];
            }

            $sales = SalesOrder::create([
                'sales_number' => SalesOrder::generateCode(),
                'customer_id' => $validated['customer_id'],
                'sales_date' => $validated['sales_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($salesItems as $item) {
                $sales->salesItems()->create($item);

                // Decrease inventory quantity
                $inventoryItem = InventoryItem::find($item['inventory_item_id']);
                $inventoryItem->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Sales order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create sales order: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SalesOrder $sales)
    {
        $sales->load('customer', 'salesItems.inventoryItem');
        return view('sales.show', compact('sales'));
    }

    public function edit(SalesOrder $sales)
    {
        //
    }

    public function update(Request $request, SalesOrder $sales)
    {
        //
    }

    public function destroy(SalesOrder $sales)
    {
        DB::beginTransaction();

        try {
            foreach ($sales->salesItems as $item) {
                // Increase inventory quantity back
                $inventoryItem = InventoryItem::find($item->inventory_item_id);
                if ($inventoryItem) {
                    $inventoryItem->increment('quantity', $item->quantity);
                }
            }

            $sales->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Sales order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete sales order.');
        }
    }
}

