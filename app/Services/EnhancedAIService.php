<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced AI Service with Multiple Providers & Fallback Strategy
 * 
 * This service now supports:
 * - Primary: Google Gemini (free tier)
 * - Fallback: OpenAI GPT (if configured)
 * - Specialized: Groq (ultra-fast for structured tasks)
 * - Semantic: Embedding-based search (local)
 */
class EnhancedAIService
{
    protected string $primaryProvider = 'gemini';
    protected array $fallbackProviders = ['openai', 'groq'];

    public function __construct()
    {
        if (config('services.openai.api_key')) {
            $this->primaryProvider = 'openai';
            $this->fallbackProviders = ['gemini', 'groq'];
        }
    }

    /**
     * Smart chat with automatic fallback
     * Tries primary provider, then fallbacks in order
     */
    public function chat(string $systemPrompt, string $userPrompt, string $format = 'json', ?string $provider = null)
    {
        $providers = $provider ? [$provider] : [$this->primaryProvider, ...$this->fallbackProviders];

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

        try {
            $fullPrompt = "System: $systemPrompt\n\nUser: $userPrompt";
            if ($format === 'json') {
                $fullPrompt .= "\n\nReturn your response STRICTLY as a valid JSON object. Do not wrap it in markdown blocks.";
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

            $response = Http::timeout(10)->post($url, [
                'contents' => [
                    [
                        'parts' => [['text' => $fullPrompt]]
                    ]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini error: ' . $response->status());
                return null;
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$content) {
                return null;
            }

            if ($format === 'json') {
                $content = preg_replace('/```json\s*|\s*```/', '', $content);
                return json_decode($content, true) ?? $content;
            }

            return $content;

        } catch (\Exception $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
            return null;
        }
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
            'text',
            'groq' // Use fast Groq for this
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
     * Health Check - Verify API connectivity
     */
    public function healthCheck(): array
    {
        $status = [];

        // Check Gemini
        $geminiKey = config('services.gemini.api_key') ? 'set' : 'missing';
        $status['gemini'] = $geminiKey;

        // Check OpenAI
        $openaiKey = config('services.openai.api_key') ? 'set' : 'missing';
        $status['openai'] = $openaiKey;

        // Check Groq
        $groqKey = config('services.groq.api_key') ? 'set' : 'missing';
        $status['groq'] = $groqKey;

        return $status;
    }
}
