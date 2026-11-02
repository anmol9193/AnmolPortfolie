@extends('admin.layout')

@section('title', 'AI Chatbot')

@section('content')
  <div class="head">
    <div class="eyebrow">AI Chatbot</div>
    <h1>Chatbot <span class="serif accent">settings</span></h1>
  </div>
  <div class="cols">
    <div class="stack">
      <form class="card" method="POST" action="{{ route('admin.chat.update') }}">
        @csrf
        @method('PUT')
        <div class="card-head"><h2>ScaleMax</h2><span class="badge">API key</span></div>
        <div class="form-grid">
          <div class="fld wide">
            <label for="chat-api-key">{{ $hasSavedKey ? 'Replace API key' : 'ScaleMax API key' }} <span class="req">*</span></label>
            <input id="chat-api-key" type="password" name="api_key" required maxlength="512" autocomplete="new-password" spellcheck="false" autocapitalize="none" aria-describedby="chat-key-hint">
            <span class="hint" id="chat-key-hint">Paste your ScaleMax API key. Saved keys stay hidden. Saving a new key replaces the previous one.</span>
            @error('api_key')<span class="err">{{ $message }}</span>@enderror
          </div>
        </div>
        <div class="form-foot"><button class="btn btn-primary" type="submit">Save API key</button></div>
      </form>
      @if ($hasSavedKey)
        <form class="card" method="POST" action="{{ route('admin.chat.destroy') }}">
          @csrf
          @method('DELETE')
          <div class="card-head"><h2>Remove saved key</h2></div>
          <p class="hint">The chatbot will fall back to the server environment key if one is configured.</p>
          <div class="form-foot"><button class="btn btn-ghost" type="submit">Remove saved key</button></div>
        </form>
      @endif
    </div>
    <div class="stack">
      <div class="card">
        <div class="card-head"><h2>Connection settings</h2><span>ScaleMax</span></div>
        <ul class="rows">
          <li><span>Admin key</span><b>{{ $hasSavedKey ? 'Saved' : 'Not saved' }}</b></li>
          <li><span>Environment key</span><b>{{ $hasEnvironmentKey ? 'Configured' : 'Not configured' }}</b></li>
          <li><span>Endpoint</span><b>{{ config('chat.base_url') }}</b></li>
          <li><span>Model</span><b>{{ config('chat.model') }}</b></li>
        </ul>
        <p class="hint">The assistant uses your saved ScaleMax key for conversational replies. Portfolio facts come from your public content.</p>
        <form method="POST" action="{{ route('admin.chat.test') }}">
          @csrf
          <div class="form-foot"><button class="btn" type="submit">Test connection</button></div>
          @error('connection')<p class="hint" role="alert">{{ $message }}</p>@enderror
        </form>
      </div>
    </div>
  </div>
@endsection
