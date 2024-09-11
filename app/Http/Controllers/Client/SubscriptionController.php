<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Gateways\Gateway;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Payment;
use App\Facades\Theme;

class SubscriptionController extends Controller
{

    public function index()
    {
        $subscriptions_paddle = Subscription::query()->whereGateway('Paddle')->where('user_id', Auth::user()->id)->latest()->paginate(10);
        return Theme::view('subscriptions.index', compact('subscriptions_paddle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'price_id' => 'required',
            'gateway' => 'required|max:255',
        ]);

        $order = auth()->user()->orders()->findOrFail($request->input('order_id'));
        $price = $order->package->prices()->findOrFail($request->input('price_id'));
        $gateway = Gateway::query()->whereType('subscription')->findOrFail($request->input('gateway'));

        $payment = Payment::generate([
            'user_id' => auth()->user()->id,
            'order_id' => $order->id,
            'type' => 'subscription',
            'description' => $order->name,
            'amount' => $price->renewal_price,
            'currency' => Gateway::$currency,
            'gateway' => $gateway->toArray(),
            'package_id' => $price->package->id,
            'price_id' => $price->id,
            'handler' => "App\\Http\\Controllers\\Client\\SubscriptionController",
            'show_as_unpaid_invoice' => false
        ]);

        return redirect()->route('payment.process', ['gateway' => $gateway->id, 'payment' => $payment->id]);
    }

    public static function getPricesForPackage($order_id)
    {
        return auth()->user()->orders()->whereStatus('active')->whereId($order_id)->firstOrFail()->package->prices;
    }

    public static function onPaymentCompleted(Payment $payment): void
    {
        $payment->order->extend($payment->price['period']);
    }

}
