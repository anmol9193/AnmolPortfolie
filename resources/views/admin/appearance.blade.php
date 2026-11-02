@extends('admin.layout')

@section('title', 'Appearance')

@php
    $combination = old('combination', $theme['combination']);
    $combination = is_string($combination) && array_key_exists($combination, config('theme.combinations')) ? $combination : 'individual';
    $selectedMode = old('mode', $theme['saved_mode']);
    $selectedMode = in_array($selectedMode, config('theme.modes'), true) ? $selectedMode : $theme['saved_mode'];
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
.combination-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.combination-option{position:relative;min-width:0;cursor:pointer}
.combination-option input{position:absolute;opacity:0;width:1px;height:1px}
.combination-card{display:grid;gap:10px;height:100%;padding:13px;border:1px solid var(--border);border-radius:16px;background:var(--bg-2);transition:border-color .2s,background .2s}
.combination-option input:checked + .combination-card{border-color:var(--accent);background:var(--accent-soft)}
.combination-option input:focus-visible + .combination-card{outline:2px solid var(--accent);outline-offset:3px}
.combination-colors{display:flex;height:46px;overflow:hidden;border-radius:10px;border:1px solid var(--border)}
.combination-colors i:first-child{flex:5;background:var(--combo-bg)}
.combination-colors i:nth-child(2){flex:3;background:var(--combo-accent)}
.combination-colors i:last-child{flex:2;background:var(--combo-secondary)}
.combination-name{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:.85rem;font-weight:600}
.combination-check{visibility:hidden;color:var(--accent)}
.combination-option input:checked + .combination-card .combination-check{visibility:visible}
.combination-card small{font-size:.72rem;line-height:1.5;color:var(--muted)}
.combination-individual{display:flex;align-items:center;gap:9px;margin-top:14px;font-size:.82rem;cursor:pointer}
.combination-individual input{accent-color:var(--accent);width:16px;height:16px}
.appearance-preview{position:sticky;top:86px}
@media(max-width:1100px){.appearance-preview{position:static}}
@media(max-width:560px){.combination-grid{grid-template-columns:1fr}.picker{min-width:0}}
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
            <input type="radio" name="mode" value="dark" @checked($selectedMode === 'dark')>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>Dark</span>
          </label>
          <label class="mode">
            <input type="radio" name="mode" value="light" @checked($selectedMode === 'light')>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>Light</span>
          </label>
        </div>
      </fieldset>

      <fieldset>
        <legend>3-color combinations</legend>
        <div class="combination-grid">
          @foreach (config('theme.combinations') as $key => $palette)
            @php($paletteColors = $palette[$selectedMode])
            <label class="combination-option">
              <input type="radio" name="combination" value="{{ $key }}" @checked($combination === $key)>
              <span class="combination-card">
                <span class="combination-colors" data-combination="{{ $key }}" style="--combo-bg:{{ $paletteColors['bg'] }};--combo-accent:{{ $paletteColors['accent'] }};--combo-secondary:{{ $paletteColors['accent2'] }}" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="combination-name">{{ $palette['label'] }}<span class="combination-check" aria-hidden="true">&#10003;</span></span>
                <small>{{ $palette['description'] }}</small>
              </span>
            </label>
          @endforeach
        </div>
        <label class="combination-individual"><input type="radio" name="combination" value="individual" @checked($combination === 'individual')> Choose individual colors</label>
        <p class="hint">Each combination sets three colors together: background, main accent and second accent. Cards, text, navigation and buttons follow the palette. Switch Mode above for its light or dark version. Adjusting any color below switches to individual colors.</p>
        @error('combination')<span class="err">{{ $message }}</span>@enderror
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

    <div class="card appearance-preview" style="animation-delay:.08s">
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
const palettes=@json(config('theme.colors')), backgrounds=@json(config('theme.backgrounds')), combinations=@json(config('theme.combinations'));
const apForm=document.getElementById('appearance'), root=document.documentElement;
const rgb=h=>[1,3,5].map(i=>parseInt(h.slice(i,i+2),16));
const lum=h=>{const [r,g,b]=rgb(h);return (.299*r+.587*g+.114*b)/255};
const mix=(h,pct,to)=>`color-mix(in srgb,${h} ${pct}%,${to})`;
function applyCombination(){
  if(apForm.combination.value==='individual') return;
  const colors=combinations[apForm.combination.value][apForm.mode.value];
  apForm.color.value='custom'; apForm.background.value='custom';
  apForm.accent.value=colors.accent; apForm.accent2.value=colors.accent2; apForm.bg.value=colors.bg;
}
function preview(){
  const custom=apForm.color.value==='custom', bgChoice=apForm.background.value;
  const hasBg=bgChoice!=='default', bg=bgChoice==='custom'?apForm.bg.value:(backgrounds[bgChoice]||{}).color;
  const mode=hasBg?(lum(bg)<.4?'dark':'light'):apForm.mode.value;
  const [accent,accent2,ink]=custom
    ?[apForm.accent.value,apForm.accent2.value,lum(apForm.accent.value)>.55?'#16110d':'#ffffff']
    :palettes[apForm.color.value][mode];
  const [r,g,b]=rgb(accent), combined=apForm.combination.value!=='individual';
  root.setAttribute('data-theme',mode);
  const shades=!hasBg?(mode==='dark'
    ?{'--bg':'#0c0b0a','--bg-2':'#121110','--surface':'#161514','--surface-2':'#1e1d1b','--border':'#2b2926','--nav-bg':'rgba(22,21,20,.72)'}
    :{'--bg':'#f5f1e8','--bg-2':'#efeadf','--surface':'#fffcf6','--surface-2':'#ebe5d8','--border':'#dcd4c4','--nav-bg':'rgba(255,252,246,.78)'})
    :mode==='dark'
    ?{'--bg':bg,'--bg-2':mix(bg,96,'#fff'),'--surface':mix(bg,93,'#fff'),'--surface-2':mix(bg,88,'#fff'),'--border':mix(bg,81,'#fff'),'--nav-bg':mix(bg,68,'transparent')}
    :{'--bg':bg,'--bg-2':mix(bg,96,'#000'),'--surface':mix(bg,45,'#fff'),'--surface-2':mix(bg,92,'#000'),'--border':mix(bg,85,'#000'),'--nav-bg':mix(bg,55,'rgba(255,255,255,.55)')};
  const tokens={...shades,'--accent':accent,'--accent-2':accent2,'--accent-ink':ink,'--accent-soft':`rgba(${r},${g},${b},${mode==='dark'?.12:.10})`,
    '--text':combined?(mode==='dark'?'#f4f7fb':'#17202d'):(mode==='dark'?'#f4efe6':'#191714'),
    '--muted':combined?'color-mix(in srgb,var(--text) 70%,var(--bg))':(mode==='dark'?'#a19b90':'#645e54'),
    '--faint':combined?'color-mix(in srgb,var(--text) 55%,var(--bg))':(mode==='dark'?'#6d685f':'#9a9387'),
    '--lime':combined?accent2:(mode==='dark'?'#c9f25a':'#5f8a00')};
  Object.entries(tokens).forEach(([key,value])=>root.style.setProperty(key,value));
  apForm.querySelectorAll('.swatch i[data-color]').forEach(i=>i.style.background=palettes[i.dataset.color][mode][0]);
  apForm.querySelectorAll('[data-combination]').forEach(swatch=>{
    const colors=combinations[swatch.dataset.combination][apForm.mode.value];
    swatch.style.setProperty('--combo-bg',colors.bg); swatch.style.setProperty('--combo-accent',colors.accent); swatch.style.setProperty('--combo-secondary',colors.accent2);
  });
  document.getElementById('accentPickers').hidden=!custom;
  document.getElementById('bgPickers').hidden=bgChoice!=='custom';
  apForm.querySelectorAll('code[data-for]').forEach(c=>c.textContent=apForm[c.dataset.for].value);
}
apForm.addEventListener('input',event=>{
  if(['combination','mode'].includes(event.target.name)) applyCombination();
  else if(['color','accent','accent2','background','bg'].includes(event.target.name)) apForm.combination.value='individual';
  preview(); document.getElementById('apHint').hidden=false;
});
applyCombination(); preview();
</script>
@endpush
