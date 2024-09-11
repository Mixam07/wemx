<?php

namespace App\Models\Gateways;

use Illuminate\Http\Request;
use App\Models\Payment;

/**
 * Summary of PaymentGatewayInterface
 */
interface PaymentGatewayInterface
{
    public static function processGateway(Gateway $gateway, Payment $payment);

    public static function returnGateway(Request $request);

    public static function processRefund(Payment $payment, array $data);

    public static function checkSubscription(Gateway $gateway, $subscriptionId): bool;

    public static function drivers(): array;

    public static function endpoint(): string;

    public static function getConfigMerge(): array;
}
