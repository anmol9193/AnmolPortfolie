@extends('admin.layout')

@section('title', 'Page content')

@section('content')
  <div class="head">
    <div class="eyebrow">Page content</div>
    <h1>Texts, photo &amp; <span class="serif accent">CV</span></h1>
  </div>

  <div class="tabs">
    @foreach ($groups as $key => $g)
      <a href="{{ route('admin.content', $key) }}" @class(['active' => $key === $group])>{{ $g['label'] }}</a>
    @endforeach
  </div>

  <form class="card" method="POST" action="{{ route('admin.content.update', $group) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @if ($errors->any())
      <div class="note bad" role="alert">Please fix the highlighted fields.</div>
    @endif

    <div class="form-grid">
      @foreach ($fields as $key => $field)
        @php
            [$label, $type] = $field;
            $hint = $field[3] ?? null;
            $value = old($key, content("$group.$key"));
        @endphp
        <div @class(['fld', 'wide' => in_array($type, ['textarea', 'lines', 'image', 'file', 'favicon'])])>
          <label for="f-{{ $key }}">{{ $label }}</label>

          @if ($type === 'textarea' || $type === 'lines')
            <textarea id="f-{{ $key }}" name="{{ $key }}" rows="{{ $type === 'lines' ? 5 : 3 }}">{{ $value }}</textarea>
          @elseif ($type === 'favicon')
            @if (content("$group.$key"))
              <div class="thumb">
                <img src="{{ media(content("$group.$key")) }}" alt="" style="width:48px;height:48px;object-fit:contain;padding:6px">
                <label class="check"><input type="checkbox" name="remove_{{ $key }}" value="1"> Remove and use my logo letters</label>
              </div>
            @endif
            <input id="f-{{ $key }}" type="file" name="{{ $key }}" accept=".png,.ico,.jpg,.jpeg,.webp,image/png,image/x-icon,image/jpeg,image/webp">
            <span class="hint">PNG, ICO, JPG or WebP, up to 1 MB. Leave empty to keep the current one.</span>
          @elseif ($type === 'image')
            @if (content("$group.$key"))
              <div class="thumb"><img src="{{ media(content("$group.$key")) }}" alt=""><a href="{{ media(content("$group.$key")) }}" target="_blank" rel="noopener">View current</a></div>
            @endif
            <input id="f-{{ $key }}" type="file" name="{{ $key }}" accept="image/jpeg,image/png,image/webp">
            <span class="hint">JPG, PNG or WebP, up to 4 MB. Leave empty to keep the current one.</span>
          @elseif ($type === 'file')
            @if (content("$group.$key"))
              <div class="thumb"><a href="{{ media(content("$group.$key")) }}" target="_blank" rel="noopener">View current file</a></div>
            @endif
            <input id="f-{{ $key }}" type="file" name="{{ $key }}" accept="application/pdf">
            <span class="hint">PDF, up to 8 MB. Leave empty to keep the current one.</span>
          @else
            <input id="f-{{ $key }}" type="text" name="{{ $key }}" value="{{ $value }}">
          @endif

          @if ($hint)
            <span class="hint">{{ $hint }}</span>
          @endif
          @error($key)
            <span class="err">{{ $message }}</span>
          @enderror
        </div>
      @endforeach
    </div>

    <div class="form-foot">
      <button class="btn btn-primary" type="submit">Save changes
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </button>
    </div>
  </form>
@endsection
