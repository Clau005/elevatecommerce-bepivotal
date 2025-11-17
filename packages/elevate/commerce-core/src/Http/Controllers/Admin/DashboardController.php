<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Dashboard\DashboardRegistry;
use Elevate\CommerceCore\Dashboard\Lenses\StatsCardLens;
use Elevate\CommerceCore\Dashboard\Lenses\RecentOrdersLens;
use Elevate\CommerceCore\Dashboard\Lenses\QuickActionsLens;
use Elevate\CommerceCore\Models\Order;
use Elevate\CommerceCore\Models\User as CommerceUser;
use ElevateCommerce\Core\Support\Helpers\CurrencyHelper;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(DashboardRegistry $dashboard): View
    {
        // Register default lenses
        $this->registerDefaultLenses($dashboard);

        // Get all lenses
        $lenses = $dashboard->all();

        return view('commerce::admin.dashboard.index', [
            'lenses' => $lenses,
        ]);
    }

    /**
     * Register the default dashboard lenses
     */
    protected function registerDefaultLenses(DashboardRegistry $dashboard): void
    {
        // Calculate stats
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $lastMonth = now()->subMonth();

        // Total Revenue
        $totalRevenue = Order::whereIn('status', ['payment-received', 'processing', 'shipped', 'delivered'])
            ->sum('total') / 100;
        $lastMonthRevenue = Order::whereIn('status', ['payment-received', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', $lastMonth)
            ->sum('total') / 100;
        $revenueChange = $lastMonthRevenue > 0 
            ? '+' . number_format((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) . '%'
            : null;

        // Total Orders
        $totalOrders = Order::count();
        $todayOrders = Order::where('created_at', '>=', $today)->count();
        $yesterdayOrders = Order::where('created_at', '>=', $yesterday)
            ->where('created_at', '<', $today)
            ->count();
        $ordersChange = $yesterdayOrders > 0
            ? ($todayOrders > $yesterdayOrders ? '+' : '') . number_format((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1) . '%'
            : null;

        // Total Customers
        $totalCustomers = CommerceUser::count();
        $newCustomersToday = CommerceUser::where('created_at', '>=', $today)->count();

        // Pending Orders
        $pendingOrders = Order::whereIn('status', ['awaiting-payment', 'payment-received'])->count();

        // Register stat cards
        $dashboard->register(
            new StatsCardLens(
                identifier: 'total-revenue',
                title: 'Total Revenue',
                value: '£' . CurrencyHelper::format($totalRevenue),
                change: $revenueChange,
                changeType: 'increase',
                gridWidth: 3,
                priority: 10
            )
        );

        $dashboard->register(
            new StatsCardLens(
                identifier: 'total-orders',
                title: 'Total Orders',
                value: number_format($totalOrders),
                change: $ordersChange,
                changeType: $todayOrders > $yesterdayOrders ? 'increase' : 'decrease',
                gridWidth: 3,
                priority: 20
            )
        );

        $dashboard->register(
            new StatsCardLens(
                identifier: 'total-customers',
                title: 'Total Customers',
                value: number_format($totalCustomers),
                change: $newCustomersToday > 0 ? "+{$newCustomersToday} today" : null,
                changeType: 'increase',
                gridWidth: 3,
                priority: 30
            )
        );

        $dashboard->register(
            new StatsCardLens(
                identifier: 'pending-orders',
                title: 'Pending Orders',
                value: number_format($pendingOrders),
                change: null,
                changeType: 'neutral',
                gridWidth: 3,
                priority: 40
            )
        );

        // Register recent orders lens
        $dashboard->register(new RecentOrdersLens());

        // Register quick actions lens
        $dashboard->register(new QuickActionsLens());
    }
}
