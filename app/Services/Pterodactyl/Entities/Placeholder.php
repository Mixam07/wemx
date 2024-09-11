<?php

namespace App\Services\Pterodactyl\Entities;

use Exception;
use Illuminate\Support\Str;

class Placeholder
{
    const PLACEHOLDERS = ['AUTO_PORT', 'RANDOM_TEXT', 'RANDOM_NUMBER', 'NODE_IP', 'PASSWORD', 'CURRENT_DATE', 'CURRENT_TIME', 'USERNAME', 'USER_EMAIL', 'USER_ID'];

    public static function prepareEnvAllocations(array $env, Node $node, array $egg): array
    {
        $data = [];
        $replace_port_keys = [];
        $allocations_ids = [];
        $port_count = 1;

        $userName = request()->user()->username ?? auth()->user()->username ?? config('app.name');
        $userEmail = request()->user()->email ?? auth()->user()->email ?? config('app.name');
        $userId = request()->user()->id ?? auth()->user()->id ?? 0;


        foreach ($env as $key => $value) {
            if (str_contains($value, 'AUTO_PORT')) {
                $env[$key] = 'AUTO_PORT';
                $replace_port_keys[] = $key;
                $port_count++;
                continue;
            }

            $value = str_contains($value, 'USERNAME') ? str_replace('USERNAME', $userName, $value) : $value;
            $value = str_contains($value, 'RANDOM_TEXT') ? str_replace('RANDOM_TEXT', Str::random(10), $value) : $value;
            $value = str_contains($value, 'RANDOM_NUMBER') ? str_replace('RANDOM_NUMBER', random_int(1000000000, 9999999999), $value) : $value;
            $value = str_contains($value, 'NODE_IP') ? str_replace('NODE_IP', $node->data['ip'] ?? $node->data['fqdn'], $value) : $value;
            $value = str_contains($value, 'PASSWORD') ? str_replace('PASSWORD', $userName . Str::random(10), $value) : $value;
            $value = str_contains($value, 'CURRENT_DATE') ? str_replace('CURRENT_DATE', now()->toDateString(), $value) : $value;
            $value = str_contains($value, 'CURRENT_TIME') ? str_replace('CURRENT_TIME', now()->toTimeString(), $value) : $value;
            $value = str_contains($value, 'USER_EMAIL') ? str_replace('USER_EMAIL', $userEmail, $value) : $value;
            $value = str_contains($value, 'USER_ID') ? str_replace('USER_ID', $userId, $value) : $value;
            $env[$key] = $value;
        }

        try {
            $i = -1;
            foreach ($node->fetchRequiredFreePorts($port_count) as $allocation_id => $port) {
                if ($i == -1) {
                    $allocations_ids['default'] = $allocation_id;
                    $i++;
                    continue;
                }
                $env[$replace_port_keys[$i]] = $port;
                $allocations_ids['additional'][] = $allocation_id;
                $i++;
            }
        } catch (Exception $e) {
            ErrorLog('pterodactyl::Placeholder::prepareEnvAllocations', $e->getMessage(), 'CRITICAL');
            redirect()->back()->with('error', $e->getMessage())->send();
        }
        $data['environment'] = self::convertValuesAccordingToRules($env, $egg);
        $data['allocation'] = $allocations_ids;
        return $data;
    }

    private static function convertValuesAccordingToRules($values, $env): array
    {
        $output = [];
        foreach ($env['variables'] as $var) {
            if (isset($values[$var['env_variable']])) {
                $rules = explode('|', $var['rules']);
                foreach ($rules as $rule) {
                    if (str_contains($rule, 'string')) {
                        $values[$var['env_variable']] = (string)$values[$var['env_variable']];
                    } else if (str_contains($rule, 'boolean') || str_contains($rule, 'bool')) {
                        $values[$var['env_variable']] = filter_var($values[$var['env_variable']], FILTER_VALIDATE_BOOLEAN);
                    } else if (str_contains($rule, 'integer') || str_contains($rule, 'int') || str_contains($rule, 'numeric')) {
                        $values[$var['env_variable']] = (int)$values[$var['env_variable']];
                    } else if (str_contains($rule, 'array')) {
                        $values[$var['env_variable']] = (array)$values[$var['env_variable']];
                    }
                }
                $output[$var['env_variable']] = $values[$var['env_variable']];
            }
        }
        return $output;
    }
}
