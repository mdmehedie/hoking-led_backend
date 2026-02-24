<?php

namespace App\Jobs;

use App\Models\SocialAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PublishToSocialMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // 1 minute, 5 minutes, 15 minutes

    protected $content;
    protected $contentType; // 'blog' or 'product'
    protected $platforms; // array of platforms to post to, or null for all active

    /**
     * Create a new job instance.
     *
     * @param mixed $content The content model (Blog or Product)
     * @param string $contentType 'blog' or 'product'
     * @param array|null $platforms Specific platforms to post to, or null for all active
     */
    public function __construct($content, string $contentType, ?array $platforms = null)
    {
        $this->content = $content;
        $this->contentType = $contentType;
        $this->platforms = $platforms;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $accounts = $this->getSocialAccounts();

        if ($accounts->isEmpty()) {
            Log::info("No active social media accounts found for publishing {$this->contentType}", [
                'content_id' => $this->content->id,
                'content_type' => $this->contentType,
            ]);
            return;
        }

        $postData = $this->preparePostData();

        foreach ($accounts as $account) {
            try {
                $this->publishToPlatform($account, $postData);
                Log::info("Successfully published {$this->contentType} to {$account->platform}", [
                    'content_id' => $this->content->id,
                    'account_id' => $account->id,
                    'platform' => $account->platform,
                ]);
            } catch (Exception $e) {
                Log::error("Failed to publish {$this->contentType} to {$account->platform}", [
                    'content_id' => $this->content->id,
                    'account_id' => $account->id,
                    'platform' => $account->platform,
                    'error' => $e->getMessage(),
                ]);
                throw $e; // Re-throw to trigger retry
            }
        }
    }

    /**
     * Get social accounts to publish to
     */
    protected function getSocialAccounts()
    {
        if ($this->platforms) {
            return SocialAccount::whereIn('platform', $this->platforms)
                ->where('is_active', true)
                ->get();
        }

        return SocialAccount::active();
    }

    /**
     * Prepare post data for the content
     */
    protected function preparePostData(): array
    {
        $baseUrl = config('app.url');

        if ($this->contentType === 'blog') {
            return [
                'title' => $this->content->title,
                'excerpt' => $this->content->excerpt,
                'url' => $baseUrl . '/blog/' . $this->content->slug,
                'image' => $this->content->image_path ? $baseUrl . '/storage/' . $this->content->image_path : null,
                'type' => 'blog',
            ];
        } elseif ($this->contentType === 'product') {
            return [
                'title' => $this->content->title,
                'description' => $this->content->short_description,
                'url' => $baseUrl . '/products/' . $this->content->slug,
                'image' => $this->content->main_image ? $baseUrl . '/storage/' . $this->content->main_image : null,
                'type' => 'product',
            ];
        }

        return [];
    }

    /**
     * Publish to a specific social media platform
     */
    protected function publishToPlatform(SocialAccount $account, array $postData): void
    {
        switch ($account->platform) {
            case 'facebook':
                $this->publishToFacebook($account, $postData);
                break;
            case 'twitter':
                $this->publishToTwitter($account, $postData);
                break;
            case 'linkedin':
                $this->publishToLinkedIn($account, $postData);
                break;
            default:
                throw new Exception("Unsupported platform: {$account->platform}");
        }
    }

    /**
     * Publish to Facebook
     */
    protected function publishToFacebook(SocialAccount $account, array $postData): void
    {
        $credentials = $account->credentials;
        $pageId = $credentials['page_id'] ?? null;
        $accessToken = $credentials['access_token'] ?? null;

        if (!$pageId || !$accessToken) {
            throw new Exception('Facebook page_id and access_token are required');
        }

        $message = $this->generatePostMessage($postData, 'facebook');

        $response = Http::post("https://graph.facebook.com/v18.0/{$pageId}/feed", [
            'message' => $message,
            'link' => $postData['url'],
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) {
            throw new Exception('Facebook API error: ' . $response->body());
        }
    }

    /**
     * Publish to Twitter
     */
    protected function publishToTwitter(SocialAccount $account, array $postData): void
    {
        $credentials = $account->credentials;
        $apiKey = $credentials['api_key'] ?? null;
        $apiSecret = $credentials['api_secret'] ?? null;
        $accessToken = $credentials['access_token'] ?? null;
        $accessTokenSecret = $credentials['access_token_secret'] ?? null;

        if (!$apiKey || !$apiSecret || !$accessToken || !$accessTokenSecret) {
            throw new Exception('Twitter API credentials are incomplete');
        }

        $message = $this->generatePostMessage($postData, 'twitter');

        // For simplicity, using a basic HTTP approach
        // In production, you'd want to use a proper Twitter SDK
        $response = Http::withBasicAuth($apiKey, $apiSecret)
            ->post('https://api.twitter.com/2/tweets', [
                'text' => $message . ' ' . $postData['url'],
            ], [
                'Authorization' => 'Bearer ' . $accessToken,
            ]);

        if ($response->failed()) {
            throw new Exception('Twitter API error: ' . $response->body());
        }
    }

    /**
     * Publish to LinkedIn
     */
    protected function publishToLinkedIn(SocialAccount $account, array $postData): void
    {
        $credentials = $account->credentials;
        $accessToken = $credentials['access_token'] ?? null;
        $organizationId = $credentials['organization_id'] ?? null;

        if (!$accessToken) {
            throw new Exception('LinkedIn access_token is required');
        }

        $message = $this->generatePostMessage($postData, 'linkedin');

        $postData = [
            'author' => $organizationId ? "urn:li:organization:{$organizationId}" : 'urn:li:person:me',
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $message,
                    ],
                    'shareMediaCategory' => 'NONE',
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post('https://api.linkedin.com/v2/ugcPosts', $postData);

        if ($response->failed()) {
            throw new Exception('LinkedIn API error: ' . $response->body());
        }
    }

    /**
     * Generate platform-specific post message
     */
    protected function generatePostMessage(array $postData, string $platform): string
    {
        $baseMessage = "🚀 New {$postData['type']} published: {$postData['title']}";

        if ($postData['excerpt'] ?? null) {
            $excerpt = strlen($postData['excerpt']) > 100
                ? substr($postData['excerpt'], 0, 100) . '...'
                : $postData['excerpt'];
            $baseMessage .= "\n\n{$excerpt}";
        }

        // Add URL to the post
        $url = $this->generateContentUrl($postData);
        $baseMessage .= "\n\n🔗 Read more: {$url}";

        $baseMessage .= "\n\n#{$postData['type']} #newcontent";

        // Platform-specific adjustments
        switch ($platform) {
            case 'twitter':
                // Twitter has character limits, so keep it shorter
                $message = strlen($baseMessage) > 200 ? substr($baseMessage, 0, 197) . '...' : $baseMessage;
                // If URL is still too long, shorten it
                if (strlen($message) > 280) {
                    $message = substr($message, 0, 277) . '...';
                }
                return $message;
            case 'facebook':
            case 'linkedin':
            default:
                return $baseMessage;
        }
    }

    /**
     * Generate content URL using frontend URL, prefix, and slug
     */
    protected function generateContentUrl(array $postData): string
    {
        // Get frontend URL from app settings or fallback to app URL
        $frontendUrl = \App\Models\AppSetting::first()?->frontend_url ?? config('app.url');

        // Ensure frontend URL doesn't end with /
        $frontendUrl = rtrim($frontendUrl, '/');

        // Get content type prefix from app settings with fallback
        $prefix = \App\Models\AppSetting::first()?->{$postData['type'] . '_prefix'} ?? $this->getContentTypePrefix($postData['type']);

        // Ensure prefix starts and ends with /
        $prefix = '/' . trim($prefix, '/') . '/';

        // Get slug
        $slug = $postData['slug'] ?? '';

        // Construct full URL
        return $frontendUrl . $prefix . $slug;
    }

    /**
     * Get URL prefix for content type (fallback method)
     */
    protected function getContentTypePrefix(string $contentType): string
    {
        return match($contentType) {
            'blog' => '/blog/',
            'news' => '/news/',
            'page' => '/pages/',
            'case_study' => '/case-studies/',
            'product' => '/products/',
            default => '/',
        };
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        Log::error('Social media publishing job failed', [
            'content_id' => $this->content->id ?? null,
            'content_type' => $this->contentType,
            'platforms' => $this->platforms,
            'error' => $exception->getMessage(),
        ]);
    }
}
