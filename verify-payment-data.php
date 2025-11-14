<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFYING PAYMENT DATA ===\n\n";

// Check latest order
echo "📦 LATEST ORDER:\n";
$order = \Elevate\CommerceCore\Models\Order::latest()->first();
if ($order) {
    echo "  ID: {$order->id}\n";
    echo "  Reference: {$order->reference}\n";
    echo "  Total: £" . number_format($order->total / 100, 2) . "\n";
    echo "  Status: {$order->status}\n";
    echo "  Payment Gateway ID: {$order->payment_gateway_id}\n";
    echo "  Created: {$order->created_at}\n\n";
    
    // Check order lines
    echo "📋 ORDER LINES:\n";
    $lines = $order->lines;
    echo "  Count: {$lines->count()}\n";
    foreach ($lines as $line) {
        echo "  - {$line->description}: £" . number_format($line->total / 100, 2) . " (qty: {$line->quantity})\n";
    }
    echo "\n";
    
    // Check addresses
    echo "📍 ADDRESSES:\n";
    $addresses = $order->addresses;
    echo "  Count: {$addresses->count()}\n";
    foreach ($addresses as $address) {
        echo "  - {$address->type}: {$address->line_one}, {$address->city}\n";
    }
    echo "\n";
    
    // Check transactions
    echo "💳 TRANSACTIONS:\n";
    $transactions = $order->transactions;
    echo "  Count: {$transactions->count()}\n";
    foreach ($transactions as $transaction) {
        echo "  - ID: {$transaction->id}\n";
        echo "    Gateway: {$transaction->gateway}\n";
        echo "    Transaction ID: {$transaction->transaction_id}\n";
        echo "    Amount: £" . number_format($transaction->amount, 2) . "\n";
        echo "    Currency: {$transaction->currency}\n";
        echo "    Status: {$transaction->status}\n";
        echo "    Payment Method: {$transaction->payment_method}\n";
        echo "    Created: {$transaction->created_at}\n";
    }
    echo "\n";
    
    // Check payment gateway
    echo "🔌 PAYMENT GATEWAY:\n";
    $gateway = $order->paymentGateway;
    if ($gateway) {
        echo "  Name: {$gateway->name}\n";
        echo "  Display Name: {$gateway->display_name}\n";
        echo "  Enabled: " . ($gateway->is_enabled ? 'Yes' : 'No') . "\n";
        echo "  Test Mode: " . ($gateway->test_mode ? 'Yes' : 'No') . "\n";
    }
    echo "\n";
    
} else {
    echo "  No orders found!\n\n";
}

// Check all payment gateways
echo "🔌 ALL PAYMENT GATEWAYS:\n";
$gateways = \Elevate\Payments\Models\PaymentGateway::all();
foreach ($gateways as $gateway) {
    echo "  - {$gateway->name} ({$gateway->display_name})\n";
    echo "    Enabled: " . ($gateway->is_enabled ? 'Yes' : 'No') . "\n";
    echo "    Test Mode: " . ($gateway->test_mode ? 'Yes' : 'No') . "\n";
}
echo "\n";

// Check all transactions
echo "💳 ALL TRANSACTIONS:\n";
$allTransactions = \Elevate\Payments\Models\Transaction::with('order')->get();
echo "  Total Count: {$allTransactions->count()}\n";
foreach ($allTransactions as $transaction) {
    echo "  - Transaction #{$transaction->id}\n";
    echo "    Order: #{$transaction->order_id} ({$transaction->order->reference})\n";
    echo "    Gateway: {$transaction->gateway}\n";
    echo "    Amount: £" . number_format($transaction->amount, 2) . " {$transaction->currency}\n";
    echo "    Status: {$transaction->status}\n";
    echo "    Stripe ID: {$transaction->transaction_id}\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
