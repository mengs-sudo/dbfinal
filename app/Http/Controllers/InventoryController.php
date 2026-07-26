<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $items = InventoryItem::with(['category', 'variants'])
            ->when($search, function ($query, $search) {
                return $query->where('item_code', 'like', '%' . $search . '%')
                    ->orWhere('item_name', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('inventory.index', compact('items', 'search', 'categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['item_code'] = InventoryItem::generateCode();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('inventory-images', 'public');
        }

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load(['category', 'variants']);

        return view('inventory.show', compact('inventory'));
    }

    public function edit(InventoryItem $inventory)
    {
        $data = $inventory->toArray();
        if ($inventory->image) {
            $data['image_url'] = asset('storage/' . $inventory->image);
        }
        return response()->json($data);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }
            $validated['image'] = $request->file('image')->store('inventory-images', 'public');
        }

        $inventory->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        if ($inventory->image) {
            Storage::disk('public')->delete($inventory->image);
        }

        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function getItem($id)
    {
        $item = InventoryItem::findOrFail($id);
        $data = $item->toArray();
        if ($item->image) {
            $data['image_url'] = asset('storage/' . $item->image);
        }
        return response()->json($data);
    }
}