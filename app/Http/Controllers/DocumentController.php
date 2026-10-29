<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\RagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DocumentController extends Controller
{
    public function __construct(private RagService $rag)
    {
    }

    public function index()
    {
        $documents = Document::withCount('chunks')->latest()->get();

        return view('documents.index', compact('documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'min:20', 'max:20000'],
        ]);

        $document = Document::create($data);

        try {
            $count = $this->rag->ingest($document);
        } catch (Throwable $e) {
            Log::error('Document ingest failed: '.$e->getMessage());

            // Do not keep a half-processed document
            $document->delete();

            return back()
                ->withInput()
                ->withErrors(['content' => 'Could not process the document with the AI API. Check storage/logs/laravel.log and try again.']);
        }

        return redirect()
            ->route('documents.index')
            ->with('status', "Document saved and split into {$count} chunks.");
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete(); // chunks are deleted automatically (cascade)

        return redirect()->route('documents.index')->with('status', 'Document deleted.');
    }
}
