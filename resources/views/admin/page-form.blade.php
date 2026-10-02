@extends('admin.layout')

@section('title', 'Edit '.$current['label'].' page')

@php
    $checked = old('sections', $current['sections']);
    $isHome = $current['key'] === 'home';
@endphp

@section('content')
  <div class="head">
    <div class="eyebrow">Site pages</div>
    <h1>Edit <span class="serif accent">{{ $current['label'] }}</span> page</h1>
  </div>

  <form class="card" method="POST" action="{{ route('admin.pages.update', $current['key']) }}">
    @csrf
    @method('PUT')

    <div class="form-grid">
      <div class="fld">
        <label for="f-label">Menu name <span class="req">*</span></label>
        <input id="f-label" type="text" name="label" value="{{ old('label', $current['label']) }}" required maxlength="40">
        <span class="hint">Shown in the header menu and footer links.</span>
        @error('label')<span class="err">{{ $message }}</span>@enderror
      </div>

      <div class="fld">
        <label for="f-title">Browser title</label>
        <input id="f-title" type="text" name="title" value="{{ old('title', $current['title']) }}" maxlength="80">
        <span class="hint">{{ $isHome ? 'Leave empty to use your name and tagline.' : 'The tab title, followed by your name.' }}</span>
        @error('title')<span class="err">{{ $message }}</span>@enderror
      </div>

      <div class="fld wide">
        <label class="check">
          <input type="hidden" name="nav" value="0">
          <input type="checkbox" name="nav" value="1" @checked(old('nav', $current['nav']))> Show this page in the header menu
        </label>
        <span class="hint">Address: {{ url($current['path']) }}</span>
      </div>

      <div class="fld wide">
        <span class="lbl">Sections on this page <span class="req">*</span></span>
        <div class="checks">
          @foreach ($sections as $key => $label)
            <label class="check"><input type="checkbox" name="sections[]" value="{{ $key }}" @checked(in_array($key, $checked))> {{ $label }}</label>
          @endforeach
        </div>
        <span class="hint">{{ $isHome ? 'On the home page the order of the middle sections comes from the active theme.' : 'Sections always appear in the order listed here.' }}</span>
        @error('sections')<span class="err">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="form-foot">
      <button class="btn btn-primary" type="submit">Save changes
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </button>
      <a class="btn" href="{{ route('admin.pages') }}">Cancel</a>
    </div>
  </form>
@endsection
