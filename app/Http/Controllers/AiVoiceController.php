<?php

namespace App\Http\Controllers;

use App\Services\VoiceAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiVoiceController extends Controller
{
    protected $assistant;

    public function __construct(VoiceAssistantService $assistant)
    {
        $this->assistant = $assistant;
    }

   public function voiceAssistant(Request $request)
{
    $request->validate([
        'message' => ['required', 'string', 'max:4000'],
        'language' => ['nullable', 'in:en,np'],
    ]);
    try {
        $user = Auth::user();
        $message = $request->input('message');
        $language = $request->input('language', 'en');

        Log::info('🎤 Voice request received', ['user' => $user->id, 'message' => $message, 'lang' => $language]);

        $result = $this->assistant->process($user->id, $message, $language);

        return response()->json($result);
    } catch (\Exception $e) {
        Log::error('❌ Voice controller error: ' . $e->getMessage());
        Log::error('❌ Controller trace: ' . $e->getTraceAsString());
        return response()->json([
            'message' => $request->input('language') === 'np' 
                ? 'क्षमा गर्नुहोस्, मैले एउटा त्रुटि भेटाएँ। कृपया फेरि प्रयास गर्नुहोस्।' 
                : 'Sorry, I encountered an error. Please try again.',
            'done' => false,
        ], 500);
    }
}

}
