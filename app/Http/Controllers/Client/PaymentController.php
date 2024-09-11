<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Modules\Affiliates\Entities\Affiliate;
use App\Models\Gateways\Gateway;
use App\Models\PackagePrice;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\ErrorLog;
use App\Models\Package;
use App\Models\PaymentTax;
use App\Models\Payment;
use App\Models\Coupon;
use App\Facades\Theme;
use App\Models\Order;
use App\Rules\ValidDomain;
use Exception;

use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    protected $gateway;

    public function createOrderPayment(Request $request, Package $package)
    {
        // call service before processing payment
        $package->service()->eventCheckout($package);


//        if ($request->has('environment')){
//            $data = $request->input('environment');
//            foreach ($data as $key => $value) {
//                $request->request->set($key, $value);
//            }
//        }

        $rules = $package->service()->getCheckoutRules($package);
        $validated = $request->validate(array_merge([
            'domain' => ['sometimes', new ValidDomain],
        ],
        $rules
        ));

        $price = PackagePrice::query()->where('package_id', $package->id)->findOrFail($request->input('price_id'));
        $gateway = Gateway::query()->findOrFail($request->input('gateway'));
        $this->gateway = $gateway;

        $options = $request->except(['_token', 'price_id', 'gateway', 'notes', 'country', 'zip_code']);

        if (!$package->inStock()) {
            return redirect()->back()->with('error',
                trans('responses.package_limit_error',
                    ['default' => 'The package either has reached its global- or per user allowance limit.'])
            );
        }

        $payment = Payment::generate([
            'user_id' => auth()->user()->id,
            'type' => $request->input('gateway_type', 'once'),
            'description' => $package->name,
            'amount' => max($this->calculateTotalPrice($price->totalPrice()), 0),
            'gateway' => $gateway->toArray(),
            'package_id' => $package->id,
            'price_id' => $price->id,
            'options' => $options,
            'handler' => \App\Handlers\NewOrder::class,
            'notes' => $request->input('notes'),
            'show_as_unpaid_invoice' => false
        ]);

        $tax = (float) number_format($this->calculateTax($price->totalPrice() - $this->calculateTotalDiscount($price->totalPrice())), 2);
        if($tax > 0) {
            PaymentTax::create([
                'payment_id' => $payment->id,
                'country' => request()->input('country'),
                'amount' => $tax,
                'included_in_price' => settings('tax_add_to_price', false),
            ]);
        }

        return redirect()->route('payment.process', ['gateway' => $gateway->id, 'payment' => $payment->id]);
    }

    // calculate the price after discounts
    protected function calculateTotalPrice($price)
    {
        $price = $price - $this->calculateTotalDiscount($price);

        if(settings('tax_add_to_price')) {
            $price = $price + $this->calculateTax($price);
        }

        return (float) str_replace(',', '', number_format($price, 2));
    }

    protected function calculateTotalDiscount($price)
    {
        $totalDiscount = 0;
        if (session('coupon_code')) {
            $coupon = Coupon::where('code', session('coupon_code'))->first();
            if ($coupon and $coupon->isValid()) {
                $coupon->decrement('allowed_uses');
                if ($coupon->discount_type == 'percentage') {
                    $totalDiscount += $price * ($coupon->discount_amount / 100);
                } else {
                    $totalDiscount += $coupon->discount_amount;
                }
            }
        }

        if (Cookie::has('affiliate')) {
            $totalDiscount += $price * Affiliate::calculateDiscountFactor(Cookie::get('affiliate'));
        }
        return $totalDiscount;
    }

    protected function calculateTax($price)
    {
        if(!settings('taxes')) {
            return 0;
        }

        if(in_array($this->gateway->id, json_decode(settings('tax_disabled_gateways', '["0"]')))) {
            return 0;
        }

        $country = request()->input('country');
        $rates = config("tax.rates.$country", false);
        if($rates) {
            $tax = 0;
            $rate = $rates['standard_rate'] / 100;
            if(settings('tax_add_to_price')) {
                $tax = $price * $rate;
            } else {
                $tax = $price - ($price / (1 + $rate));
            }

            return $tax;
        }

        return 0;
    }

    public function createOrderSubscription()
    {
        $order = Order::findOrFail(request()->get('order_id'));
        $price = $order->package->prices()->findOrFail(request()->get('price_id'));
        $payment = Payment::where('order_id', $order->id)->where('user_id', auth()->user()->id)->first();
        $gateway = Gateway::find(request()->get('gateway'));

        if ($payment == null){
            return redirect()->back();
        }
        $payment->price_id = $price->id;
        $payment->amount = $this->calculateTotalPrice($price->totalPrice());
        $payment->type = $gateway->type;
        $payment->save();
        return $this->processPayment($gateway, $payment);
    }


    public function processPayment(Gateway $gateway, Payment $payment)
    {
        // check whether the payment amount is 0, if so process the payment right away
        if ($payment->amount == 0) {
            $payment->completed();
            return redirect('/dashboard')->with('success',
                trans('responses.payment_completed_success',
                    ['default' => 'Your payment has been completed!'])
            );

        }

        try {
            $gateway_type = request()->input('gateway_type', 'once');
            return $gateway->class::processGateway($gateway, $payment, $gateway_type);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function paymentReturn(Request $request, $gateway)
    {
        try {
            $gateway = Gateway::query()->where('endpoint', $gateway)->first();
            return $gateway->class::returnGateway($request);
        } catch (Exception $error) {
            ErrorLog::catch('payment:return', $error);
            return abort('404');
        }
    }

    public function paymentCancel(Request $request, Payment $payment)
    {
        return Theme::view('store.cancel', compact('payment'));
    }

    public function paymentSuccess(Request $request, Payment $payment)
    {
        return Theme::view('store.success', compact('payment'));
    }

    public function invoice(Payment $payment)
    {
        if ($payment->status == 'unpaid' and !$payment->show_as_unpaid_invoice) {
            return redirect()->route('dashboard')->with('error',
                trans('responses.invoice_unpaid_error',
                    ['default' => 'The requested invoice is no longer available.'])
            );
        }

        return Theme::view('invoice', compact('payment'));
    }

    public function downloadInvoice(Payment $payment)
    {
        $pdf = Pdf::loadView(Theme::path('invoice-pdf'), ['payment' => $payment]);
        return $pdf->download("invoice-{$payment->shortId()}.pdf");
    }

    public function payInvoice(Payment $payment)
    {
        $gateway = Gateway::query()->findOrFail(request()->input('gateway'));
        $payment->update(['gateway' => $gateway->toArray()]);

        return redirect()->route('payment.process', ['gateway' => $gateway->id, 'payment' => $payment->id]);
    }

    public function createBalancePayment(Request $request)
    {
        request()->validate([
            'amount' => 'required|numeric|between:1,999'
        ]);

        $gateway = Gateway::query()->findOrFail($request->input('gateway'));

        $payment = Payment::generate([
            'user_id' => auth()->user()->id,
            'description' => 'Balance Top up',
            'type' => 'balance',
            'amount' => $request->input('amount'),
            'gateway' => $gateway->toArray(),
            'handler' => "App\\Models\\Balance",
            'show_as_unpaid_invoice' => false
        ]);

        return redirect()->route('payment.process', ['gateway' => $gateway->id, 'payment' => $payment->id]);
    }
}
