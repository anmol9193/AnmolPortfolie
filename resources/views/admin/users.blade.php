@extends('admin.layout')

@section('title', 'Admins')

@push('styles')
<style>
.admin-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 0}
.admin-row + .admin-row{border-top:1px solid var(--border)}
.admin-person{min-width:0;display:grid;gap:5px}
.admin-person small{color:var(--muted);overflow-wrap:anywhere}
.admin-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center;flex-shrink:0}
.admin-person .badge{width:fit-content;font-size:.6rem;letter-spacing:.07em}
.admin-pagination{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:18px;font-size:.8rem;color:var(--muted)}
@media(max-width:600px){.admin-row{align-items:flex-start;flex-direction:column}.admin-actions{width:100%}}
</style>
@endpush

@section('content')
  <div class="head"><div class="eyebrow">Team access</div><h1>Manage <span class="serif accent">admins</span></h1><p>Add trusted admins and control their access to your portfolio.</p></div>
  <div class="cols">
    <div class="card">
      <div class="card-head"><h2>Admin accounts</h2><span>{{ $admins->total() }} accounts</span></div>
      @foreach ($admins as $admin)
        <div class="admin-row">
          <div class="admin-person">
            <b>{{ $admin->name }}</b><small>{{ $admin->email }}</small>
            <span class="badge">{{ $admin->isSuperAdmin() ? 'Super admin' : ($admin->is_active ? 'Active admin' : 'Inactive admin') }}</span>
          </div>
          @unless ($admin->isSuperAdmin() || $admin->is(auth()->user()))
            <div class="admin-actions">
              <form method="POST" action="{{ route('admin.users.update', $admin) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_active" value="{{ $admin->is_active ? '0' : '1' }}">
                <button class="sm" type="submit">{{ $admin->is_active ? 'Deactivate' : 'Activate' }}</button>
              </form>
              <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" data-confirm="Remove this admin?" data-confirm-text="{{ $admin->email }} will no longer be able to sign in." data-confirm-button="Remove admin">
                @csrf
                @method('DELETE')
                <button class="sm danger" type="submit">Remove</button>
              </form>
            </div>
          @endunless
        </div>
      @endforeach
      @if ($admins->hasPages())
        <div class="admin-pagination">
          @if ($admins->previousPageUrl())<a href="{{ $admins->previousPageUrl() }}">Previous</a>@else<span></span>@endif
          <span>Page {{ $admins->currentPage() }} of {{ $admins->lastPage() }}</span>
          @if ($admins->nextPageUrl())<a href="{{ $admins->nextPageUrl() }}">Next</a>@else<span></span>@endif
        </div>
      @endif
    </div>
    <form class="card" method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="card-head"><h2>Add admin</h2><span class="badge">Super admin only</span></div>
      <div class="form-grid">
        <div class="fld wide"><label for="admin-name">Name <span class="req">*</span></label><input id="admin-name" name="name" type="text" value="{{ old('name') }}" maxlength="80" required autocomplete="name">@error('name')<span class="err">{{ $message }}</span>@enderror</div>
        <div class="fld wide"><label for="admin-email">Email <span class="req">*</span></label><input id="admin-email" name="email" type="text" inputmode="email" value="{{ old('email') }}" maxlength="255" required autocomplete="off">@error('email')<span class="err">{{ $message }}</span>@enderror</div>
        <div class="fld wide"><label for="admin-password">Password</label><input id="admin-password" name="password" type="password" required minlength="8" autocomplete="new-password"><span class="hint">Admins sign in with this password, then verify a code sent to their email.</span>@error('password')<span class="err">{{ $message }}</span>@enderror</div>
        <div class="fld wide"><label for="admin-password-confirm">Confirm password</label><input id="admin-password-confirm" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></div>
      </div>
      <div class="form-foot"><button class="btn btn-primary" type="submit">Add admin</button></div>
      <p class="hint">Admins can access Dashboard, Themes, Appearance, Site pages and Messages. All other tools are reserved for the super admin.</p>
    </form>
  </div>
@endsection
