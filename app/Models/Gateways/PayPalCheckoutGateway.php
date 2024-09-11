<?php

namespace App\Models\Gateways;

use App\Models\Gateways\PaymentGatewayInterface;
use App\Models\Gateways\Gateway;
use Illuminate\Http\Request;
use App\Facades\Theme;
use App\Models\Payment;
use App\Models\Balance;
use App\Models\Notification;
use App\Models\ErrorLog;

class PayPalCheckoutGateway implements PaymentGatewayInterface
{

    public static function processGateway(Gateway $gateway, Payment $payment)
    {
        if ($gateway->config['production'] == 'true') {
            $url = "https://ipnpb.paypal.com/cgi-bin/webscr";
        } else {
            $url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
        }

        echo
        '<body onload="document.redirectform.submit()" style="display: none">
            <form action="'. $url .'" method="post" name="redirectform">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="'. $gateway->config['paypal_email'] .'">
                <input type="hidden" name="item_name" value="'.$payment->description.'">
                <input type="hidden" name="item_number" value="'.$payment->id.'">
                <input type="hidden" name="amount" value="'.$payment->amount.'">
                <input type="hidden" name="currency_code" value="'.$payment->currency.'">
                <input name="cancel_return" value="' . route('payment.cancel', ['payment' => $payment->id]) . '">
                <input name="notify_url" value="' . route('payment.return', ['gateway' => self::endpoint(), 'payment' => $payment->id]) . '">
                <input name="return" value="' . route('payment.success', ['payment' => $payment->id]) . '">
                <input name="rm" value="2">
                <input name="charset" value="utf-8">
                <input name="no_note" value="1">
              </form>
        </body>';
    }

    public static function returnGateway(Request $request)
    {
        try {
        $payment = Payment::findOrFail($request->input('payment'));

        // The IPN request is a POST request, so we'll get the data from the request input
        $ipnPayload = $request->all();

        // Before processing the IPN message, you should validate it to make sure it's actually from PayPal
        $ipnCheck = self::validateIpn($ipnPayload);

        if ($ipnCheck) {
            // Process IPN message
            $paymentStatus = $ipnPayload['payment_status'];

            if ($paymentStatus == "Completed") {
                // Your code to handle successful payment
                $payment->completed();
            }

            else {
                // Your code to handle failed payment
            }
        }

        } catch(\Exception $error) {
            ErrorLog::catch('payment:return:paypal-checkout:failed', $error);
        }
    }

    public static function drivers(): array
    {
        return [
            'PayPalCheckout' => [
                'driver' => 'PayPalCheckout',
                'type' => 'once',
                'class' => 'App\Models\Gateways\PayPalCheckoutGateway',
                'endpoint' => self::endpoint(),
                'refund_support' => false,
            ]
        ];
    }

    private static function validateIpn($ipnPayload)
    {
        // This is the URL you'll post the IPN message back to for validation
        $gateway = Gateway::where('driver', 'PayPalCheckout')->firstOrFail();

        if ($gateway->config['production'] == 'true') {
            $paypalUrl = "https://ipnpb.paypal.com/cgi-bin/webscr";
        } else {
            $paypalUrl = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
        }


        $payload = 'cmd=_notify-validate';

        foreach ($ipnPayload as $key => $value) {
            $value = urlencode($value);
            $payload .= "&$key=$value";
        }

        // Use CURL to post back the data for validation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypalUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return strcmp($result, "VERIFIED") == 0;
    }

    public static function endpoint(): string
    {
        return 'paypal-checkout';
    }

    public static function getConfigMerge(): array
    {
        return [
            'paypal_email' => '',
            'production' => true,
        ];
    }

    public static function processRefund(Payment $payment, array $data)
    {
        // TODO: Implement processRefund() method.
    }

    public static function checkSubscription(Gateway $gateway, $subscriptionId): bool
    {
        return false;
    }
}
