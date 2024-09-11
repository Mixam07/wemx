<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Facades\Theme;
use App\Models\Categories;
use App\Models\Package;
use App\Models\Coupon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;

class StoreController extends Controller
{

    public function index(Categories $categories)
    {
        return Theme::view('store.index', compact('categories'));
    }

    public function pricing(Package $package)
    {
        return Theme::view('store.pricing', compact('package'));
    }

    public function service($service)
    {
        $category = Categories::query()->where('link', $service)->firstOrFail();
        if ($category->status == 'inactive' or $category->status == 'restricted') {
            if (!auth()->check() OR !auth()->user()->is_admin()) {
                return redirect('/dashboard')->with('error',
                    trans('responses.package_not_available',
                        ['default' => 'This package is not available at this time.'])
                    );
            }
        }

        return Theme::view('store.store', compact('category'));
    }

    public function viewPackage(Package $package)
    {
        $package->service()->eventLoadPackage($package);
        if ($package->status == 'inactive' or $package->status == 'restricted') {
            if (!auth()->check() OR !auth()->user()->is_admin()) {
                return redirect('/dashboard')->with('error',
                    trans('responses.package_not_available',
                        ['default' => 'This package is not available at this time.'])
                    );
            }
        }

        // define package in request
        request()->package = $package;

        return Theme::view('store.packages.view', compact('package'));
    }

    public function validateCoupon(Package $package, $code = ''): array
    {
        if (!Coupon::query()->where('code', $code)->exists()) {
            Session::forget('coupon_code');
            return ['success' => false, 'description' =>
                trans('responses.coupon_invalid_expired',
                    ['default' => 'Invalid coupon or coupon has expired'])
                ];
        }

        $coupon = Coupon::query()->where('code', $code)->firstOrFail();
        if(!$coupon->isValid()) {
            Session::forget('coupon_code');
            return ['success' => false, 'description' => 'Coupon has reached usage limit or has expired.'];
        }

        if (!in_array($package->id, $coupon->applicable_products)) {
            Session::forget('coupon_code');
            return ['success' => false, 'description' =>
                trans('responses.coupon_package_applicable_error',
                    ['default' => 'Coupon is not applicable for this package.'])
                ];
        }

        $coupon = $coupon->toArray();
        $coupon = Arr::except($coupon, ['id', 'allowed_uses', 'applicable_products', 'notes', 'expires_at', 'created_at', 'updated_at']);
        $discount = ($coupon['discount_type'] == 'percentage') ? $coupon['discount_amount'] . '%' : currency('symbol') . number_format($coupon['discount_amount'], 2);

        Session::put('coupon_code', $code);

        return array_merge(['success' => true, 'description' => 'Coupon applied successfully, you receive a discount of '. $discount], $coupon);

    }
}
