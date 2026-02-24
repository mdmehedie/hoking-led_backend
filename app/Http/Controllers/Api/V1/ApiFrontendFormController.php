<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Form;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApiFrontendFormController extends ApiBaseController
{
    /**
     * Get all forms list for frontend
     */
    public function index(Request $request): JsonResponse
    {
        $forms = Form::where('email_notifications', true)
            ->orWhere('store_leads', true)
            ->get(['id', 'name', 'fields', 'success_message']);

        return $this->okResponse(['forms' => $forms], __('Forms retrieved successfully'));
    }

    /**
     * Handle form submission
     */
    public function store(Request $request, $formId): JsonResponse
    {
        $form = Form::findOrFail($formId);

        $data = $request->all();

        // Store lead if enabled
        if ($form->store_leads) {
            Lead::create([
                'form_id' => $form->id,
                'data' => $data,
            ]);
        }

        // Send email notifications if enabled
        if ($form->email_notifications && $form->notification_emails) {
            $emails = $form->notification_emails;
            $subject = 'New Form Submission: ' . $form->name;
            $body = 'New submission for form "' . $form->name . '":' . "\n\n" . json_encode($data, JSON_PRETTY_PRINT);

            Mail::raw($body, function ($message) use ($emails, $subject) {
                $message->to($emails)->subject($subject);
            });
        }

        return $this->okResponse([
            'message' => $form->success_message ?: 'Form submitted successfully',
        ], __('Form submitted successfully'));
    }
}
