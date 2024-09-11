<?php

namespace App\Models\Gateways;

use App\Models\PackagePrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;
use Omnipay\Common\GatewayInterface;


class PayPalGateway implements PaymentGatewayInterface
{

    private static function getBaseUrl(): string
    {
        return self::gateway()->getTestMode() ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';
    }

    private static function getAuthHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::gateway()->getToken()
        ];
    }

    private static function performGetRequest(string $endpoint): array
    {
        $response = Http::withHeaders(self::getAuthHeaders())->get(self::getBaseUrl() . $endpoint);
        if ($response->successful()){
            try {
                return $response->json();
            } catch (Exception $e){
                return [];
            }
        } elseif (config('app.debug')){
            dd($response->json(), $response);
        }
        return [];
    }

    private static function performPostRequest(string $endpoint, array $data): array
    {
        try {
            $response = Http::withHeaders(self::getAuthHeaders())->asJson()->post(self::getBaseUrl() . $endpoint, $data);
            if ($response->successful()) {
                return $response->json() ?? [];
            } elseif (config('app.debug')) {
                dd($response->json(), $response);
            }
        } catch (Exception $e) {
            // Log an error or return an empty array
            // Log::error($e->getMessage());
        }
        return [];
    }

    public static function gateway(): GatewayInterface
    {
        return Gateway::getGateway('PayPal_REST');
    }

    public static function processGateway(Gateway $gateway, Payment $payment): void
    {
        self::createSubscription($payment);
    }


    public static function returnGateway(Request $request): RedirectResponse
    {
        $validation = false;
        if ($request->hasHeader('referer')){
            if ($request->header('referer') == 'https://www.sandbox.paypal.com/' or $request->header('referer') == 'https://www.paypal.com/'){
                $validation = true;
            }
        }
        if (!$validation){
            redirect()->route('payment.cancel')->with('error', 'PayPal validation error');
        }

        if ($request->exists('subscription_id')) {

            $response = Http::withToken(self::gateway()->getToken())->get(self::getBaseUrl() . '/v1/billing/subscriptions/' . $request->get('subscription_id'));

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? null;
                if ($status == 'ACTIVE') {
                    $payment = Payment::find($data['custom_id']);
                    $payment->completed($data['id'], $data);
                    return redirect()->route('payment.success', $payment->id);
                }
            }
        }
        return redirect()->route('payment.cancel');
    }


    public static function processRefund(Payment $payment, array $data): void
    {
        $omnipayGateway = self::gateway();

        $refund_id = $payment->data['transactions'][0]['related_resources'][0]['sale']['id'];


        $response = $omnipayGateway->refund([
            'transactionReference' => $refund_id,
            'amount' => $data['refunded_amount'],
            'currency' => $payment->currency
        ])->send();

        if ($response->isSuccessful()) {
            if ($payment->type == 'balance') {
                $payment->user->balance('Payment Refunded', '-', $data['refunded_amount'], $payment->id);
            }
        } else {
            abort(403, 'Something went wrong');
            // Something went wrong
        }

        app()->setLocale($payment->user->language);
        $payment->user->notify([
            'type' => 'success',
            'icon' => '<i class="bx bxs-dollar-circle"></i>',
            'message' => __('responses.refund_payment_notify', ['payment_id' => $payment->id]),
        ]);


    }


    public static function drivers(): array
    {
        return [
            'PayPal_Rest' => [
                'driver' => 'PayPal_Rest',
                'type' => 'subscription',
                'class' => 'App\Models\Gateways\PayPalGateway',
                'endpoint' => self::endpoint(),
                'refund_support' => false,
            ]
        ];
    }

    public static function endpoint(): string
    {
        return 'paypal';
    }

    public static function getConfigMerge(): array
    {
        return [];
    }

    public static function checkSubscription(Gateway $gateway, $subscriptionId): bool
    {
        $response = self::performGetRequest('/v1/billing/subscriptions/' . $subscriptionId);
        if (!empty($response)) {
            if (isset($response['status']) and $response['status'] == 'ACTIVE') {
                return true;
            }
        }
        return false;
    }

    public static function cancelSubscription(Gateway $gateway, $subscriptionId): void
    {
        $endpoint = '/v1/billing/subscriptions/' . $subscriptionId . '/cancel';
        $data = [
            "reason" => ""
        ];
        self::performPostRequest($endpoint, $data);
    }

    private static function createSubscription(Payment $payment): void
    {
        $price = PackagePrice::find($payment->price_id);
        $plan = self::createPlan($payment, $price);

        $subscriptionData = [
            'plan_id' => $plan['id'],
            'custom_id' => $payment->id,
            'subscriber' => [
                'email_address' => $payment->user->email,
            ],
            'application_context' => [
                'user_action' => 'SUBSCRIBE_NOW',
                'shipping_preference' => 'NO_SHIPPING',
                'return_url' => route('payment.return', self::endpoint()),
                'cancel_url' => route('payment.cancel', $payment->id),
            ],
        ];
        $response = self::performPostRequest('/v1/billing/subscriptions', $subscriptionData);
        if (!empty($response)) {
            redirect($response['links']['0']['href'])->send();
        }
    }

    private static function createPlan(Payment $payment, PackagePrice $price): ?array
    {
        $price_data = $price->data ?? [];
        if (array_key_exists('paypal_product_id', $price_data)) {
            $product = self::getProduct($price->data['paypal_product_id']);
            if ($product == null) {
                $product = self::createProduct($payment, $price);
            }
        } else {
            $product = self::createProduct($payment, $price);
        }
        $planData = [
            "product_id" => $product['id'],
            "name" => $payment->package->name . "/" . $price->renewal_price,
            "description" => "Subscription package " . $payment->package->name . "/" . $price->renewal_price,
            "status" => "ACTIVE",
            "billing_cycles" => [
                [
                    "frequency" => [
                        "interval_unit" => "DAY",
                        "interval_count" => $price->period,
                    ],
                    "tenure_type" => "TRIAL",
                    "sequence" => 1,
                    "total_cycles" => 1,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $payment->amount,
                            "currency_code" => $payment->currency,
                        ],
                    ],
                ],
                [
                    "frequency" => [
                        "interval_unit" => "DAY",
                        "interval_count" => $price->period,
                    ],
                    "tenure_type" => "REGULAR",
                    "sequence" => 2,
                    "total_cycles" => self::gateway()->getTestMode() ? 999 : 0,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $price->renewal_price,
                            "currency_code" => $payment->currency,
                        ],
                    ],
                ],
            ],
            "payment_preferences" => [
                "auto_bill_outstanding" => true,
                "setup_fee" => [
                    "value" => $price->setup_fee,
                    "currency_code" => $payment->currency,
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 0,
            ],
            "taxes" => [
                "percentage" => "0",
                "inclusive" => false,
            ],
        ];

        $response = self::performPostRequest('/v1/billing/plans', $planData);
        if (!empty($response)){
            return $response;
        }
        return null;
    }

    private static function createProduct(Payment $payment, PackagePrice $price): ?array
    {
        $img_url = null;
        if (!preg_match('/\s/', $payment->package->icon)) {
            $img_url = config('app.url') . '/storage/products/' . $payment->package->icon;
        }
        $productData = [
            'name' => $payment->package->name . "/" . PackagePrice::find($payment->price_id)->price,
            'description' => 'Subscription product ' . $payment->package->name . "/" . $payment->amount,
            'type' => 'DIGITAL',
            'category' => 'SOFTWARE',
            'image_url' => $img_url,
            'home_url' => route('store.package', ['package' => $payment->package->id])
        ];

        $response = self::performPostRequest("/v1/catalogs/products", $productData);
        if (!empty($response)){
            $tmp = $price->data ?? [];
            $price->data = array_merge($tmp, ['paypal_product_id' => $response['id']]);
            $price->save();
            return $response;
        }
        return null;
    }

    private static function getProduct($product_id): ?array
    {
        $response = self::performGetRequest("/v1/catalogs/products/" . $product_id);
        if (!empty($response)){
            return $response;
        }
        return null;
    }
}
