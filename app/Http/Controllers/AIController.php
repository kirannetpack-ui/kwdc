<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    // 🟢 HELPER FUNCTION TO FIND KEYWORDS
    private function routeByKeyword($text)
    {
        $text = strtolower($text);
        
        // CREATE ACTIONS
        if (str_contains($text, 'create') || str_contains($text, 'make') || str_contains($text, 'new')) {
            if (str_contains($text, 'pickup') || str_contains($text, 'pick up')) {
                return ['action' => 'redirect', 'url' => '/pickup/direct-create', 'message' => 'Opening Pickup page.'];
            }
            if (str_contains($text, 'dispatch') || str_contains($text, 'delivery')) {
                return ['action' => 'redirect', 'url' => '/dispatch/direct-create', 'message' => 'Opening Dispatch page.'];
            }
            if (str_contains($text, 'equipment') || str_contains($text, 'machine')) {
                return ['action' => 'redirect', 'url' => '/client/equipment/request', 'message' => 'Opening Equipment Request page.'];
            }
        }

        // TRACK ACTIONS
        if (str_contains($text, 'track') || str_contains($text, 'where') || str_contains($text, 'location')) {
            return ['action' => 'redirect', 'url' => '/dispatch', 'message' => 'Opening Dispatch list for tracking.'];
        }

        // ACCOUNT ACTIONS
        if (str_contains($text, 'logout') || str_contains($text, 'sign out')) {
            return ['action' => 'redirect', 'url' => '/logout', 'message' => 'Logging you out.'];
        }

        // INFO ACTIONS
        if (str_contains($text, 'price') || str_contains($text, 'cost') || str_contains($text, 'rate') || str_contains($text, 'rent')) {
            return [
                'action' => 'info',
                'message' => "KTM-WDC Rates (Subject to change):\n- Warehouses: Starting at रू 45/sq ft.\n- Dispatches: रू 45/km + Admin Margin.\n- Equipment: Daily rates vary based on model. Navigate to Equipment Requests to see specific prices."
            ];
        }

        return null; // No keyword matched, fallback to AI
    }

    public function handleVoice(Request $request)
    {
        $transcript = $request->command;
        $language = $request->input('language', 'English');

        // 🔥 1. CHECK KEYWORDS FIRST (0ms delay, no AI)
        $keywordRedirect = $this->routeByKeyword($transcript);
        if ($keywordRedirect) {
            return response()->json($keywordRedirect);
        }

        // 2. If no keyword found, fallback to AI
        $prompt = "The user said: '{$transcript}'. 
        Identify their intent. If they want to create something, redirect to pickup, dispatch, or equipment.
        If they ask generic questions (not about prices), give a helpful concise response.
        Return ONLY valid JSON. Action must be 'redirect' or 'info'.";

        $result = $this->aiService->chat(
            "You are a logistics expert. Return ONLY valid JSON.",
            $prompt,
            'json'
        );

        return response()->json($result ?? ['action' => 'info', 'message' => 'Sorry, I did not understand that. Please try again.']);
    }

    public function chatSupport(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'language' => 'required|string'
        ]);

        $text = $request->message;
        $language = $request->language;

        // 🔥 1. CHECK KEYWORDS FIRST (0ms delay, no AI)
        $keywordRedirect = $this->routeByKeyword($text);
        if ($keywordRedirect) {
            return response()->json($keywordRedirect);
        }

        // 2. Fallback to AI
        $prompt = "The user asked: '{$text}'. 
        Respond to them concisely. If they ask prices, give exact figures.
        Return ONLY valid JSON. Action must be 'chat' and message must be formatted with bullet points.";

        $jsonResponse = $this->aiService->chat(
            "You are a support agent. Return ONLY valid JSON.",
            $prompt,
            'json'
        );

        if (!$jsonResponse || !isset($jsonResponse['action'])) {
            return response()->json([
                'action' => 'chat',
                'message' => 'Sorry, I am currently offline. Please contact admin.'
            ]);
        }

        return response()->json($jsonResponse);
    }
}