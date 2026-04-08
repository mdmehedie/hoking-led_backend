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
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:newsletter_subscriptions,email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'consent_given' => 'accepted',
            'source' => ['nullable', 'string', Rule::in(['website', 'footer', 'popup', 'checkout', 'landing_page', 'import'])],
            'preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                ['errors' => $validator->errors()],
                __('Validation failed'),
                422
            );
        }

        $subscription = NewsletterSubscription::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'source' => $request->source ?? 'website',
            'status' => 'pending',
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
