<?php

namespace App\Console\Commands;

use App\Models\Form;
use Illuminate\Console\Command;

class AddWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:add
                            {form_id : The ID of the form}
                            {url : The webhook URL}
                            {--method=POST : HTTP method (POST/PUT)}
                            {--headers= : JSON string of headers}
                            {--inactive : Make webhook inactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a webhook to a form';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $formId = $this->argument('form_id');
        $url = $this->argument('url');
        $method = $this->option('method');
        $headersJson = $this->option('headers');
        $inactive = $this->option('inactive');

        // Validate form exists
        $form = Form::find($formId);
        if (!$form) {
            $this->error("❌ Form with ID {$formId} not found!");
            return Command::FAILURE;
        }

        // Parse headers if provided
        $headers = null;
        if ($headersJson) {
            $headers = json_decode($headersJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('❌ Invalid JSON format for headers!');
                return Command::FAILURE;
            }
        }

        // Create webhook
        $webhook = $form->webhooks()->create([
            'url' => $url,
            'method' => strtoupper($method),
            'headers' => $headers,
            'active' => !$inactive,
        ]);

        $this->info("✅ Webhook added successfully!");
        $this->line("📝 Form: {$form->name} (ID: {$form->id})");
        $this->line("🔗 URL: {$webhook->url}");
        $this->line("📡 Method: {$webhook->method}");
        $this->line("✅ Active: " . ($webhook->active ? 'Yes' : 'No'));
        $this->line("🆔 Webhook ID: {$webhook->id}");

        if ($headers) {
            $this->line("📋 Headers: " . json_encode($headers, JSON_PRETTY_PRINT));
        }

        return Command::SUCCESS;
    }
}
