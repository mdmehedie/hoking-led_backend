<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CountryHelper;
use App\Http\Controllers\Api\ApiBaseController;
use App\Mail\AdminContactNotification;
use App\Mail\UserContactAutoReply;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApiFrontendContactController extends ApiBaseController
{
    protected array $disposableDomains = [
        'mailinator.com', 'tempmail.com', 'throwaway.email',
        'guerrillamail.com', 'sharklasers.com', 'yopmail.com',
    ];

    protected array $validSources = [
        'contact_page', 'footer', 'popup', 'support_page',
        'product_page', 'api', 'news_page', 'blog_page',
    ];

    protected array $validResourceTypes = [
        'product', 'blog', 'news', 'project',
        'page', 'case_study', 'brand', 'category',
    ];

    public function submit(Request $request): JsonResponse
    {
        // Bot detection: too fast submission
        if ($request->has('_token_time') && (time() - (int) $request->_token_time) < 1) {
            return $this->errorResponse(
                ['error' => __('Bot activity detected')],
                403
            );
        }

        // Honeypot check
        if ($request->has('_website') && !empty($request->_website)) {
            return $this->createdResponse(
                ['submission' => ['id' => null]],
                __('Thank you for your message. We will get back to you soon.')
            );
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:10',
            'place' => 'nullable|string|max:255', // Company Name
            'subject' => 'required|string|max:500',
            'message' => 'required|string|min:10|max:5000',
            'source' => ['nullable', 'string', Rule::in($this->validSources)],
            'extras' => 'nullable|array',
            'extras.resource_type' => ['nullable', 'string', Rule::in($this->validResourceTypes)],
            'extras.resource_id' => 'nullable|integer',
            'extras.public_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableResponse($validator->errors()->toArray(), __($validator->errors()->first()));
        }

        // Block disposable email domains
        $emailDomain = strtolower(substr(strrchr($request->email, '@'), 1));
        if (in_array($emailDomain, $this->disposableDomains)) {
            return $this->unprocessableResponse(
                ['email' => __('Disposable email addresses are not allowed')],
                __('Disposable email addresses are not allowed')
            );
        }

        // Sanitize phone and concatenate with country code
        $finalPhone = $request->phone;
        $countryName = null;

        if ($request->filled('country_code')) {
            $countryCode = $request->country_code;
            if (!str_starts_with($countryCode, '+')) {
                $countryCode = '+' . $countryCode;
            }
            
            $cleanPhone = $request->filled('phone') ? preg_replace('/[^0-9]/', '', $request->phone) : '';
            $finalPhone = $countryCode . $cleanPhone;

            // Detect country from dial code
            $countryName = CountryHelper::getCountryByDialCode($countryCode);
        }

        // Strip HTML from message (prevent XSS)
        $cleanMessage = strip_tags($request->message);

        $submission = ContactSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $finalPhone,
            'country' => $countryName,
            'place' => $request->place, // Company name
            'subject' => $request->subject,
            'message' => $cleanMessage,
            'source' => $request->source ?? 'contact_page',
            'status' => 'new',
            'priority' => 'medium',
            'ip_address' => $request->ip(),
            'extras' => $request->extras,
            'auto_delete_at' => now()->addMonths(12),
        ]);

        // Send email notifications (queued) - only if mail server is configured
        $adminEmail = config('mail.from.address');
        $mailHost = config('mail.mailers.' . config('mail.default') . '.host');
        if ($adminEmail && $mailHost && $mailHost !== '127.0.0.1') {
            try {
                Mail::to($adminEmail)->queue(new AdminContactNotification($submission));
                Mail::to($submission->email)->queue(new UserContactAutoReply($submission));
            } catch (\Exception $e) {
                \Log::warning('Failed to queue contact form emails: ' . $e->getMessage());
            }
        }

        return $this->createdResponse(
            ['submission' => ['id' => $submission->id]],
            __('Thank you for your message. We will get back to you soon.')
        );
    }
}
