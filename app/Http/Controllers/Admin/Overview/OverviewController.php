<?php

namespace App\Http\Controllers\Admin\Overview;

use App\Entities\ZipManager;
use App\Http\Controllers\Controller;
use App\Facades\AdminTheme as Theme;
use App\Models\Categories;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class OverviewController extends Controller
{
    public function index()
    {
        // get data for paid payments
        $incomeSubscriptions = $this->incomeSubscriptionsDays(request()->input('period', 30));
        $period = request()->input('period', 30);
        $paid_payments = $this->getPaidPayments($period);
        $paid_subscriptions = $this->getPaidPaymentsSubscriptions($period);
        $unpaid_payments = $this->getUnpaidPaidPayments($period);
        $registrations = $this->getNewRegistrations($period);

        list($paid_dates_sub, $paid_amounts_sub) = $this->prepareDataForView($paid_subscriptions);
        list($paid_dates, $paid_amounts) = $this->prepareDataForView($paid_payments);
        list($unpaid_dates, $unpaid_amounts) = $this->prepareDataForView($unpaid_payments);

        $registration_dates = [0, 0];
        $registration_counts = [0, 0];
        foreach ($registrations as $registration) {
            $registration_dates[] = $registration->date;
            $registration_counts[] = $registration->count;
        }

        return Theme::view('overview.index',
            compact('paid_dates_sub', 'paid_amounts_sub', 'incomeSubscriptions','paid_dates', 'paid_amounts', 'unpaid_amounts', 'unpaid_dates', 'registration_dates', 'registration_counts'));
    }

    private function prepareDataForView($payments)
    {
        $dates = [0, 0];
        $amounts = [0, 0];

        foreach ($payments as $payment) {
            $dates[] = $payment->date;
            $amounts[] = $payment->sum;
        }

        return [$dates, $amounts];
    }

    private function incomeSubscriptionsDays($days): float
    {
        $totalIncomePerDays = 0;

        $payments = Payment::query()
            ->whereType('subscription')
            ->whereStatus('paid')
            ->whereHas('order', function ($query) {
                $query->whereStatus('active');
            })
            ->with('price')
            ->get();

        foreach ($payments as $payment) {
            $dailyIncome = $payment->price->renewal_price / $payment->price->period;
            $totalIncomePerDays += $dailyIncome * $days;
        }

        return round($totalIncomePerDays, 2);
    }

    private function getPaidPaymentsSubscriptions($days)
    {
        $startDate = now()->subDays($days)->format('Y-m-d');

        return Payment::query()
            ->whereDate('created_at', '>', $startDate)
            ->whereType('subscription')
            ->whereStatus('paid')
            ->selectRaw('sum(amount) as sum, DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPaidPayments($days)
    {
        $startDate = now()->subDays($days)->format('Y-m-d');

        return Payment::query()
            ->whereDate('created_at', '>', $startDate)
            ->where('type', '!=', 'subscription')
            ->whereStatus('paid')
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(gateway, "$.driver")) != ?', ['Balance'])
            ->selectRaw('sum(amount) as sum, DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }



    private function getUnpaidPaidPayments($days)
    {
        $startDate = now()->subDays($days)->format('Y-m-d');

        return Payment::query()
            ->whereDate('created_at', '>', $startDate)
            ->where('type', '!=', 'subscription')
            ->whereStatus('unpaid')
            ->selectRaw('sum(amount) as sum, DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getNewRegistrations($days)
    {
        $startDate = now()->subDays($days)->format('Y-m-d');

        return User::query()
            ->whereDate('created_at', '>', $startDate)
            ->selectRaw('count(*) as count, DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * @throws \Exception
     */
    public function resourceInstall($resource_id, $version_id)
    {
        $zip = new ZipManager();
        $resp = $zip->resourceDownloadAndExtract($resource_id, $version_id);
        if ($resp['status']) {
            return redirect()->back()->with('success', __('admin.resource_installed_success'));
        }
        return redirect()->back()->with('error', $resp['error']);
    }

    public function modeToggle()
    {
        $currentMode = Cache::get('admin_theme_mode_'.auth()->user()->id, 'light');
        $newMode = $currentMode === 'light' ? 'dark' : 'light';
        Cache::put('admin_theme_mode_'.auth()->user()->id, $newMode);

        return redirect()->back();
    }

    public function changeOrder($id, $model, $direction = 'up')
    {
        if (!in_array($direction, ['up', 'down'])) {
            return redirect()->back()->withErrors([__('admin.order_changed_error')]);
        }
        $item = match ($model) {
            'categories' => Categories::find($id),
            'packages' => Package::find($id),
            default => Categories::find($id),
        };
        $item->changeOrder($direction);
        return redirect()->back()->withSuccess(__('admin.order_changed'));
    }
}
