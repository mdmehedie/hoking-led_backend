<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFrontendConversationController extends ApiBaseController
{
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:5000',
        ]);

        $conversation = Conversation::create([
            'visitor_name' => $validated['name'],
            'visitor_email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'status' => 'new',
            'priority' => 'medium',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if (!empty($validated['message'])) {
            $conversation->messages()->create([
                'sender_type' => 'visitor',
                'message' => $validated['message'],
            ]);
            $conversation->update(['last_visitor_message_at' => now()]);
        }

        $conversation->notifyAdminsAboutNewConversation();

        return $this->createdResponse([
            'session_id' => $conversation->session_id,
            'message' => 'Conversation started. Use this session_id to send and receive messages.',
        ], 'Conversation started successfully');
    }

    public function messages(Request $request, string $sessionId): JsonResponse
    {
        $conversation = Conversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            return $this->notFoundResponse('Conversation not found');
        }

        $messages = $conversation->messages()
            ->visibleToVisitor()
            ->when($request->get('after'), function ($query, $after) {
                $query->where('created_at', '>', $after);
            })
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'message' => $m->message,
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        $conversation->markAdminRead();

        return $this->okResponse([
            'session_id' => $conversation->session_id,
            'status' => $conversation->status,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, string $sessionId): JsonResponse
    {
        $conversation = Conversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            return $this->notFoundResponse('Conversation not found');
        }

        if ($conversation->status === 'closed') {
            return $this->badRequestResponse([], 'This conversation has been closed.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = $conversation->visitorReply($validated['message']);


        return $this->okResponse([
            'session_id' => $conversation->session_id,
            'status' => $conversation->status,
            'message' => $message,
        ]);
    }
}
