@extends('admin.layout')

@section('title', 'Themes')

@php
    $layouts = config('theme.layouts');
    $sections = config('theme.sections');
@endphp

@push('styles')
<style>
.themes{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:22px}
.theme{position:relative;display:block;cursor:pointer}
.theme input{position:absolute;opacity:0;pointer-events:none}
.theme .box{height:100%;background:var(--surface);border:1.5px solid var(--border);border-radius:22px;padding:14px;transition:border-color .3s,transform .4s var(--ease),box-shadow .3s}
.theme:hover .box{transform:translateY(-3px);border-color:var(--faint)}
.theme input:checked + .box{border-color:var(--accent);box-shadow:0 0 0 4px var(--accent-soft)}
.theme input:focus-visible + .box{box-shadow:0 0 0 4px var(--accent-soft)}
/* mini page preview */
.tp{position:relative;isolation:isolate;overflow:hidden;height:230px;border-radius:12px;border:1px solid var(--border);background:var(--bg);padding:12px;display:flex;flex-direction:column;gap:5px}
.tp::before{content:"";position:absolute;inset:0;z-index:-2;background-image:linear-gradient(var(--grid-line) 1px,transparent 1px),linear-gradient(90deg,var(--grid-line) 1px,transparent 1px);background-size:24px 24px;mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%)}
.tp > .glow{position:absolute;width:200px;height:200px;top:-90px;right:-70px;z-index:-1}
.tp .tp-nav{height:10px;width:70%;margin:0 auto 4px;border-radius:99px;background:var(--surface);border:1px solid var(--border)}
.tp .tp-hero{display:flex;align-items:center;gap:8px;margin-bottom:4px}
.tp .tp-hero b{font-size:.95rem;font-weight:700;letter-spacing:-.03em;line-height:1}
.tp .tp-hero i{margin-left:auto;width:26px;height:30px;background:var(--surface-2);border:1px solid var(--border)}
.tp .tp-sec{flex:1;min-height:0;display:flex;align-items:center;gap:6px;padding:0 8px;background:var(--surface);border:1px solid var(--border);font-size:.6rem;font-weight:600;color:var(--muted);line-height:1}
.tp .tp-sec em{font-style:normal;color:var(--accent);font-weight:700}
.tp .tp-sec:first-of-type{border-color:var(--accent);color:var(--text)}
.theme .meta{padding:14px 6px 4px}
.theme .meta b{display:flex;align-items:center;gap:8px;font-size:1.05rem}
.theme .meta p{color:var(--muted);font-size:.84rem;margin-top:4px}
.theme .meta small{display:block;margin-top:8px;font-size:.72rem;text-transform:uppercase;letter-spacing:1.4px;color:var(--faint)}
.theme .on{display:none;font-size:.66rem;text-transform:uppercase;letter-spacing:1.4px;padding:3px 9px;border-radius:99px;background:var(--accent);color:var(--accent-ink);font-weight:700}
.theme input:checked + .box .on{display:inline-block}
.save-bar{display:flex;align-items:center;gap:18px;flex-wrap:wrap}
.save-bar p{color:var(--muted);font-size:.86rem}
@foreach ($layouts as $key => $layout)
@include('partials.theme-background', ['background' => $layout['background'], 'scope' => '.tp-'.$key])
@endforeach
@media (max-width:1100px){.themes{grid-template-columns:repeat(2,1fr)}}
@media (max-width:640px){.themes{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
  <div class="head">
    <div class="eyebrow">Themes</div>
    <h1>Pick a <span class="serif accent">theme</span></h1>
  </div>

  @if ($errors->any())
    <div class="note bad" role="alert">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.themes.update') }}">
    @csrf
    @method('PUT')

    <div class="themes">
      @foreach ($layouts as $key => $layout)
        <label class="theme">
          <input type="radio" name="layout" value="{{ $key }}" @checked($theme['layout'] === $key)>
          <div class="box">
            <div class="tp tp-{{ $key }}" aria-hidden="true">
              <div class="glow"></div>
              <div class="tp-nav"></div>
              <div class="tp-hero"><b>Anmol Saini<span class="accent">.</span></b><i style="border-radius:{{ $layout['radius'] }}"></i></div>
              @foreach ($layout['order'] as $i => $id)
                <div class="tp-sec" style="border-radius:min({{ $layout['radius'] }},8px)"><em>{{ $i + 1 }}</em>{{ $sections[$id] }}</div>
              @endforeach
            </div>
            <div class="meta">
              <b>{{ $layout['label'] }} <span class="on">Active</span></b>
              <p>{{ $layout['description'] }}</p>
              <small>Background: {{ $layout['background'] }} · Corners: {{ $layout['radius'] }}</small>
            </div>
          </div>
        </label>
      @endforeach
    </div>

    <div class="save-bar">
      <button class="btn btn-primary" type="submit">Apply theme
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </button>
      <p>The hero always stays on top; contact and footer always stay at the bottom.</p>
    </div>
  </form>
@endsection
