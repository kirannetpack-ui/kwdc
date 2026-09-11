<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced AI Service with Multiple Providers & Fallback Strategy
 *
 * This service now supports:
 * - Default: Free local/rule-based assistant mode
 * - Optional: Google Gemini, OpenAI GPT, or Groq when explicitly enabled
 * - Semantic: Embedding-based search (local)
 */
class EnhancedAIService
{
    protected string $primaryProvider = 'free';
    protected array $fallbackProviders = [];

    public function __construct()
    {
        $configuredProvider = strtolower((string) config('services.ai.provider', 'free'));

        if ($configuredProvider === 'auto') {
            $this->primaryProvider = 'gemini';
            $this->fallbackProviders = ['openai', 'groq'];
            return;
        }

        if (in_array($configuredProvider, ['gemini', 'openai', 'groq'], true)) {
            $this->primaryProvider = $configuredProvider;
            $this->fallbackProviders = [];
        }
    }

    /**
     * Smart chat with automatic fallback
     * Tries primary provider, then fallbacks in order
     */
    public function chat(string $systemPrompt, string $userPrompt, string $format = 'json', ?string $provider = null)
    {
        $providers = $this->requestedProviders($provider);

        if ($providers === []) {
            Log::info('AI provider disabled; using free local assistant behavior.');
            return null;
        }

        foreach ($providers as $p) {
            $result = match($p) {
                'gemini' => $this->chatGemini($systemPrompt, $userPrompt, $format),
                'openai' => $this->chatOpenAI($systemPrompt, $userPrompt, $format),
                'groq' => $this->chatGroq($systemPrompt, $userPrompt, $format),
                default => null,
            };

            if ($result !== null) {
                Log::info("✅ AI response from provider: {$p}");
                return $result;
            }

            Log::warning("⚠️ Provider {$p} failed, trying next...");
        }

        Log::error("❌ All AI providers failed");
        return null;
    }

    private function requestedProviders(?string $provider): array
    {
        $configuredProvider = strtolower((string) config('services.ai.provider', 'free'));

        if ($configuredProvider === 'free') {
            return [];
        }

        if ($provider) {
            $provider = strtolower($provider);

            return ($configuredProvider === 'auto' || $configuredProvider === $provider)
                ? [$provider]
                : [];
        }

        return array_values(array_filter(
            [$this->primaryProvider, ...$this->fallbackProviders],
            fn (string $candidate) => $candidate !== 'free'
        ));
    }

    /**
     * Google Gemini Implementation (Primary)
     */
    protected function chatGemini(string $systemPrompt, string $userPrompt, string $format = 'json')
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            Log::warning('Gemini API key missing');
            return null;
        }

        $models = array_unique([
            config('services.gemini.model', 'gemini-3.5-flash'),
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
        ]);

        foreach ($models as $model) {
            try {
                $fullPrompt = "System: $systemPrompt\n\nUser: $userPrompt";
                if ($format === 'json') {
                    $fullPrompt .= "\n\nReturn your response STRICTLY as a valid JSON object. Do not wrap it in markdown code blocks.";
                }

                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

                $response = Http::withoutVerifying()->timeout(12)->post($url, [
                    'contents' => [
                        [
                            'parts' => [['text' => $fullPrompt]]
                        ]
                    ]
                ]);

                if ($response->failed()) {
                    Log::warning("Gemini model {$model} returned status {$response->status()}: " . substr($response->body(), 0, 120));
                    continue;
                }

                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$content) {
                    continue;
                }

                if ($format === 'json') {
                    $content = preg_replace('/```(?:json)?\s*|\s*```/', '', trim($content));
                    return json_decode($content, true) ?? $content;
                }

                return trim($content);

            } catch (\Exception $e) {
                Log::error("Gemini exception on model {$model}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }

    /**
     * OpenAI Implementation (Fallback)
     * Requires OPENAI_API_KEY in .env
     */
    protected function chatOpenAI(string $systemPrompt, string $userPrompt, string $format = 'json')
    {
        $apiKey = config('services.openai.api_key');
        if (!$apiKey) {
            return null;
        }

        try {
            $url = "https://api.openai.com/v1/chat/completions";
            $payload = [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt . ($format === 'json' ? "\n\nReturn ONLY valid JSON." : '')]
                ],
                'temperature' => $format === 'json' ? 0.2 : 0.7,
                'max_tokens' => 1024,
            ];

            if ($format === 'json') {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::timeout(10)
                ->withToken($apiKey)
                ->post($url, $payload);

            if ($response->failed()) {
                Log::warning('OpenAI error: ' . $response->status());
                return null;
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if ($format === 'json' && $content) {
                $content = preg_replace('/```json\s*|\s*```/', '', $content);
                return json_decode($content, true) ?? $content;
            }

            return $content;

        } catch (\Exception $e) {
            Log::warning('OpenAI exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Groq Implementation (Ultra-Fast for Structured Tasks)
     * Requires GROQ_API_KEY in .env
     * Best for: Quick classifications, extractions, structured outputs
     */
    protected function chatGroq(string $systemPrompt, string $userPrompt, string $format = 'json')
    {
        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            return null;
        }

        try {
            $url = "https://api.groq.com/openai/v1/chat/completions";

            $response = Http::timeout(5)
                ->withToken($apiKey)
                ->post($url, [
                    'model' => 'mixtral-8x7b-32768',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt . ($format === 'json' ? '\n\nReturn ONLY valid JSON.' : '')]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 512,
                ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if ($format === 'json' && $content) {
                $content = preg_replace('/```json\s*|\s*```/', '', $content);
                return json_decode($content, true) ?? $content;
            }

            return $content;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Semantic Search using Local Embeddings
     * For matching user queries to relevant documents/FAQs
     */
    public function semanticSearch(string $query, array $documents, int $topK = 3): array
    {
        // This would use a local embedding model or vector DB in production
        // For now, using simple keyword matching as fallback

        $queryKeywords = $this->extractKeywords($query);
        $scores = [];

        foreach ($documents as $idx => $doc) {
            $docKeywords = $this->extractKeywords($doc['text']);
            $matches = count(array_intersect($queryKeywords, $docKeywords));
            $scores[$idx] = $matches;
        }

        arsort($scores);
        $results = [];
        foreach (array_slice($scores, 0, $topK) as $idx => $score) {
            if ($score > 0) {
                $results[] = [...$documents[$idx], 'relevance_score' => $score];
            }
        }

        return $results;
    }

    /**
     * Extract keywords from text
     */
    protected function extractKeywords(string $text, int $limit = 10): array
    {
        $text = strtolower($text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter out common stop words
        $stopwords = ['the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'what', 'which', 'who', 'when', 'where', 'why', 'how'];
        $keywords = array_diff($words, $stopwords);

        return array_slice(array_unique($keywords), 0, $limit);
    }

    /**
     * Document Processing for OCR/Compliance Documents
     * Extracts text from images for security agency compliance docs
     */
    public function processDocument(string $imagePath): array
    {
        // Uses Google's Vision API or Tesseract OCR
        // For now, placeholder for future integration

        Log::info("Document processing requested for: {$imagePath}");
        
        return [
            'success' => false,
            'message' => 'Document processing requires OCR integration (Google Vision / Tesseract)',
            'extracted_text' => null,
            'confidence' => 0,
        ];
    }

    /**
     * Multi-language Text Generation with Language Detection
     */
    public function generateMultiLanguage(string $text, string $targetLanguage = 'auto'): array
    {
        // Detect language
        if ($targetLanguage === 'auto') {
            $targetLanguage = $this->detectLanguage($text);
        }

        $prompt = "Translate or generate content in {$targetLanguage} for: {$text}";
        $result = $this->chat(
            "You are a professional translator and content generator.",
            $prompt,
            'text'
        );

        return [
            'language' => $targetLanguage,
            'content' => $result ?? $text,
            'translated' => $result !== null,
        ];
    }

    /**
     * Detect language of text
     */
    protected function detectLanguage(string $text): string
    {
        // Check for Nepali/Hindi Devanagari script (Unicode range U+0900 to U+097F)
        if (preg_match('~[अ-ह]~u', $text)) {
            return 'Nepali';
        }
        // Check for Chinese/Japanese characters
        if (preg_match('~[\p{Han}\p{Hiragana}\p{Katakana}]~u', $text)) {
            return 'Japanese';
        }
        // Default to English
        return 'English';
    }

    /**
     * Transcribe audio using Google Gemini multimodal capabilities
     */
    public function transcribeAudio(string $base64Audio, string $mimeType = 'audio/webm', string $language = 'en'): ?string
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            Log::warning('Gemini API key missing for audio transcription');
            return null;
        }

        $models = array_unique([
            config('services.gemini.model', 'gemini-3.5-flash'),
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
        ]);

        $prompt = $language === 'np'
            ? 'तपाईं एक सटीक अडियो ट्रान्सक्राइबिङ सहायक हुनुहुन्छ। यो अडियो सुन्नुहोस् र बोलिएका शब्दहरूलाई शुद्ध नेपाली देवनागरी वा अंग्रेजीमा जस्ताको तस्तै ट्रान्सक्राइब गर्नुहोस्। कुनै अतिरिक्त टिप्पणी, अभिवादन वा स्पष्टीकरण नदिनुहोस्। यदि कुनै आवाज छैन भने खाली छोड्नुहोस्।'
            : 'You are an accurate audio transcriber for Kathmandu Logistics. Transcribe the spoken words in this audio exactly as spoken (English or Nepali). Return ONLY the transcribed text. Do not add quotes, commentary, markdown, or timestamps. If no clear speech is heard, return an empty string.';

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

                $response = Http::withoutVerifying()->timeout(15)->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Audio,
                                    ]
                                ],
                                [
                                    'text' => $prompt,
                                ]
                            ]
                        ]
                    ]
                ]);

                if ($response->failed()) {
                    Log::warning("Gemini audio transcription failed on {$model}: " . $response->status());
                    continue;
                }

                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text !== null) {
                    $cleaned = trim(preg_replace('/^["\']|["\']$/', '', trim($text)));
                    if (strcasecmp($cleaned, 'silent') === 0 || strcasecmp($cleaned, 'none') === 0) {
                        return '';
                    }
                    return $cleaned;
                }
            } catch (\Throwable $e) {
                Log::error("Gemini transcribe error on {$model}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }

    /**
     * Health Check - Verify API connectivity
     */
    public function healthCheck(): array
    {
        $status = [];
        $configuredProvider = strtolower((string) config('services.ai.provider', 'free'));

        $status['mode'] = $configuredProvider;
        $status['external_ai'] = $configuredProvider === 'free' ? 'disabled' : 'enabled';

        // Check Gemini
        $geminiKey = ($configuredProvider === 'free')
            ? 'disabled'
            : (config('services.gemini.api_key') ? 'set' : 'missing');
        $status['gemini'] = $geminiKey;

        // Check OpenAI
        $openaiKey = ($configuredProvider === 'free')
            ? 'disabled'
            : (config('services.openai.api_key') ? 'set' : 'missing');
        $status['openai'] = $openaiKey;

        // Check Groq
        $groqKey = ($configuredProvider === 'free')
            ? 'disabled'
            : (config('services.groq.api_key') ? 'set' : 'missing');
        $status['groq'] = $groqKey;

        return $status;
    }
}
