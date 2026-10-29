<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FAQ Chatbot (RAG)</title>
    <style>
        :root { --bg:#f4f6fb; --card:#fff; --text:#1d2433; --muted:#6b7280; --brand:#2e5c9a; --line:#e3e7ef; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; background:var(--bg); color:var(--text); }
        .wrap { max-width: 860px; margin: 0 auto; padding: 24px 16px 60px; }
        h1 { margin: 0 0 4px; font-size: 24px; color: var(--brand); }
        .sub { color: var(--muted); margin-bottom: 14px; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 14px; font-weight: 600; }
        nav a.active { border-bottom: 2px solid var(--brand); }
        .card { background: var(--card); border:1px solid var(--line); border-radius:10px; padding:18px; margin-bottom:16px; }
        label { display:block; font-weight:600; margin: 10px 0 4px; font-size: 14px; }
        input, textarea { width:100%; padding:9px 10px; border:1px solid var(--line); border-radius:6px; font: inherit; }
        textarea { min-height: 160px; resize: vertical; }
        button { background: var(--brand); color:#fff; border:0; padding:10px 16px; border-radius:6px; font: inherit; cursor:pointer; margin-top:14px; }
        button.danger { background:#fff; color:#b42318; border:1px solid #b42318; margin-top:0; padding:5px 10px; }
        .err { color:#b42318; font-size:13px; margin-top:4px; }
        .ok { background:#e7f6ec; color:#1e7a46; padding:10px 14px; border-radius:6px; margin-bottom:14px; }
        .row { display:flex; justify-content:space-between; gap:12px; align-items:center; }
        .muted { color: var(--muted); font-size: 13px; }
        a { color: var(--brand); text-decoration: none; }
        .answer { background:#f7f9fd; border-left:3px solid var(--brand); padding:12px 14px; border-radius:6px; }
        .src { border-top:1px solid var(--line); padding-top:8px; margin-top:8px; font-size:13px; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>FAQ Chatbot (RAG)</h1>
    <div class="sub">Laravel + free AI API: answers come only from your own documents.</div>
    <nav>
        <a href="{{ route('chat.show') }}" class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">Ask</a>
        <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.*') ? 'active' : '' }}">Documents</a>
    </nav>
    @if (session('status')) <div class="ok">{{ session('status') }}</div> @endif
    @yield('content')
</div>
</body>
</html>
