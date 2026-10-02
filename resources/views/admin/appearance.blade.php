@extends('admin.layout')

@section('title', 'Appearance')

@php
    $isCustom = old('color', $theme['color']) === \App\Support\Theme::CUSTOM;
    $bgChoice = old('background', $theme['bg_choice']);
    $hasBg = $bgChoice === 'custom';
    $accent = old('accent', $theme['accent']);
    $accent2 = old('accent2', $theme['accent2']);
    // a sensible starting point for the background picker: the current mode's default background
    $bg = old('bg', $theme['bg'] ?? ($theme['mode'] === 'dark' ? '#0c0b0a' : '#f5f1e8'));
@endphp

@push('styles')
<style>
.swatch i.any{background:conic-gradient(#ff5b2e,#f5c542,#3ddc84,#2dd4bf,#4f9dff,#a78bfa,#ff5c93,#ff5b2e)}
.swatch i.bg-dot{box-shadow:inset 0 0 0 1px rgba(128,128,128,.45)}
.swatch i.bg-default{background:linear-gradient(135deg,#f5f1e8 50%,#0c0b0a 50%);box-shadow:inset 0 0 0 1px rgba(128,128,128,.45)}
.pickers{display:flex;flex-wrap:wrap;gap:14px;margin-top:16px;padding:16px;border-radius:16px;border:1px dashed var(--border);background:var(--bg-2)}
.pickers[hidden]{display:none}
.picker{display:flex;align-items:center;gap:12px;min-width:210px}
.picker input[type=color]{width:52px;height:44px;padding:4px;border-radius:12px;border:1px solid var(--border);background:var(--surface);cursor:pointer;flex:none}
.picker b{display:block;font-size:.86rem}
.picker code{display:block;font-family:inherit;font-size:.8rem;color:var(--muted);text-transform:uppercase}
fieldset .hint{margin-top:12px}
.pv-box{border-radius:16px;border:1px solid var(--border);background:var(--bg);padding:22px}
.pv-card{margin-top:4px;padding:14px 16px;border-radius:14px;background:var(--surface);border:1px solid var(--border);font-size:.86rem;color:var(--muted)}
.pv-card b{display:block;color:var(--text);font-size:.95rem}
</style>
@endpush

@section('content')
  <div class="head">
    <div class="eyebrow">Appearance</div>
    <h1>Theme &amp; <span class="serif accent">colors</span></h1>
  </div>

  <div class="cols">
    <form class="card" id="appearance" method="POST" action="{{ route('admin.appearance.update') }}">
      @csrf
      @method('PUT')

      <fieldset>
        <legend>Mode</legend>
        <div class="modes">
          <label class="mode">
            <input type="radio" name="mode" value="dark" @checked(old('mode', $theme['saved_mode']) === 'dark')>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>Dark</span>
          </label>
          <label class="mode">
            <input type="radio" name="mode" value="light" @checked(old('mode', $theme['saved_mode']) === 'light')>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>Light</span>
          </label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Accent color</legend>
        <div class="swatches">
          @foreach (config('theme.colors') as $key => $color)
            <label class="swatch">
              <input type="radio" name="color" value="{{ $key }}" @checked(old('color', $theme['color']) === $key)>
              <i data-color="{{ $key }}" style="background:{{ $color[$theme['mode']][0] }}"></i>
              <span>{{ $color['label'] }}</span>
            </label>
          @endforeach
          <label class="swatch">
            <input type="radio" name="color" value="custom" @checked($isCustom)>
            <i class="any"></i>
            <span>Custom</span>
          </label>
        </div>
        <div class="pickers" id="accentPickers" @if (! $isCustom) hidden @endif>
          <label class="picker">
            <input type="color" name="accent" value="{{ $accent }}">
            <span><b>Main color</b><code data-for="accent">{{ $accent }}</code></span>
          </label>
          <label class="picker">
            <input type="color" name="accent2" value="{{ $accent2 }}">
            <span><b>Second color</b><code data-for="accent2">{{ $accent2 }}</code></span>
          </label>
        </div>
        @error('accent')<span class="err">{{ $message }}</span>@enderror
        <p class="hint">Custom lets you pick any two colors: the main one for buttons and highlights, the second one for gradients.</p>
      </fieldset>

      <fieldset>
        <legend>Page background</legend>
        <div class="swatches">
          <label class="swatch">
            <input type="radio" name="background" value="default" @checked($bgChoice === 'default')>
            <i class="bg-default"></i>
            <span>Default</span>
          </label>
          @foreach (config('theme.backgrounds') as $key => $background)
            <label class="swatch">
              <input type="radio" name="background" value="{{ $key }}" @checked($bgChoice === $key)>
              <i class="bg-dot" style="background:{{ $background['color'] }}"></i>
              <span>{{ $background['label'] }}</span>
            </label>
          @endforeach
          <label class="swatch">
            <input type="radio" name="background" value="custom" @checked($hasBg)>
            <i class="any"></i>
            <span>Custom</span>
          </label>
        </div>
        <div class="pickers" id="bgPickers" @if (! $hasBg) hidden @endif>
          <label class="picker">
            <input type="color" name="bg" value="{{ $bg }}">
            <span><b>Page background</b><code data-for="bg">{{ $bg }}</code></span>
          </label>
        </div>
        @error('bg')<span class="err">{{ $message }}</span>@enderror
        <p class="hint">Default follows the Mode above. With any other background, cards and borders are shaded from it and dark or light text is chosen automatically from how bright it is (the Mode is then ignored).</p>
      </fieldset>

      <button class="btn btn-primary" type="submit">Save changes
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </button>
      <p class="hint" id="apHint" hidden>Previewing — click “Save changes” to apply this to the site.</p>
    </form>

    <div class="card" style="animation-delay:.08s">
      <div class="card-head"><h2>Preview</h2><span>Live</span></div>
      <div class="preview pv-box">
        <div class="pv-h">{{ content('site.name') }}<span class="serif accent">.</span></div>
        <div class="pv-row"><span class="pv-chip">About me</span><span class="pv-chip">Projects</span></div>
        <div class="pv-bar"></div>
        <div class="pv-card"><b>A card</b>Text on a card looks like this.</div>
        <div class="pv-row"><span class="pv-btn">See my work</span></div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
// ---------- Live preview (saved only on submit) ----------
const palettes=@json(config('theme.colors')), backgrounds=@json(config('theme.backgrounds'));
const apForm=document.getElementById('appearance'), root=document.documentElement;
const rgb=h=>[1,3,5].map(i=>parseInt(h.slice(i,i+2),16));
const lum=h=>{const [r,g,b]=rgb(h);return (.299*r+.587*g+.114*b)/255};
const mix=(h,pct,to)=>`color-mix(in srgb,${h} ${pct}%,${to})`;
function preview(){
  const custom=apForm.color.value==='custom', bgChoice=apForm.background.value;
  const hasBg=bgChoice!=='default', bg=bgChoice==='custom'?apForm.bg.value:(backgrounds[bgChoice]||{}).color;
  // same rules as App\Support\Theme and partials/theme.blade.php
  const mode=hasBg?(lum(bg)<.4?'dark':'light'):apForm.mode.value;
  const [accent,accent2,ink]=custom
    ?[apForm.accent.value,apForm.accent2.value,lum(apForm.accent.value)>.55?'#16110d':'#ffffff']
    :palettes[apForm.color.value][mode];
  const [r,g,b]=rgb(accent);
  root.setAttribute('data-theme',mode);
  root.style.setProperty('--accent',accent);
  root.style.setProperty('--accent-2',accent2);
  root.style.setProperty('--accent-ink',ink);
  root.style.setProperty('--accent-soft',`rgba(${r},${g},${b},${mode==='dark'?.12:.10})`);
  const shades=!hasBg?{}:mode==='dark'
    ?{'--bg':bg,'--bg-2':mix(bg,96,'#fff'),'--surface':mix(bg,93,'#fff'),'--surface-2':mix(bg,88,'#fff'),'--border':mix(bg,81,'#fff')}
    :{'--bg':bg,'--bg-2':mix(bg,96,'#000'),'--surface':mix(bg,45,'#fff'),'--surface-2':mix(bg,92,'#000'),'--border':mix(bg,85,'#000')};
  ['--bg','--bg-2','--surface','--surface-2','--border'].forEach(k=>shades[k]?root.style.setProperty(k,shades[k]):root.style.removeProperty(k));
  apForm.querySelectorAll('.swatch i[data-color]').forEach(i=>i.style.background=palettes[i.dataset.color][mode][0]);
  document.getElementById('accentPickers').hidden=!custom;
  document.getElementById('bgPickers').hidden=bgChoice!=='custom';
  apForm.querySelectorAll('code[data-for]').forEach(c=>c.textContent=apForm[c.dataset.for].value);
}
apForm.addEventListener('input',()=>{preview();document.getElementById('apHint').hidden=false});
</script>
@endpush
