<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'variant_name' => 'required|string|max:50|unique:product_variants,variant_name,NULL,id,inventory_item_id,' . $inventory->id,
            'sku' => 'nullable|string|max:40|unique:product_variants,sku',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $inventory->variants()->create($validated);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Variant added successfully.');
    }

    public function edit(InventoryItem $inventory, ProductVariant $variant)
    {
        return response()->json($variant);
    }

    public function update(Request $request, InventoryItem $inventory, ProductVariant $variant)
    {
        $validated = $request->validate([
            'variant_name' => 'required|string|max:50|unique:product_variants,variant_name,' . $variant->id . ',id,inventory_item_id,' . $inventory->id,
            'sku' => 'nullable|string|max:40|unique:product_variants,sku,' . $variant->id,
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $variant->update($validated);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Variant updated successfully.');
    }

    public function destroy(InventoryItem $inventory, ProductVariant $variant)
    {
        $variant->delete();

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Variant deleted successfully.');
    }
}