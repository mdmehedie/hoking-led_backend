<?php

namespace App\Jobs;

use App\Models\FormWebhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60; // 1 minute
    public array $backoffStrategy = [60, 300, 900]; // 1min, 5min, 15min

    protected FormWebhook $webhook;
    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(FormWebhook $webhook, array $data)
    {
        $this->webhook = $webhook;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->webhook->active) {
            Log::info("Webhook {$this->webhook->id} is inactive, skipping.");
            return;
        }

        try {
            $httpClient = Http::timeout(30);

            // Add custom headers if provided
            if ($this->webhook->headers) {
                foreach ($this->webhook->headers as $key => $value) {
                    $httpClient = $httpClient->withHeaders([$key => $value]);
                }
            }

            // Send the request based on method
            $response = match ($this->webhook->method) {
                'POST' => $httpClient->post($this->webhook->url, $this->data),
                'PUT' => $httpClient->put($this->webhook->url, $this->data),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$this->webhook->method}")
            };

            if ($response->successful()) {
                Log::info("Webhook {$this->webhook->id} sent successfully to {$this->webhook->url}", [
                    'webhook_id' => $this->webhook->id,
                    'url' => $this->webhook->url,
                    'method' => $this->webhook->method,
                    'status_code' => $response->status(),
                    'attempt' => $this->attempts(),
                ]);
            } else {
                Log::warning("Webhook {$this->webhook->id} failed with status {$response->status()}", [
                    'webhook_id' => $this->webhook->id,
                    'url' => $this->webhook->url,
                    'method' => $this->webhook->method,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'attempt' => $this->attempts(),
                ]);

                // Throw exception to trigger retry
                throw new \Exception("Webhook request failed with status {$response->status()}");
            }

        } catch (Throwable $e) {
            Log::error("Webhook {$this->webhook->id} failed", [
                'webhook_id' => $this->webhook->id,
                'url' => $this->webhook->url,
                'method' => $this->webhook->method,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Webhook {$this->webhook->id} failed permanently after {$this->tries} attempts", [
            'webhook_id' => $this->webhook->id,
            'url' => $this->webhook->url,
            'method' => $this->webhook->method,
            'error' => $exception->getMessage(),
        ]);

        // You could add notification logic here for permanent failures
        // e.g., send email to admin about failed webhook
    }
}
