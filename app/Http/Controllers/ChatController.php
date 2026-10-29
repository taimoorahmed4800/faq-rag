<?php

namespace App\Http\Controllers;

use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatController extends Controller
{
    public function __construct(private RagService $rag)
    {
    }

    public function show()
    {
        return view('chat', ['question' => null, 'answer' => null, 'sources' => []]);
    }

    public function ask(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        try {
            $result = $this->rag->ask($data['question']);
        } catch (Throwable $e) {
            Log::error('RAG ask failed: '.$e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['question' => 'The AI request failed. Please try again in a moment.']);
        }

        return view('chat', [
            'question' => $data['question'],
            'answer'   => $result['answer'],
            'sources'  => $result['sources'],
        ]);
    }
}
