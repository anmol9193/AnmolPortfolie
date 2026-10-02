@extends('admin.layout')

@section('title', 'Messages')

@push('styles')
<style>
.inbox{list-style:none}
.inbox li + li{border-top:1px solid var(--border)}
.inbox a{display:grid;grid-template-columns:12px 44px minmax(0,1.1fr) minmax(0,2fr) auto;align-items:center;gap:14px;padding:14px 12px;margin:0 -12px;border-radius:14px;transition:background .25s}
.inbox a:hover{background:var(--surface-2)}
.inbox .dot{width:9px;height:9px;border-radius:50%;background:transparent}
.inbox .new .dot{background:var(--accent);box-shadow:0 0 0 4px var(--accent-soft)}
.inbox .av{width:44px;height:44px;border-radius:14px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;font-weight:700;font-size:.9rem}
.inbox b{display:block;font-weight:500;font-size:.94rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.inbox small{display:block;color:var(--muted);font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.inbox .new b{font-weight:700}
.inbox .txt small{font-size:.84rem}
.inbox time{font-size:.76rem;color:var(--faint);white-space:nowrap;text-align:right}
.pager{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:18px;padding-top:16px;border-top:1px solid var(--border);font-size:.84rem;color:var(--muted)}
.pager div{display:flex;gap:8px}
.pager .off{opacity:.4;pointer-events:none}
@media (max-width:760px){
  .inbox a{grid-template-columns:12px 44px minmax(0,1fr) auto}
  .inbox .txt{display:none}
}
</style>
@endpush

@section('content')
  <div class="head">
    <div class="eyebrow">Messages</div>
    <h1>Contact form <span class="serif accent">inbox</span></h1>
  </div>

  <div class="card">
    <div class="card-head"><h2>All messages</h2><span>{{ $messages->total() }} total · {{ $unread }} unread</span></div>

    @if ($messages->isEmpty())
      <p class="empty">No messages yet. When someone fills in the contact form on your site, it shows up here.</p>
    @else
      <ul class="inbox">
        @foreach ($messages as $msg)
          <li @class(['new' => ! $msg->read_at])>
            <a href="{{ route('admin.messages.show', $msg) }}">
              <span class="dot" @if (! $msg->read_at) title="Unread" @endif></span>
              <span class="av">{{ mb_strtoupper(mb_substr($msg->name, 0, 1)) }}</span>
              <span><b>{{ $msg->name }}</b><small>{{ $msg->email }} · {{ $msg->phone }}</small></span>
              <span class="txt"><b>{{ $msg->subject }}</b><small>{{ Str::limit($msg->message, 90) }}</small></span>
              <time datetime="{{ $msg->created_at->toIso8601String() }}">{{ $msg->created_at->diffForHumans() }}</time>
            </a>
          </li>
        @endforeach
      </ul>

      @if ($messages->hasPages())
        <div class="pager">
          <span>Page {{ $messages->currentPage() }} of {{ $messages->lastPage() }}</span>
          <div>
            <a @class(['sm', 'off' => $messages->onFirstPage()]) href="{{ $messages->previousPageUrl() ?? '#' }}">Newer</a>
            <a @class(['sm', 'off' => ! $messages->hasMorePages()]) href="{{ $messages->nextPageUrl() ?? '#' }}">Older</a>
          </div>
        </div>
      @endif
    @endif
  </div>
@endsection
