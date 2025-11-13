<?php

namespace Elevate\CommerceCore\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get customer's order history.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 50);
        
        $orders = $request->user()->orders()
            ->with(['lines.purchasable', 'addresses', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'orders' => $orders->map(function ($order) {
                return $this->formatOrder($order);
            }),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Get a specific order.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->load(['lines.purchasable', 'addresses', 'payments', 'timelines']);

        return response()->json([
            'success' => true,
            'order' => $this->formatOrderDetails($order),
        ]);
    }

    /**
     * Get order timeline for customer.
     */
    public function timeline(Request $request, Order $order): JsonResponse
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        // Only show customer-visible timeline entries
        $timeline = $order->timelines()
            ->where(function ($query) {
                $query->where('is_system_event', true)
                      ->orWhere('is_visible_to_customer', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'timeline' => $timeline->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'type' => $entry->type,
                    'message' => $this->getTimelineMessage($entry),
                    'data' => $entry->data,
                    'created_at' => $entry->created_at,
                    'formatted_date' => $entry->created_at->format('M j, Y g:i A'),
                ];
            }),
        ]);
    }

    /**
     * Reorder items from a previous order.
     */
    public function reorder(Request $request, Order $order): JsonResponse
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        try {
            $addedItems = [];
            $unavailableItems = [];

            foreach ($order->lines as $line) {
                $purchasable = $line->purchasable;
                
                if (!$purchasable) {
                    $unavailableItems[] = $line->name ?? 'Unknown item';
                    continue;
                }

                if (!$purchasable->isAvailableForPurchase()) {
                    $unavailableItems[] = $purchasable->getName();
                    continue;
                }

                if (!$purchasable->hasStock($line->quantity)) {
                    $unavailableItems[] = $purchasable->getName() . ' (insufficient stock)';
                    continue;
                }

                // Add to cart
                $purchasable->addToCart($line->quantity, $request->header('X-Session-ID'), $request->user()->id);
                $addedItems[] = [
                    'name' => $purchasable->getName(),
                    'quantity' => $line->quantity,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Items added to cart',
                'added_items' => $addedItems,
                'unavailable_items' => $unavailableItems,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder items',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format order for list view.
     */
    private function formatOrder(Order $order): array
    {
        $latestPayment = $order->payments()->where('type', 'payment')->latest()->first();

        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'status' => $order->status,
            'total' => $order->total,
            'formatted_total' => '£' . number_format($order->total / 100, 2),
            'item_count' => $order->lines->count(),
            'payment_status' => $latestPayment?->status ?? 'pending',
            'created_at' => $order->created_at,
            'formatted_date' => $order->created_at->format('M j, Y'),
        ];
    }

    /**
     * Format order for detailed view.
     */
    private function formatOrderDetails(Order $order): array
    {
        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'status' => $order->status,
            'sub_total' => $order->sub_total,
            'discount_total' => $order->discount_total,
            'tax_total' => $order->tax_total,
            'total' => $order->total,
            'formatted_sub_total' => '£' . number_format($order->sub_total / 100, 2),
            'formatted_discount_total' => '£' . number_format(($order->discount_total ?? 0) / 100, 2),
            'formatted_tax_total' => '£' . number_format(($order->tax_total ?? 0) / 100, 2),
            'formatted_total' => '£' . number_format($order->total / 100, 2),
            'currency_code' => $order->currency_code,
            'notes' => $order->notes,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'formatted_date' => $order->created_at->format('M j, Y g:i A'),
            
            'lines' => $order->lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'type' => $line->type,
                    'description' => $line->description,
                    'identifier' => $line->identifier,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'sub_total' => $line->sub_total,
                    'total' => $line->total,
                    'formatted_unit_price' => '£' . number_format($line->unit_price / 100, 2),
                    'formatted_sub_total' => '£' . number_format($line->sub_total / 100, 2),
                    'formatted_total' => '£' . number_format($line->total / 100, 2),
                    'meta' => $line->meta,
                    'purchasable' => $line->purchasable ? [
                        'id' => $line->purchasable->id,
                        'name' => $line->purchasable->getName(),
                        'type' => class_basename($line->purchasable),
                    ] : null,
                ];
            }),
            
            'addresses' => $order->addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'type' => $address->type,
                    'first_name' => $address->first_name,
                    'last_name' => $address->last_name,
                    'company' => $address->company,
                    'line_one' => $address->line_one,
                    'line_two' => $address->line_two,
                    'line_three' => $address->line_three,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postcode' => $address->postcode,
                    'country_code' => $address->country_code,
                    'delivery_instructions' => $address->delivery_instructions,
                    'contact_email' => $address->contact_email,
                    'contact_phone' => $address->contact_phone,
                ];
            }),
            
            'payments' => $order->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'type' => $payment->type,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'processed_at' => $payment->processed_at,
                    'created_at' => $payment->created_at,
                ];
            }),
        ];
    }

    /**
     * Get user-friendly timeline message.
     */
    private function getTimelineMessage($entry): string
    {
        return match($entry->type) {
            'order_created' => 'Order placed',
            'payment_received' => 'Payment received',
            'payment_failed' => 'Payment failed',
            'payment_refunded' => 'Payment refunded',
            'payment_partially_refunded' => 'Partial refund processed',
            'status_change' => 'Order status updated',
            default => ucwords(str_replace('_', ' ', $entry->type)),
        };
    }
}