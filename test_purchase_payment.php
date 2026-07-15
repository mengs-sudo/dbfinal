<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\PurchaseOrder;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

echo "=== EXACT PaymentController@store SIMULATION ===\n";
echo "Submitting: type=purchase, purchase_order_id=2, amount=4.00\n\n";

$validated = [
    'type' => 'purchase',
    'purchase_order_id' => 2,
    'payment_date' => '2026-07-15',
    'amount' => 4.00,
    'payment_method' => 'cash',
];

echo "Validation PASSED\n";

DB::beginTransaction();

try {
    $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);
    echo "Found PO: " . $purchaseOrder->purchase_number . " (ID: " . $purchaseOrder->id . ")\n";

    $paidSoFar = $purchaseOrder->payments()->sum('amount');
    $remaining = $purchaseOrder->total_amount - $paidSoFar;

    echo "Total: " . $purchaseOrder->total_amount . "\n";
    echo "Paid so far: " . $paidSoFar . "\n";
    echo "Remaining: " . $remaining . "\n";

    if ($validated['amount'] > $remaining) {
        throw new Exception("Payment amount exceeds remaining balance of " . number_format($remaining, 2));
    }
    echo "Amount check: PASSED\n";

    $entityName = $purchaseOrder->supplier->supplier_name ?? 'Supplier';
    echo "Entity name: " . $entityName . "\n";

    // Generate payment number
    $lastPayment = Payment::orderBy('id', 'desc')->first();
    $number = $lastPayment ? intval(substr($lastPayment->payment_number, 3)) + 1 : 1;
    $paymentNumber = 'PAY' . str_pad($number, 3, '0', STR_PAD_LEFT);
    echo "Payment number: " . $paymentNumber . "\n";

    $payment = Payment::create([
        'payment_number' => $paymentNumber,
        'type' => 'purchase',
        'entity_name' => $entityName,
        'purchase_order_id' => $validated['purchase_order_id'],
        'payment_date' => $validated['payment_date'],
        'amount' => $validated['amount'],
        'payment_method' => $validated['payment_method'],
        'status' => 'completed',
        'created_by' => 1,
    ]);
    echo "Payment created: ID=" . $payment->id . "\n";

    // Update purchase order paid amount
    $newPaidAmount = $paidSoFar + $validated['amount'];
    $purchaseOrder->update([
        'paid_amount' => $newPaidAmount,
        'status' => $newPaidAmount >= $purchaseOrder->total_amount ? 'paid' : 'partial',
    ]);
    echo "PO updated - paid_amount=" . $newPaidAmount . ", status=" . $purchaseOrder->fresh()->status . "\n";

    // Update supplier balance
    $supplier = Supplier::find($purchaseOrder->supplier_id);
    if ($supplier) {
        $supplier->decrement('balance', $validated['amount']);
        echo "Supplier balance decremented to: " . $supplier->fresh()->balance . "\n";
    } else {
        echo "WARNING: Supplier not found!\n";
    }

    DB::commit();
    echo "\n*** PURCHASE PAYMENT: SUCCESS ***\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "\n*** FAILED: " . $e->getMessage() . " ***\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== DONE ===\n";
