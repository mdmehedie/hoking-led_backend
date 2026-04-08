<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Resources\NewsletterSubscriptionResource;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiFrontendNewsletterController extends ApiBaseController
{
    /**
     * Disposable email domains to block
     */
    protected array $disposableDomains = [
        'mailinator.com', 'tempmail.com', 'throwaway.email',
        'guerrillamail.com', 'sharklasers.com', 'yopmail.com',
    ];

    public function subscribe(Request $request): JsonResponse
    {
        // Bot detection: too fast submission (< 1 second)
        if ($request->has('_token_time') && (time() - (int) $request->_token_time) < 1) {
            return $this->errorResponse(
                ['error' => __('Bot activity detected')],
                403
            );
        }

        // Honeypot check
        if ($request->has('_website') && !empty($request->_website)) {
            // Silently accept to avoid bot knowing
            return $this->createdResponse(
                ['subscription' => ['id' => null, 'email' => $request->email]],
                __('Successfully subscribed to newsletter')
            );
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscriptions,email'],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'source' => ['nullable', 'string', Rule::in(['website', 'footer', 'popup', 'checkout', 'landing_page', 'import'])],
            'preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableResponse(['errors' => $validator->errors()], __('Validation failed'));
        }

        // Block disposable email domains
        $emailDomain = strtolower(substr(strrchr($request->email, '@'), 1));
        if (in_array($emailDomain, $this->disposableDomains)) {
            return $this->errorResponse(
                ['email' => __('Disposable email addresses are not allowed')],
                422
            );
        }

        $subscription = NewsletterSubscription::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'source' => $request->source ?? 'website',
            'status' => 'active',
            'subscribed_at' => now(),
            'consent_given' => true,
            'consent_ip' => $request->ip(),
            'preferences' => $request->preferences,
        ]);

        return $this->createdResponse(
            ['subscription' => new NewsletterSubscriptionResource($subscription)],
            __('Successfully subscribed to newsletter')
        );
    }

    public function unsubscribe(string $token): JsonResponse
    {
        // Rate limit unsubscribe: 5 per minute per token
        $unsubscribeKey = 'newsletter_unsub_' . $token;
        if (cache()->has($unsubscribeKey)) {
            return $this->errorResponse(
                ['error' => __('Too many unsubscribe attempts. Please try again later.')],
                429
            );
        }
        cache()->put($unsubscribeKey, true, 60);

        $subscription = NewsletterSubscription::where('unsubscribe_token', $token)->first();

        if (!$subscription) {
            return $this->notFoundResponse([], __('Subscription not found'));
        }

        if ($subscription->status === 'unsubscribed') {
            return $this->okResponse(
                ['subscription' => new NewsletterSubscriptionResource($subscription)],
                __('You are already unsubscribed')
            );
        }

        $subscription->unsubscribe();

        return $this->okResponse(
            ['subscription' => new NewsletterSubscriptionResource($subscription)],
            __('Successfully unsubscribed from newsletter')
        );
    }

    public function confirm(string $token): JsonResponse
    {
        // Rate limit confirm: 5 per minute per token
        $confirmKey = 'newsletter_confirm_' . $token;
        if (cache()->has($confirmKey)) {
            return $this->errorResponse(
                ['error' => __('Too many confirmation attempts. Please try again later.')],
                429
            );
        }
        cache()->put($confirmKey, true, 60);

        $subscription = NewsletterSubscription::where('unsubscribe_token', $token)->first();

        if (!$subscription) {
            return $this->notFoundResponse([], __('Subscription not found'));
        }

        if ($subscription->status === 'active') {
            return $this->okResponse(
                ['subscription' => new NewsletterSubscriptionResource($subscription)],
                __('Your subscription is already confirmed')
            );
        }

        $subscription->markAsActive();

        return $this->okResponse(
            ['subscription' => new NewsletterSubscriptionResource($subscription)],
            __('Subscription confirmed successfully')
        );
    }
}
