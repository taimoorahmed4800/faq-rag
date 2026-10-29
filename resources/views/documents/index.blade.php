@extends('layout')

@section('content')
    <div class="card">
        <strong>Add a document</strong>
        <div class="muted">Paste FAQ text, policies, product info... Separate topics with a blank line.</div>
        <form method="POST" action="{{ route('documents.store') }}">
            @csrf
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title') }}" required>
            @error('title') <div class="err">{{ $message }}</div> @enderror

            <label for="content">Text</label>
            <textarea id="content" name="content" required>{{ old('content') }}</textarea>
            @error('content') <div class="err">{{ $message }}</div> @enderror

            <button type="submit">Save &amp; index with AI</button>
        </form>
    </div>

    <h3>Documents ({{ $documents->count() }})</h3>

    @forelse ($documents as $document)
        <div class="card">
            <div class="row">
                <div>
                    <strong>{{ $document->title }}</strong>
                    <div class="muted">{{ $document->chunks_count }} chunks · added {{ $document->created_at->diffForHumans() }}</div>
                </div>
                <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?')">
                    @csrf
                    @method('DELETE')
                    <button class="danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <p class="muted">No documents yet. Add one above (you can use samples/sample-faq.txt).</p>
    @endforelse
@endsection
