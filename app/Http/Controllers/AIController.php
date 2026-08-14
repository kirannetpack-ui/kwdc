<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Services\SmartAIService;
use App\Services\EnhancedAIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;
    protected $smartAIService;

    public function __construct(AIService $aiService, SmartAIService $smartAIService)
    {
        $this->aiService = $aiService;
        $this->smartAIService = $smartAIService;
    }

    public function handleVoice(Request $request)
    {
        $transcript = $request->command;
        $language = $request->input('language', 'English');
        $userId = auth()->id();

        // Use comprehensive SmartAIService for intelligent voice processing
        $response = $this->smartAIService->processQuery($transcript, $userId);

        return response()->json($response);
    }

    public function chatSupport(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'language' => 'required|string'
        ]);

        $text = $request->message;
        $language = $request->language;
        $userId = auth()->id();

        // Use comprehensive SmartAIService for comprehensive AI assistance
        $response = $this->smartAIService->processQuery($text, $userId);

        return response()->json($response);
    }
}