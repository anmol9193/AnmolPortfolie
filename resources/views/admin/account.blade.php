@extends('admin.layout')

@section('title', 'Account')

@php
    $user = auth()->user();
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp

@section('content')
  <div class="head">
    <div class="eyebrow">Account</div>
    <h1>Your <span class="serif accent">account</span></h1>
  </div>

  <div class="cols">
    <div class="stack">
      <form class="card" id="profile" method="POST" action="{{ route('admin.account.profile') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-head"><h2>Profile</h2><span class="badge">Admin</span></div>
        <div class="profile">
          <div class="avatar" id="avatarPreview">@if ($user->avatar)<img src="{{ media($user->avatar) }}" alt="">@else{{ $initials }}@endif</div>
          <div><b>{{ $user->name }}</b><small>{{ $user->email }}</small></div>
        </div>
        <div class="form-grid">
          <div class="fld wide">
            <label for="f-avatar">Profile picture</label>
            <input id="f-avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
            <span class="hint">JPG, PNG or WebP, up to 4 MB. Shown in the header and on the dashboard instead of your initials.</span>
            @if ($user->avatar)
              <label class="check" style="margin-top:10px"><input type="checkbox" name="remove_avatar" value="1"> Remove picture and show my initials</label>
            @endif
            @error('avatar')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="fld">
            <label for="f-name">Name <span class="req">*</span></label>
            <input id="f-name" type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="80" autocomplete="name">
            @error('name')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="fld">
            <label for="f-email">Login email <span class="req">*</span></label>
            <input id="f-email" type="text" inputmode="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <span class="hint">You sign in with this email.</span>
            @error('email')<span class="err">{{ $message }}</span>@enderror
          </div>
        </div>
        <div class="form-foot">
          <button class="btn btn-primary" type="submit">Save profile
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          </button>
        </div>
      </form>

      <form class="card" id="password" method="POST" action="{{ route('admin.account.password') }}" style="animation-delay:.08s">
        @csrf
        @method('PUT')
        <div class="card-head"><h2>Change password</h2><span>Security</span></div>
        <div class="form-grid">
          <div class="fld wide">
            <label for="f-current">Current password <span class="req">*</span></label>
            <input id="f-current" class="pw" type="password" name="current_password" required autocomplete="current-password">
            @error('current_password')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="fld">
            <label for="f-new">New password <span class="req">*</span></label>
            <input id="f-new" class="pw" type="password" name="password" required minlength="8" autocomplete="new-password">
            <span class="hint">At least 8 characters.</span>
            @error('password')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="fld">
            <label for="f-confirm">Repeat new password <span class="req">*</span></label>
            <input id="f-confirm" class="pw" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
          </div>
          <div class="fld wide">
            <label class="check"><input type="checkbox" id="showPw"> Show passwords</label>
          </div>
        </div>
        <div class="form-foot">
          <button class="btn btn-primary" type="submit">Change password
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </button>
        </div>
      </form>
    </div>

    <div class="stack">
      <div class="card" style="animation-delay:.12s">
        <div class="card-head"><h2>Details</h2><span>Info</span></div>
        <ul class="rows">
          <li><span>Role</span><b>Site owner</b></li>
          <li><span>Account created</span><b>{{ $user->created_at?->format('d M Y') ?? '—' }}</b></li>
          <li><span>Last changed</span><b>{{ $user->updated_at?->diffForHumans() ?? '—' }}</b></li>
        </ul>
      </div>

      <div class="card" style="animation-delay:.18s">
        <div class="card-head"><h2>System</h2><span>Info</span></div>
        <ul class="rows">
          <li><span>Environment</span><b>{{ app()->environment() }}</b></li>
          <li><span>Debug mode</span><b>{{ config('app.debug') ? 'on' : 'off' }}</b></li>
          <li><span>Laravel</span><b>{{ app()->version() }}</b></li>
          <li><span>PHP</span><b>{{ PHP_VERSION }}</b></li>
          <li><span>Database</span><b>{{ config('database.connections.'.config('database.default').'.database') }}</b></li>
          <li><span>Timezone</span><b>{{ config('app.timezone') }}</b></li>
        </ul>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
// ---------- Preview the chosen profile picture before saving ----------
document.getElementById('f-avatar').addEventListener('change',e=>{
  const file=e.target.files[0]; if(!file) return;
  const img=new Image(); img.alt=''; img.src=URL.createObjectURL(file);
  document.getElementById('avatarPreview').replaceChildren(img);
});

// ---------- Show / hide the three password fields ----------
document.getElementById('showPw').addEventListener('change',e=>{
  document.querySelectorAll('.pw').forEach(i=>i.type=e.target.checked?'text':'password');
});
</script>
@endpush
