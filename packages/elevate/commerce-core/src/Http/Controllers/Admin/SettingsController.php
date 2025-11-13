<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Staff;
use Elevate\CommerceCore\Models\CustomerGroup;
use Elevate\CommerceCore\Models\Country;
use Elevate\CommerceCore\Models\Currency;
use Elevate\CommerceCore\Models\Discount;
use Elevate\CommerceCore\Models\CheckoutRule;
use Elevate\CommerceCore\Models\Language;
use App\Models\ProductOption;
use App\Models\Tag;
use Elevate\CommerceCore\Models\GiftVoucher;
use Elevate\CommerceCore\Models\GiftVoucherUsage;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Elevate\CommerceCore\Settings\SettingsRegistry;
use Elevate\CommerceCore\Settings\Sections\GeneralSettingsSection;
use Elevate\CommerceCore\Settings\Sections\CurrenciesSettingsSection;
use Elevate\CommerceCore\Settings\Sections\LanguagesSettingsSection;
use Elevate\CommerceCore\Settings\Sections\StatesSettingsSection;
use Elevate\CommerceCore\Settings\Sections\StaffSettingsSection;
use Elevate\CommerceCore\Settings\Sections\CustomerGroupsSettingsSection;
use Elevate\CommerceCore\Settings\Sections\RolesSettingsSection;
use Elevate\CommerceCore\Settings\Sections\DiscountsSettingsSection;
use Elevate\CommerceCore\Settings\Sections\PaymentsSettingsSection;
use Elevate\CommerceCore\Settings\Sections\ShippingSettingsSection;
use Elevate\CommerceCore\Settings\Sections\CheckoutSettingsSection;
use Elevate\CommerceCore\Settings\Sections\GiftVouchersSettingsSection;

class SettingsController extends Controller
{
    /**
     * Display the settings overview page.
     */
    public function index(SettingsRegistry $settings)
    {
        // Register default sections
        $this->registerDefaultSections($settings);

        // Get all sections grouped by category
        $sections = $settings->grouped();

        return view('commerce::admin.settings.index', [
            'sections' => $sections,
        ]);
    }

    /**
     * Register default settings sections
     */
    protected function registerDefaultSections(SettingsRegistry $settings): void
    {
        $settings->registerMany([
            new GeneralSettingsSection(),
            new CurrenciesSettingsSection(),
            new LanguagesSettingsSection(),
            new StatesSettingsSection(),
            new StaffSettingsSection(),
            new CustomerGroupsSettingsSection(),
            new RolesSettingsSection(),
            new DiscountsSettingsSection(),
            new GiftVouchersSettingsSection(),
            new PaymentsSettingsSection(),
            new ShippingSettingsSection(),
            new CheckoutSettingsSection(),
        ]);
    }

    /**
     * Display a specific settings page.
     */
    public function show(string $section)
    {
        // Map section names to views and redirects
        $pageMap = [
            'general' => [
                'view' => 'commerce::admin.settings.general',
                'data' => fn() => []
            ],
            'staff' => [
                'redirect' => 'admin.settings.staff.index'
            ],
            'currencies' => [
                'redirect' => 'admin.settings.currencies.index'
            ],
            'languages' => [
                'redirect' => 'admin.settings.languages.index'
            ],
            'states' => [
                'redirect' => 'admin.settings.states.index'
            ],
            'customer-groups' => [
                'redirect' => 'admin.settings.customer-groups.index'
            ],
            'roles' => [
                'redirect' => 'admin.settings.roles.index'
            ],
            'discounts' => [
                'redirect' => 'admin.settings.discounts.index'
            ],
            'payments' => [
                'redirect' => 'admin.settings.payments.index'
            ],
            'checkout' => [
                'redirect' => 'admin.settings.checkout.index'
            ],
            'gift-vouchers' => [
                'redirect' => 'admin.settings.gift-vouchers.index'
            ],
        ];

        if (!isset($pageMap[$section])) {
            abort(404);
        }

        $config = $pageMap[$section];
        
        // Handle redirects to dedicated controllers
        if (isset($config['redirect'])) {
            return redirect()->route($config['redirect']);
        }
        
        // Handle view rendering
        if (isset($config['view'])) {
            $data = $config['data']();
            return view($config['view'], $data);
        }

        abort(404);
    }

}
