<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class AiController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        try {
            $chat = new Chat();
            $chat->systemMessage('You are a helpful shopping assistant for an e-commerce marketplace.');

            return response()->json([
                'reply' => $chat->send($request->message) ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function productAnalyze(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        try {
            $file = $request->file('image');
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $mime = $file->getMimeType();

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o',
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this product image for an e-commerce listing. Return JSON only with exactly these keys: product_name (string), short_description (string, max 160 characters), long_description (string, 2-4 sentences).',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mime};base64,{$base64}",
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $data = json_decode($response->choices[0]->message->content ?? '{}', true) ?? [];

            return response()->json([
                'product_name' => (string) ($data['product_name'] ?? ''),
                'short_description' => (string) ($data['short_description'] ?? ''),
                'long_description' => (string) ($data['long_description'] ?? ''),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
