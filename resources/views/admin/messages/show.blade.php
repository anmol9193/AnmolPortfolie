@extends('admin.layout')

@section('title', 'Message')

@php
    $tel = preg_replace('/[^\d+]/', '', $msg->phone);
@endphp

@push('styles')
<style>
.msg-top{display:flex;align-items:center;gap:16px;margin-bottom:20px}
.msg-top .av{flex:none;width:58px;height:58px;border-radius:18px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;font-weight:700;font-size:1.2rem}
.msg-top b{display:block;font-size:1.15rem;line-height:1.3}
.msg-top small{color:var(--muted);font-size:.86rem}
.msg-subject{font-size:1.2rem;font-weight:700;letter-spacing:-.02em;margin-bottom:12px}
.msg-body{padding:20px;border-radius:16px;background:var(--bg-2);border:1px solid var(--border);line-height:1.75;white-space:pre-wrap;overflow-wrap:anywhere}
.rows a{color:var(--accent)}
.rows a:hover{text-decoration:underline}
.btn.flat:hover svg{transform:none}
</style>
@endpush

@section('content')
  <div class="head head-row">
    <div>
      <div class="eyebrow">Messages</div>
      <h1>Message from <span class="serif accent">{{ Str::before($msg->name.' ', ' ') }}</span></h1>
    </div>
    <a href="{{ route('admin.messages') }}" class="btn flat">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>Back to inbox</a>
  </div>

  <div class="cols">
    <div class="card">
      <div class="msg-top">
        <div class="av">{{ mb_strtoupper(mb_substr($msg->name, 0, 1)) }}</div>
        <div><b>{{ $msg->name }}</b><small>{{ $msg->created_at->format('d M Y, h:i A') }} · {{ $msg->created_at->diffForHumans() }}</small></div>
      </div>
      <div class="msg-subject">{{ $msg->subject }}</div>
      <div class="msg-body">{{ $msg->message }}</div>

      <div class="form-foot">
        <a class="btn btn-primary" href="mailto:{{ $msg->email }}?subject={{ rawurlencode('Re: '.$msg->subject) }}">Reply by email
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg></a>
        <a class="btn" href="tel:{{ $tel }}">Call</a>
        <form method="POST" action="{{ route('admin.messages.destroy', $msg) }}" style="margin-left:auto"
              data-confirm="Delete this message?" data-confirm-text="The message from {{ $msg->name }} will be removed. This cannot be undone." data-confirm-button="Delete">
          @csrf
          @method('DELETE')
          <button class="sm danger" type="submit">Delete</button>
        </form>
      </div>
    </div>

    <div class="card" style="animation-delay:.08s">
      <div class="card-head"><h2>Sender</h2><span>Details</span></div>
      <ul class="rows">
        <li><span>Name</span><b>{{ $msg->name }}</b></li>
        <li><span>Email</span><b><a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a></b></li>
        <li><span>Mobile</span><b><a href="tel:{{ $tel }}">{{ $msg->phone }}</a></b></li>
        <li><span>Received</span><b>{{ $msg->created_at->format('d M Y, h:i A') }}</b></li>
        <li><span>Thank-you email</span><b>{{ $msg->thanked_at ? 'Sent' : 'Not sent' }}</b></li>
      </ul>
    </div>
  </div>
@endsection
