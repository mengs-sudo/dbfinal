<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $payments = Payment::with('purchaseOrder.supplier', 'salesOrder.customer', 'createdBy')
            ->when($search, function ($query, $search) {
                return $query->where('payment_number', 'like', '%' . $search . '%')
                    ->orWhere('entity_name', 'like', '%' . $search . '%')
                    ->orWhereHas('purchaseOrder', function ($q) use ($search) {
                        $q->where('purchase_number', 'like', '%' . $search . '%')
                            ->orWhereHas('supplier', function ($sq) use ($search) {
                                $sq->where('supplier_name', 'like', '%' . $search . '%');
                            });
                    })
                    ->orWhereHas('salesOrder', function ($q) use ($search) {
                        $q->where('sales_number', 'like', '%' . $search . '%')
                            ->orWhereHas('customer', function ($sq) use ($search) {
                                $sq->where('customer_name', 'like', '%' . $search . '%');
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.index', compact('payments', 'search'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->where('status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $salesOrders = SalesOrder::with('customer')
            ->where('status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentNumber = Payment::generateCode();

        return view('payments.create', compact('purchaseOrders', 'salesOrders', 'paymentNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:purchase,sales',
            'purchase_order_id' => 'required_if:type,purchase|exists:purchase_orders,id',
            'sales_order_id' => 'required_if:type,sales|exists:sales_orders,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,check,bank_transfer,credit_card',
        ]);

        DB::beginTransaction();

        try {
            $entityName = '';

            if ($validated['type'] === 'purchase') {
                $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);

                $paidSoFar = $purchaseOrder->payments()->sum('amount');
                $remaining = $purchaseOrder->total_amount - $paidSoFar;

                if ($validated['amount'] > $remaining) {
                    throw new \Exception("Payment amount exceeds remaining balance of " . number_format($remaining, 2));
                }

                $entityName = $purchaseOrder->supplier->supplier_name ?? 'Supplier';

                $payment = Payment::create([
                    'payment_number' => Payment::generateCode(),
                    'type' => 'purchase',
                    'entity_name' => $entityName,
                    'purchase_order_id' => $validated['purchase_order_id'],
                    'payment_date' => $validated['payment_date'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);

                // Update purchase order paid amount
                $newPaidAmount = $paidSoFar + $validated['amount'];
                $purchaseOrder->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $newPaidAmount >= $purchaseOrder->total_amount ? 'paid' : 'partial',
                ]);

                // Update supplier balance
                $supplier = Supplier::find($purchaseOrder->supplier_id);
                $supplier->decrement('balance', $validated['amount']);

            } else { // type === 'sales'
                $salesOrder = SalesOrder::findOrFail($validated['sales_order_id']);

                $receivedSoFar = $salesOrder->receipts()->sum('amount');
                $remaining = $salesOrder->total_amount - $receivedSoFar;

                if ($validated['amount'] > $remaining) {
                    throw new \Exception("Payment amount exceeds remaining balance of " . number_format($remaining, 2));
                }

                $entityName = $salesOrder->customer->customer_name ?? 'Customer';

                $payment = Payment::create([
                    'payment_number' => Payment::generateCode(),
                    'type' => 'sales',
                    'entity_name' => $entityName,
                    'sales_order_id' => $validated['sales_order_id'],
                    'payment_date' => $validated['payment_date'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);

                // Update sales order paid amount (via receipts table)
                $salesOrder->receipts()->create([
                    'receipt_number' => 'REC' . str_pad(($salesOrder->receipts()->count() + 1), 3, '0', STR_PAD_LEFT),
                    'payment_id' => $payment->id,
                    'receipt_date' => $validated['payment_date'],
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
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Payment $payment)
    {
        $payment->load('purchaseOrder.supplier', 'salesOrder.customer', 'createdBy');
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        //
    }

    public function update(Request $request, Payment $payment)
    {
        //
    }

    public function destroy(Payment $payment)
    {
        DB::beginTransaction();

        try {
            if ($payment->type === 'purchase') {
                $purchaseOrder = $payment->purchaseOrder;
                $supplier = $purchaseOrder->supplier;

                if ($supplier) {
                    $supplier->increment('balance', $payment->amount);
                }

                $newPaidAmount = $purchaseOrder->paid_amount - $payment->amount;
                $purchaseOrder->update([
                    'paid_amount' => max(0, $newPaidAmount),
                    'status' => $newPaidAmount <= 0 ? 'pending' : 'partial',
                ]);
            } else {
                $salesOrder = $payment->salesOrder;
                $customer = $salesOrder?->customer;

                // Also delete the related receipt
                $payment->receipt()?->delete();

                if ($customer) {
                    $customer->increment('balance', $payment->amount);
                }

                if ($salesOrder) {
                    $newPaidAmount = $salesOrder->paid_amount - $payment->amount;
                    $salesOrder->update([
                        'paid_amount' => max(0, $newPaidAmount),
                        'status' => $newPaidAmount <= 0 ? 'pending' : 'partial',
                    ]);
                }
            }

            $payment->delete();

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete payment.');
        }
    }

    public function getPurchaseOrder($id)
    {
        $purchaseOrder = PurchaseOrder::with('supplier')->findOrFail($id);
        $paidAmount = $purchaseOrder->payments()->sum('amount');
        $remaining = $purchaseOrder->total_amount - $paidAmount;

        return response()->json([
            'purchase_order' => $purchaseOrder,
            'paid_amount' => $paidAmount,
            'remaining' => $remaining,
        ]);
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
