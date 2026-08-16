<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AssistantActionPlanner
{
    public function __construct(private EnhancedAIService $aiService)
    {
    }

    public function plan(string $query, ?int $userId = null, ?string $role = null): array
    {
        $query = trim($query);

        if ($query === '') {
            return $this->unknown();
        }

        $distanceAnswer = $this->planDistanceAnswer($query);
        if ($distanceAnswer) {
            return $distanceAnswer;
        }

        $aiPlan = $this->planWithAi($query, $role);
        if ($this->isUsablePlan($aiPlan)) {
            return $this->normalizePlan($aiPlan);
        }

        return $this->planWithRules($query);
    }

    private function planWithAi(string $query, ?string $role): ?array
    {
        if (App::environment('testing') || !config('services.openai.api_key')) {
            return null;
        }

        $system = <<<'PROMPT'
You are the KWDC logistics assistant planner. Convert the user request into one safe app action.
Never include secrets. Never submit final paid/irreversible actions. Prefer opening and prefilling a form.
Return JSON with keys: intent, action, url, data, message, confidence.
Allowed intents: pickup_request, dispatch_request, reminder_create, tracking, invoice_lookup, warehouse_rental, equipment_rental, security_booking, distance_answer, general_help.
Allowed actions: open_page, guidance, answer.
Use these urls: /pickup/direct-create, /dispatch/direct-create, /reminders, /dispatch, /client/invoices, /my-requests, /equipment-requests/create, /security/dashboard.
For pickup/dispatch data, use: pickup_address, delivery_address, pickup_contact_person, pickup_contact_phone, recipient_name, recipient_phone, total_distance, total_price, vehicle_type, items_description, scheduled_date, scheduled_time.
For reminders, use: title, starts_at, remind_at, notes, using datetime-local format YYYY-MM-DDTHH:MM when possible.
PROMPT;

        $user = json_encode([
            'role' => $role,
            'today' => now()->format('Y-m-d'),
            'message' => $query,
        ], JSON_UNESCAPED_SLASHES);

        try {
            $result = $this->aiService->chat($system, $user, 'json', 'openai');
            return is_array($result) ? $result : null;
        } catch (\Throwable $e) {
            Log::warning('Assistant planner AI failed: ' . $e->getMessage());
            return null;
        }
    }

    private function planWithRules(string $query): array
    {
        $lower = strtolower($query);
        $data = $this->extractData($query);

        if (preg_match('/\b(remind|reminder|calendar|schedule)\b/', $lower)) {
            $reminder = $this->extractReminderData($query);

            return [
                'intent' => 'reminder_create',
                'action' => 'open_page',
                'url' => '/reminders',
                'data' => $reminder,
                'message' => 'Opening the reminder calendar with the reminder details filled in.',
                'confidence' => 0.78,
            ];
        }

        if (preg_match('/\b(track|tracking|where is|status|progress)\b/', $lower)) {
            return [
                'intent' => 'tracking',
                'action' => 'open_page',
                'url' => '/dispatch',
                'data' => $data,
                'message' => 'Opening your dispatch tracking page.',
                'confidence' => 0.8,
            ];
        }

        if (preg_match('/\b(invoice|invoices|payment|unpaid|paid|bill)\b/', $lower)) {
            return [
                'intent' => 'invoice_lookup',
                'action' => 'open_page',
                'url' => '/client/invoices',
                'data' => [],
                'message' => 'Opening your invoices.',
                'confidence' => 0.75,
            ];
        }

        if (preg_match('/\b(equipment|jcb|forklift|crane|loader|excavator|machine)\b/', $lower)) {
            return [
                'intent' => 'equipment_rental',
                'action' => 'open_page',
                'url' => '/equipment-requests/create',
                'data' => $data,
                'message' => 'Opening equipment requests with the details I could understand.',
                'confidence' => 0.72,
            ];
        }

        if (preg_match('/\b(security|guard|agency|suraksha)\b/', $lower)) {
            return [
                'intent' => 'security_booking',
                'action' => 'open_page',
                'url' => '/security/dashboard',
                'data' => $data,
                'message' => 'Opening security booking options.',
                'confidence' => 0.72,
            ];
        }

        if (preg_match('/\b(warehouse|storage|godown|store space)\b/', $lower)) {
            return [
                'intent' => 'warehouse_rental',
                'action' => 'open_page',
                'url' => '/my-requests',
                'data' => $data,
                'message' => 'Opening warehouse requests.',
                'confidence' => 0.72,
            ];
        }

        if (preg_match('/\b(dispatch|deliver|delivery|shipment|send|transport)\b/', $lower)) {
            return [
                'intent' => 'dispatch_request',
                'action' => 'open_page',
                'url' => '/dispatch/direct-create',
                'data' => $data,
                'message' => $this->routeMessage('dispatch', $data),
                'confidence' => 0.82,
            ];
        }

        if (preg_match('/\b(pickup|pick up|collect|collection|fetch)\b/', $lower)) {
            return [
                'intent' => 'pickup_request',
                'action' => 'open_page',
                'url' => '/pickup/direct-create',
                'data' => $data,
                'message' => $this->routeMessage('pickup', $data),
                'confidence' => 0.82,
            ];
        }

        return $this->unknown();
    }

    private function extractData(string $query): array
    {
        $data = [];
        $clean = trim(preg_replace('/\s+/', ' ', $query));

        if (preg_match('/(?:pickup\s+from|pick\s+up\s+from|from)\s+(.+?)\s+(?:to|drop(?:\s+to)?|deliver(?:\s+to)?)\s+(.+?)(?=\s+(?:with|for|at|tomorrow|today|on|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['pickup_address'] = $this->cleanValue($matches[1]);
            $data['delivery_address'] = $this->cleanValue($matches[2]);
        } elseif (preg_match('/(?:pickup|pick up|from)\s+(.+?)(?=\s+(?:with|for|at|tomorrow|today|on|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['pickup_address'] = $this->cleanValue($matches[1]);
        }

        if (!isset($data['delivery_address']) && preg_match('/(?:to|drop(?:\s+to)?|deliver(?:\s+to)?)\s+(.+?)(?=\s+(?:with|for|at|tomorrow|today|on|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['delivery_address'] = $this->cleanValue($matches[1]);
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:km|kilometer|kilometers)\b/i', $clean, $matches)) {
            $data['total_distance'] = $matches[1];
        }

        if (preg_match('/(?:rs|npr|रु|रू|amount|price|cost|total)\s*\.?\s*(\d+(?:,\d+)*(?:\.\d+)?)/i', $clean, $matches)) {
            $data['total_price'] = str_replace(',', '', $matches[1]);
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:kg|kgs|kilogram|kilograms|ton|tons)\b/i', $clean, $matches)) {
            $data['items_description'] = trim(($data['items_description'] ?? '') . ' Weight: ' . $matches[0]);
        }

        if (preg_match('/(\d+)\s*(?:box|boxes|carton|cartons|package|packages)\b/i', $clean, $matches)) {
            $data['items_description'] = trim(($data['items_description'] ?? '') . ' Quantity: ' . $matches[0]);
        }

        if (preg_match('/\b(mini truck|heavy truck|truck|van|bike|motorcycle|two-wheeler|refrigerated)\b/i', $clean, $matches)) {
            $data['vehicle_type'] = ucwords(strtolower($matches[1]));
        }

        if (preg_match('/(?:call|phone|contact)\s+([A-Za-z][A-Za-z\s.]+?)(?=\s+(?:at|on|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['recipient_name'] = $this->cleanValue($matches[1]);
        }

        if (preg_match('/(?:put|set|make|change|fill|add)\s+(?:the\s+)?(?:pickup\s+)?contact(?:\s+person)?(?:\s+name)?\s+(?:as|to|=)\s+(.+?)(?=\s+(?:on|for|in|at|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['pickup_contact_person'] = $this->cleanValue($matches[1]);
        } elseif (preg_match('/(?:contact\s+person|pickup\s+contact|contact\s+name)\s+(?:is|as|to|=)\s+(.+?)(?=\s+(?:on|for|in|at|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['pickup_contact_person'] = $this->cleanValue($matches[1]);
        }

        if (preg_match('/(?:put|set|make|change|fill|add)\s+(?:the\s+)?(?:recipient\s+)?(?:name)\s+(?:as|to|=)\s+(.+?)(?=\s+(?:on|for|in|at|,|\.|$)|,|\.|$)/i', $clean, $matches)) {
            $data['recipient_name'] = $this->cleanValue($matches[1]);
        }

        if (preg_match('/(?:\+977[-\s]?)?(9[78]\d{8})\b/', $clean, $matches)) {
            $data['recipient_phone'] = $matches[1];
            $data['pickup_contact_phone'] = $matches[1];
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }

    private function planDistanceAnswer(string $query): ?array
    {
        $clean = trim(preg_replace('/\s+/', ' ', $query));
        $patterns = [
            '/\bhow\s+far\s+is\s+(.+?)\s+(?:to|from)\s+(.+?)(?:\?|$)/i',
            '/\bdistance\s+(?:from\s+)?(.+?)\s+to\s+(.+?)(?:\?|$)/i',
        ];

        foreach ($patterns as $pattern) {
            if (!preg_match($pattern, $clean, $matches)) {
                continue;
            }

            $fromName = $this->cleanPlaceName($matches[1]);
            $toName = $this->cleanPlaceName($matches[2]);
            $from = $this->knownPlace($fromName);
            $to = $this->knownPlace($toName);

            if (!$from || !$to) {
                return [
                    'intent' => 'distance_answer',
                    'action' => 'answer',
                    'url' => '#',
                    'data' => [
                        'from' => $fromName,
                        'to' => $toName,
                    ],
                    'message' => "I do not have enough local map data for {$fromName} to {$toName} yet. Try nearby known places like Koteshwor, Bhaktapur, Boudha, Thamel, Kalanki, Kathmandu, Patan, Pokhara, or Birgunj.",
                    'confidence' => 0.45,
                ];
            }

            $straightKm = $this->haversineKm($from['lat'], $from['lng'], $to['lat'], $to['lng']);
            $roadKm = max($straightKm, $straightKm * 1.25);
            $roundedKm = max(1, round($roadKm));
            $minMinutes = max(8, (int) round(($roadKm / 28) * 60));
            $maxMinutes = max($minMinutes + 8, (int) round(($roadKm / 18) * 60));

            return [
                'intent' => 'distance_answer',
                'action' => 'answer',
                'url' => '#',
                'data' => [
                    'from' => $from['name'],
                    'to' => $to['name'],
                    'estimated_distance_km' => $roundedKm,
                    'estimated_time_minutes' => "{$minMinutes}-{$maxMinutes}",
                ],
                'message' => "{$from['name']} to {$to['name']} is roughly {$roundedKm} km by road, usually about {$minMinutes}-{$maxMinutes} minutes depending on traffic.",
                'confidence' => 0.78,
            ];
        }

        return null;
    }

    private function cleanPlaceName(string $value): string
    {
        $value = preg_replace('/\b(distance|far|km|kilometer|kilometers|road|by\s+road|from|to|is|the)\b/i', ' ', $value);

        return ucwords($this->cleanValue($value));
    }

    private function knownPlace(string $name): ?array
    {
        $key = strtolower(preg_replace('/[^a-z0-9]+/i', '', $name));
        $places = [
            'koteshwor' => ['name' => 'Koteshwor', 'lat' => 27.6785, 'lng' => 85.3491],
            'koteswor' => ['name' => 'Koteshwor', 'lat' => 27.6785, 'lng' => 85.3491],
            'koteshor' => ['name' => 'Koteshwor', 'lat' => 27.6785, 'lng' => 85.3491],
            'koteswar' => ['name' => 'Koteshwor', 'lat' => 27.6785, 'lng' => 85.3491],
            'ateshor' => ['name' => 'Koteshwor', 'lat' => 27.6785, 'lng' => 85.3491],
            'ateshwar' => ['name' => 'Koteshwor', 'lat' => 27.6785, 'lng' => 85.3491],
            'bhaktapur' => ['name' => 'Bhaktapur', 'lat' => 27.6710, 'lng' => 85.4298],
            'bhatapur' => ['name' => 'Bhaktapur', 'lat' => 27.6710, 'lng' => 85.4298],
            'bhaktpur' => ['name' => 'Bhaktapur', 'lat' => 27.6710, 'lng' => 85.4298],
            'boudha' => ['name' => 'Boudha', 'lat' => 27.7215, 'lng' => 85.3620],
            'boudhanath' => ['name' => 'Boudha', 'lat' => 27.7215, 'lng' => 85.3620],
            'thamel' => ['name' => 'Thamel', 'lat' => 27.7154, 'lng' => 85.3123],
            'kalanki' => ['name' => 'Kalanki', 'lat' => 27.6932, 'lng' => 85.2816],
            'baneshwor' => ['name' => 'Baneshwor', 'lat' => 27.6889, 'lng' => 85.3358],
            'newbaneshwor' => ['name' => 'New Baneshwor', 'lat' => 27.6889, 'lng' => 85.3358],
            'patan' => ['name' => 'Patan', 'lat' => 27.6766, 'lng' => 85.3188],
            'lalitpur' => ['name' => 'Lalitpur', 'lat' => 27.6766, 'lng' => 85.3188],
            'kathmandu' => ['name' => 'Kathmandu', 'lat' => 27.7172, 'lng' => 85.3240],
            'pokhara' => ['name' => 'Pokhara', 'lat' => 28.2096, 'lng' => 83.9856],
            'birgunj' => ['name' => 'Birgunj', 'lat' => 27.0104, 'lng' => 84.8774],
        ];

        return $places[$key] ?? null;
    }

    private function haversineKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadiusKm = 6371;
        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * sin($lngDelta / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function extractReminderData(string $query): array
    {
        $title = preg_replace('/\b(remind me to|remind|reminder|calendar|schedule)\b/i', '', $query);
        $title = preg_replace('/\b(today|tomorrow|at\s+\d{1,2}(?::\d{2})?\s*(?:am|pm)?|on\s+\d{4}-\d{2}-\d{2})\b/i', '', $title);
        $title = $this->cleanValue($title) ?: 'Reminder';
        $startsAt = $this->extractDateTime($query) ?? now()->addDay()->setTime(9, 0);

        return [
            'title' => ucfirst($title),
            'starts_at' => $startsAt->format('Y-m-d\TH:i'),
            'remind_at' => $startsAt->copy()->subHour()->format('Y-m-d\TH:i'),
            'notes' => trim($query),
        ];
    }

    private function extractDateTime(string $query): ?Carbon
    {
        $base = now();

        if (preg_match('/\btomorrow\b/i', $query)) {
            $base = now()->addDay();
        } elseif (preg_match('/\btoday\b/i', $query)) {
            $base = now();
        } elseif (preg_match('/\bon\s+(\d{4}-\d{2}-\d{2})\b/i', $query, $matches)) {
            $base = Carbon::parse($matches[1]);
        }

        if (preg_match('/\bat\s+(\d{1,2})(?::(\d{2}))?\s*(am|pm)?\b/i', $query, $matches)) {
            $hour = (int) $matches[1];
            $minute = isset($matches[2]) ? (int) $matches[2] : 0;
            $period = strtolower($matches[3] ?? '');

            if ($period === 'pm' && $hour < 12) {
                $hour += 12;
            } elseif ($period === 'am' && $hour === 12) {
                $hour = 0;
            }

            return $base->copy()->setTime($hour, $minute);
        }

        return preg_match('/\btomorrow|today|on\s+\d{4}-\d{2}-\d{2}\b/i', $query)
            ? $base->copy()->setTime(9, 0)
            : null;
    }

    private function normalizePlan(array $plan): array
    {
        $intent = $plan['intent'] ?? 'general_help';
        $url = $plan['url'] ?? $this->urlForIntent($intent);
        $action = in_array($plan['action'] ?? null, ['open_page', 'guidance', 'answer'], true) ? $plan['action'] : 'guidance';

        return [
            'intent' => $intent,
            'action' => $action,
            'url' => $url,
            'data' => is_array($plan['data'] ?? null) ? array_filter($plan['data']) : [],
            'message' => $plan['message'] ?? 'I prepared the next step for you.',
            'confidence' => (float) ($plan['confidence'] ?? 0.7),
        ];
    }

    private function isUsablePlan(?array $plan): bool
    {
        if (!$plan) {
            return false;
        }

        return in_array($plan['intent'] ?? null, [
            'pickup_request',
            'dispatch_request',
            'reminder_create',
            'tracking',
            'invoice_lookup',
            'warehouse_rental',
            'equipment_rental',
            'security_booking',
            'distance_answer',
            'general_help',
        ], true);
    }

    private function urlForIntent(string $intent): string
    {
        return match ($intent) {
            'pickup_request' => '/pickup/direct-create',
            'dispatch_request' => '/dispatch/direct-create',
            'reminder_create' => '/reminders',
            'tracking' => '/dispatch',
            'invoice_lookup' => '/client/invoices',
            'warehouse_rental' => '/my-requests',
            'equipment_rental' => '/equipment-requests/create',
            'security_booking' => '/security/dashboard',
            default => '#',
        };
    }

    private function routeMessage(string $type, array $data): string
    {
        if (!empty($data['pickup_address']) && !empty($data['delivery_address'])) {
            return 'Opening the ' . $type . ' form with route ' . $data['pickup_address'] . ' to ' . $data['delivery_address'] . ' filled in.';
        }

        return 'Opening the ' . $type . ' form with the details I could understand.';
    }

    private function cleanValue(?string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', trim((string) $value, " \t\n\r\0\x0B,.:-")));
    }

    private function unknown(): array
    {
        return [
            'intent' => 'general_help',
            'action' => 'guidance',
            'url' => '#',
            'data' => [],
            'message' => 'Tell me what you want to do, like create a pickup, fill a dispatch form, track an order, or create a reminder.',
            'confidence' => 0.3,
        ];
    }
}
