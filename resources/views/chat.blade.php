@extends('layout')

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('chat.ask') }}">
            @csrf
            <label for="question">Your question</label>
            <input id="question" name="question" value="{{ old('question', $question) }}" placeholder="e.g. How many days do I have to return a product?" required>
            @error('question') <div class="err">{{ $message }}</div> @enderror
            <button type="submit">Ask</button>
        </form>
    </div>

    @if ($answer)
        <div class="card">
            <strong>Answer</strong>
            <div class="answer" style="margin-top:8px">{!! nl2br(e($answer)) !!}</div>

            @if (count($sources))
                <div style="margin-top:14px"><strong>Sources used</strong></div>
                @foreach ($sources as $i => $source)
                    <div class="src">
                        <strong>[{{ $i + 1 }}] {{ $source['title'] }}</strong>
                        <span class="muted">(similarity {{ $source['score'] }})</span>
                        <div class="muted">{{ $source['snippet'] }}…</div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif
@endsection
