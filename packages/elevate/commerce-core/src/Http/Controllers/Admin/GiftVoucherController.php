<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\GiftVoucher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GiftVoucherController extends Controller
{
    /**
     * Display a listing of gift vouchers.
     */
    public function index(Request $request): View
    {
        $query = GiftVoucher::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('expires_at', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<=', now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $giftVouchers = $query->orderBy('created_at', 'desc')
                             ->paginate(15);
        
        // Prepare data array
        $tableData = $giftVouchers->map(function($voucher) {
            return [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'recipient_name' => $voucher->recipient_name,
                'recipient_email' => $voucher->recipient_email,
                'amount' => $voucher->amount,
                'balance' => $voucher->balance,
                'is_active' => $voucher->is_active,
                'expires_at' => $voucher->expires_at,
                'created_at' => $voucher->created_at,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'code' => [
                'label' => 'Code',
                'sortable' => true,
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['code'].'</div>
                            <div class="text-sm text-gray-500">'.($row['recipient_name'] ?: 'No recipient').'</div>
                        </div>
                    ';
                }
            ],
            'recipient_email' => [
                'label' => 'Recipient',
                'sortable' => true,
                'render' => function($row) {
                    return '<span class="text-sm text-gray-600">'.($row['recipient_email'] ?: 'N/A').'</span>';
                }
            ],
            'amount' => [
                'label' => 'Amount',
                'sortable' => true,
                'render' => function($row) {
                    return '<span class="text-sm font-medium text-gray-900">£'.number_format($row['amount'] / 100, 2).'</span>';
                }
            ],
            'balance' => [
                'label' => 'Balance',
                'sortable' => true,
                'render' => function($row) {
                    $balance = $row['balance'] / 100;
                    $color = $balance > 0 ? 'text-green-600' : 'text-gray-400';
                    return '<span class="text-sm font-medium '.$color.'">£'.number_format($balance, 2).'</span>';
                }
            ],
            'expires_at' => [
                'label' => 'Expires',
                'sortable' => true,
                'render' => function($row) {
                    if (!$row['expires_at']) {
                        return '<span class="text-sm text-gray-500">Never</span>';
                    }
                    $expired = $row['expires_at']->isPast();
                    $color = $expired ? 'text-red-600' : 'text-gray-600';
                    return '<span class="text-sm '.$color.'">'.$row['expires_at']->format('M j, Y').'</span>';
                }
            ],
            'is_active' => [
                'label' => 'Status',
                'sortable' => true,
                'render' => function($row) {
                    if (!$row['is_active']) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>';
                    }
                    if ($row['expires_at'] && $row['expires_at']->isPast()) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>';
                    }
                    if ($row['balance'] <= 0) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Used</span>';
                    }
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) {
                    $editUrl = route('admin.settings.gift-vouchers.edit', $row['id']);
                    return '
                        <div class="flex items-center gap-2">
                            <a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">
                                Edit
                            </a>
                            <button onclick="confirmDelete('.$row['id'].')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">
                                Delete
                            </button>
                        </div>
                    ';
                }
            ],
        ];
        
        return view('commerce::admin.settings.gift-vouchers.index', [
            'giftVouchers' => [
                'data' => $tableData,
                'columns' => $columns,
                'paginator' => $giftVouchers,
            ],
        ]);
    }

    /**
     * Show the form for creating a new gift voucher.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.gift-vouchers.form');
    }

    /**
     * Store a newly created gift voucher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:gift_vouchers,code',
            'amount' => 'required|numeric|min:0',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        
        // Convert amount to cents
        $validated['amount'] = (int) round($validated['amount'] * 100);
        $validated['balance'] = $validated['amount'];

        GiftVoucher::create($validated);

        return redirect()->route('admin.settings.gift-vouchers.index')
            ->with('success', 'Gift voucher created successfully');
    }

    /**
     * Show the form for editing the specified gift voucher.
     */
    public function edit(int $id): View
    {
        $giftVoucher = GiftVoucher::findOrFail($id);
        
        // Convert cents to dollars for display
        $giftVoucher->amount = $giftVoucher->amount / 100;
        $giftVoucher->balance = $giftVoucher->balance / 100;
        
        return view('commerce::admin.settings.gift-vouchers.form', compact('giftVoucher'));
    }

    /**
     * Update the specified gift voucher.
     */
    public function update(Request $request, int $id)
    {
        $giftVoucher = GiftVoucher::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:gift_vouchers,code,' . $id,
            'amount' => 'required|numeric|min:0',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? false;
        
        // Convert amount to cents
        $validated['amount'] = (int) round($validated['amount'] * 100);
        
        // Don't update balance if amount hasn't changed
        if ($giftVoucher->amount !== $validated['amount']) {
            $validated['balance'] = $validated['amount'];
        }

        $giftVoucher->update($validated);

        return redirect()->route('admin.settings.gift-vouchers.index')
            ->with('success', 'Gift voucher updated successfully');
    }

    /**
     * Remove the specified gift voucher.
     */
    public function destroy(int $id)
    {
        $giftVoucher = GiftVoucher::findOrFail($id);
        $giftVoucher->delete();

        return redirect()->route('admin.settings.gift-vouchers.index')
            ->with('success', 'Gift voucher deleted successfully');
    }
}
