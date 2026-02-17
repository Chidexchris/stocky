<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    /**
     * Handle the chat request.
     * POST /api/ai/chat
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array',
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'AI Service not configured (Missing API Key).',
            ], 500);
        }

        $userMessage = $request->message;
        $history = $request->history ?? [];

        // System Instruction / Knowledge Base
        $systemInstruction = "You are the dtrecord AI Assistant. You are friendly, professional, and helpful.\n";
        $systemInstruction .= "dtrecord is a SaaS platform for inventory management, sales tracking, and business automation.\n";
        $systemInstruction .= "Features include: Real-time stock tracking, PDF invoicing, daily reports, multi-store support, supplier management, and customer debt tracking.\n";
        $systemInstruction .= "Pricing Plans:\n";
        $systemInstruction .= "- Starter ($15/mo): 3 users, 2GB storage, 2 stores.\n";
        $systemInstruction .= "- Business ($39/mo): Everything in Starter, 15 users, 10 stores, supplier management, priority support.\n";
        $systemInstruction .= "- Enterprise ($79/mo): Everything in Business, 50 users, 20 stores, multi-location sync, 24/7 support.\n";
        $systemInstruction .= "Note: Prices shown are monthly. Annual billing offers 20% savings.\n";
        $systemInstruction .= "Always encourage users to try the 14-day free trial.\n";
        $systemInstruction .= "Attend to customer needs, answer their questions accurately based on this information, and be helpful.";

        // Prepare contents array for Gemini
        $contents = [];
        
        // Add chat history if available
        if ($history && is_array($history)) {
            foreach ($history as $msg) {
                $role = ($msg['role'] === 'assistant') ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $msg['text']]]
                ];
            }
        }

        // Add current user message with system instructions for the last part
        // Note: Gemini usually handles system instructions as a separate field 'system_instruction' 
        // in newer versions, but prepending to the first message or current message works too.
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "Follow these instructions: " . $systemInstruction . "\n\nUser Question: " . $userMessage]]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Failed to connect to AI service.';
                Log::error('Gemini API Error: ' . json_encode($errorData));
                
                return response()->json([
                    'success' => false,
                    'error' => "AI Error: " . $errorMessage,
                ], $response->status());
            }

            $data = $response->json();
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't process that.";

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);

        } catch (\Exception $e) {
            Log::error('AI Controller Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An internal error occurred.',
            ], 500);
        }
    }
}
