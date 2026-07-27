<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Inventory valuation: total dollar worth of current stock (quantity * unit_cost).
    public function valuation(Request $request)
    {
        $categoryId = $request->get('category_id');

        $baseQuery = InventoryItem::query()
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            });

        // Paginated rows for the table on screen.
        $items = (clone $baseQuery)
            ->with('category')
            ->orderBy('item_name')
            ->paginate(15)
            ->appends(['category_id' => $categoryId]);

        // Totals computed over ALL matching rows (not just the current page),
        // so the summary cards stay correct regardless of pagination.
        $totals = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_items, COALESCE(SUM(quantity), 0) as total_quantity, COALESCE(SUM(quantity * unit_cost), 0) as total_valuation')
            ->first();

        // Breakdown of valuation by category, for the summary table.
        $valuationByCategory = InventoryItem::query()
            ->join('categories', 'categories.id', '=', 'inventory_items.category_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc(DB::raw('SUM(inventory_items.quantity * inventory_items.unit_cost)'))
            ->select('categories.name as category_name')
            ->selectRaw('SUM(inventory_items.quantity) as total_quantity')
            ->selectRaw('SUM(inventory_items.quantity * inventory_items.unit_cost) as total_value')
            ->get();

        $uncategorizedValue = InventoryItem::query()
            ->whereNull('category_id')
            ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity, COALESCE(SUM(quantity * unit_cost), 0) as total_value')
            ->first();

        $categories = Category::orderBy('name')->get();

        // Dataset for the donut chart + its legend: every category's share
        // of the grand total valuation, expressed as both a dollar amount
        // and a percentage (e.g. "Electronics $8,750.00 (47.4%)").
        // Uncategorized items are folded in as their own slice so the chart
        // always accounts for 100% of on-hand value.
        $chartRows = $valuationByCategory->map(function ($row) {
            return [
                'label' => $row->category_name,
                'value' => (float) $row->total_value,
            ];
        });

        if ($uncategorizedValue->total_value > 0) {
            $chartRows->push([
                'label' => 'Uncategorized',
                'value' => (float) $uncategorizedValue->total_value,
            ]);
        }

        $grandTotal = (float) $totals->total_valuation;

        $categoryChart = $chartRows->map(function ($row) use ($grandTotal) {
            $row['percentage'] = $grandTotal > 0
                ? round(($row['value'] / $grandTotal) * 100, 1)
                : 0;

            return $row;
        })->values();

        // Number of distinct valuation "slices" (categories + the
        // uncategorized bucket, if any) shown in the summary card.
        $totalCategories = $categoryChart->count();

        return view('reports.valuation', compact(
            'items',
            'totals',
            'valuationByCategory',
            'uncategorizedValue',
            'categories',
            'categoryId',
            'categoryChart',
            'totalCategories'
        ));
    }
}