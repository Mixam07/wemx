<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $data;
    protected $method;
    protected $headers;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param  string  $url
     * @param  array  $data
     * @param  string  $method
     * @param  array  $headers
     * @return void
     */
    public function __construct(string $url, string $method, array $data = [], array $headers = [])
    {
        $this->url = $url;
        $this->data = $data;
        $this->method = $method;
        $this->headers = $headers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $method = $this->method;

        $response = Http::withHeaders(array_merge([
            'Accept' => 'application/json',
        ], $this->headers))->$method($this->url, $this->data);

        if ($response->failed()) {
            // If the request failed, we'll throw an exception to mark this job as failed
            ErrorLog('order::webhooks::failed', $response->body(), 'CRITICAL');

            // If the request failed, we'll throw an exception to mark this job as failed
            throw new \Exception('Webhook request failed');
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        //
    }
}