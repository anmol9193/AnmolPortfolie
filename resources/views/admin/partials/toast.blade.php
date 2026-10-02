{{-- Floating alert for the result of the last action ("... saved.", validation problems). --}}
@php
    $toast = session('status')
        ? ['ok', 'Done', session('status')]
        : ($errors->any() ? ['bad', 'Not saved', $errors->count() === 1 ? $errors->first() : 'Please fix the '.$errors->count().' highlighted fields.'] : null);
@endphp
@if ($toast)
  @verbatim
  <style>
  .toast{position:fixed;top:16px;right:20px;z-index:300;width:min(420px,100% - 32px);display:flex;align-items:flex-start;gap:14px;padding:16px 16px 18px;border-radius:18px;background:var(--surface);border:1px solid color-mix(in srgb,var(--accent) 40%,var(--border));overflow:hidden;animation:toast-in .6s var(--ease) both}
  .toast.hide{animation:toast-out .45s var(--ease) forwards}
  .toast .t-ic{flex:none;width:40px;height:40px;border-radius:12px;display:grid;place-items:center;background:var(--accent);color:var(--accent-ink)}
  .toast .t-ic svg{width:20px;height:20px}
  .toast .t-body{flex:1;min-width:0;padding-top:1px}
  .toast b{display:block;font-size:.95rem;line-height:1.3}
  .toast p{color:var(--muted);font-size:.86rem;line-height:1.45;margin-top:2px;overflow-wrap:anywhere}
  .toast .t-x{flex:none;width:30px;height:30px;border:none;border-radius:50%;background:transparent;color:var(--muted);display:grid;place-items:center;cursor:pointer;transition:.25s var(--ease)}
  .toast .t-x:hover{background:var(--surface-2);color:var(--text)}
  .toast .t-x svg{width:15px;height:15px}
  .toast .t-bar{position:absolute;left:0;bottom:0;height:3px;width:100%;background:linear-gradient(90deg,var(--accent),var(--accent-2));transform-origin:left;animation:toast-bar var(--t-time) linear forwards}
  .toast:hover .t-bar{animation-play-state:paused}
  .toast b{color:var(--accent)}
  .toast.bad .t-ic{background:var(--accent-soft);color:var(--accent)}
  @keyframes toast-in{from{opacity:0;transform:translateX(28px) scale(.96)}to{opacity:1;transform:none}}
  @keyframes toast-out{to{opacity:0;transform:translateX(28px)}}
  @media (max-width:860px){.toast{right:16px}}
  @keyframes toast-bar{to{transform:scaleX(0)}}
  </style>
  @endverbatim
  <div @class(['toast', $toast[0]]) id="toast" role="{{ $toast[0] === 'ok' ? 'status' : 'alert' }}" style="--t-time:{{ $toast[0] === 'ok' ? 5 : 8 }}s">
    <div class="t-ic">
      @if ($toast[0] === 'ok')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      @else
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
      @endif
    </div>
    <div class="t-body"><b>{{ $toast[1] }}</b><p>{{ $toast[2] }}</p></div>
    <button class="t-x" type="button" aria-label="Close message"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    <i class="t-bar"></i>
  </div>
  <script>
  // Close on the button, or by itself when the timer bar runs out (hovering pauses it).
  (function(){
    const toast=document.getElementById('toast'), close=()=>{toast.classList.add('hide');setTimeout(()=>toast.remove(),450)};
    toast.querySelector('.t-x').addEventListener('click',close);
    toast.querySelector('.t-bar').addEventListener('animationend',close);
  })();
  </script>
@endif
