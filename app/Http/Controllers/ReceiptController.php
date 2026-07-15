<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\SalesOrder;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $receipts = Receipt::with('salesOrder.customer')
            ->when($search, function ($query, $search) {
                return $query->where('receipt_number', 'like', '%' . $search . '%')
                    ->orWhereHas('salesOrder', function ($q) use ($search) {
                        $q->where('sales_number', 'like', '%' . $search . '%')
                            ->orWhereHas('customer', function ($sq) use ($search) {
                                $sq->where('customer_name', 'like', '%' . $search . '%');
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('receipts.index', compact('receipts', 'search'));
    }

    public function create()
    {
        $salesOrders = SalesOrder::with('customer')
            ->where('status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();
        $receiptNumber = Receipt::generateCode();

        return view('receipts.create', compact('salesOrders', 'receiptNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,check,bank_transfer,credit_card',
        ]);

        DB::beginTransaction();

        try {
            $salesOrder = SalesOrder::findOrFail($validated['sales_order_id']);

            // Check if receipt amount exceeds remaining balance
            $receivedSoFar = $salesOrder->receipts()->sum('amount');
            $remaining = $salesOrder->total_amount - $receivedSoFar;

            if ($validated['amount'] > $remaining) {
                throw new \Exception("Receipt amount exceeds remaining balance of {$remaining}");
            }

            $receipt = Receipt::create([
                'receipt_number' => Receipt::generateCode(),
                'sales_order_id' => $validated['sales_order_id'],
                'receipt_date' => $validated['receipt_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Update sales order paid amount
            $newPaidAmount = $receivedSoFar + $validated['amount'];
            $salesOrder->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newPaidAmount >= $salesOrder->total_amount ? 'paid' : 'partial',
            ]);

            // Update customer balance
            $customer = Customer::find($salesOrder->customer_id);
            $customer->decrement('balance', $validated['amount']);

            DB::commit();

            return redirect()->route('receipts.index')
                ->with('success', 'Receipt recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record receipt: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('salesOrder.customer');
        return view('receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        //
    }

    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    public function destroy(Receipt $receipt)
    {
        DB::beginTransaction();

        try {
            $salesOrder = $receipt->salesOrder;
            $customer = $salesOrder->customer;

            // Reverse customer balance
            $customer->increment('balance', $receipt->amount);

            // Update sales order paid amount
            $newPaidAmount = $salesOrder->paid_amount - $receipt->amount;
            $salesOrder->update([
                'paid_amount' => max(0, $newPaidAmount),
                'status' => $newPaidAmount <= 0 ? 'pending' : 'partial',
            ]);

            $receipt->delete();

            DB::commit();

            return redirect()->route('receipts.index')
                ->with('success', 'Receipt deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete receipt.');
        }
    }

    public function getSalesOrder($id)
    {
        $salesOrder = SalesOrder::with('customer')->findOrFail($id);
        $receivedAmount = $salesOrder->receipts()->sum('amount');
        $remaining = $salesOrder->total_amount - $receivedAmount;

        return response()->json([
            'sales_order' => $salesOrder,
            'received_amount' => $receivedAmount,
            'remaining' => $remaining,
        ]);
    }
}
