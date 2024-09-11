<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Facades\Theme;

class SupportChatController extends Controller
{
    public function chat()
    {
        // dd($this->interactWithChatGPT('How do I install Laravel?'));
        return Theme::view('chat.index');
    }

    public function interactWithChatGPT()
    {
        $apiKey = '';
        $endpoint = 'https://api.openai.com/v1/engines/text-davinci-003/completions';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'prompt' => $_GET['prompt'],
            'max_tokens' => 4000,
            'temperature' => 0.0,
        ])->object();

        return response()->json(['res' => nl2br($response->choices[0]->text)]);

        // return nl2br($response->choices[0]->text);
    }
}
