<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Small client for any OpenAI-compatible API (Gemini, Groq, OpenRouter, Ollama...).
 * Two jobs: chat() to get an answer, embed() to turn text into numbers.
 */
class LlmClient
{
    /**
     * Ask the chat model. Returns the answer text.
     */
    public function chat(string $system, string $user, int $maxTokens = 2000): string
    {
        $response = $this->http()
            ->post($this->baseUrl().'/chat/completions', [
                'model'       => $this->required('services.ai.model', 'AI_MODEL'),
                'temperature' => 0.2,
                'max_tokens'  => $maxTokens,
                'messages'    => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
            ])
            ->throw();

        $text = $response->json('choices.0.message.content');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Empty response from the chat model.');
        }

        return trim($text);
    }

    /**
     * Turn a list of texts into a list of vectors (same order).
     *
     * @param  string[]  $texts
     * @return array<int, float[]>
     */
    public function embed(array $texts): array
    {
        $texts = array_values($texts);

        $payload = [
            'model' => $this->required('services.ai.embed_model', 'AI_EMBED_MODEL'),
            'input' => $texts,
        ];

        // Optional: smaller vectors = less storage (only if your provider supports it)
        if ($dimensions = config('services.ai.embed_dimensions')) {
            $payload['dimensions'] = (int) $dimensions;
        }

        $response = $this->http()
            ->post($this->baseUrl().'/embeddings', $payload)
            ->throw();

        $data = $response->json('data');

        if (! is_array($data) || count($data) !== count($texts)) {
            throw new RuntimeException('Unexpected response from the embedding API.');
        }

        usort($data, fn ($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));

        return array_map(fn ($row) => $row['embedding'], $data);
    }

    private function http(): PendingRequest
    {
        $request = Http::timeout(60)->retry(3, 2000, function ($exception) {
            if ($exception instanceof ConnectionException) {
                return true;
            }

            return $exception instanceof RequestException
                && in_array($exception->response->status(), [429, 500, 502, 503, 504], true);
        });

        if ($key = config('services.ai.key')) {
            $request = $request->withToken($key);
        }

        return $request;
    }

    private function baseUrl(): string
    {
        return rtrim($this->required('services.ai.base_url', 'AI_BASE_URL'), '/');
    }

    private function required(string $configKey, string $envName): string
    {
        $value = config($configKey);

        if (! $value) {
            throw new RuntimeException("{$envName} is not set in .env");
        }

        return (string) $value;
    }
}
