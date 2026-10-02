@extends('admin.layout')

@section('title', ($item->exists ? 'Edit ' : 'Add ').$collection['singular'])

@section('content')
  <div class="head">
    <div class="eyebrow">{{ $collection['label'] }}</div>
    <h1>{{ $item->exists ? 'Edit' : 'Add' }} <span class="serif accent">{{ $collection['singular'] }}</span></h1>
  </div>

  <form class="card" method="POST" enctype="multipart/form-data"
        action="{{ $item->exists ? route('admin.items.update', [$type, $item]) : route('admin.items.store', $type) }}">
    @csrf
    @if ($item->exists)
      @method('PUT')
    @endif

    @if ($errors->any())
      <div class="note bad" role="alert">Please fix the highlighted fields.</div>
    @endif

    <div class="form-grid">
      @foreach ($collection['fields'] as $key => $field)
        @php
            [$label, $kind] = $field;
            $required = $field['required'] ?? false;
            $saved = $item->exists ? $item->field($key, '') : ($field['default'] ?? '');
            $value = old($key, $saved);
        @endphp
        <div @class(['fld', 'wide' => in_array($kind, ['textarea', 'image', 'icon'])])>
          @if ($kind === 'checkbox')
            <span class="lbl">&nbsp;</span>
            <label class="check">
              <input type="hidden" name="{{ $key }}" value="0">
              <input type="checkbox" name="{{ $key }}" value="1" @checked($value)> {{ $label }}
            </label>
          @else
            <label for="f-{{ $key }}">{{ $label }} @if ($required)<span class="req">*</span>@endif</label>

            @if ($kind === 'textarea')
              <textarea id="f-{{ $key }}" name="{{ $key }}" rows="4" @required($required)>{{ $value }}</textarea>
            @elseif ($kind === 'select')
              <select id="f-{{ $key }}" name="{{ $key }}">
                @foreach ($field['options'] as $optionKey => $optionLabel)
                  <option value="{{ $optionKey }}" @selected($value === $optionKey)>{{ $optionLabel }}</option>
                @endforeach
              </select>
            @elseif ($kind === 'color')
              <input id="f-{{ $key }}" type="color" name="{{ $key }}" value="{{ $value ?: '#ff5b2e' }}">
            @elseif ($kind === 'icon')
              <div class="icons">
                @foreach (config('icons') as $name => $svg)
                  <label title="{{ $name }}">
                    <input type="radio" name="{{ $key }}" value="{{ $name }}" @checked($value === $name)>
                    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $svg !!}</svg></span>
                  </label>
                @endforeach
              </div>
            @elseif ($kind === 'image')
              @if ($saved)
                <div class="thumb">
                  <img src="{{ media($saved) }}" alt="">
                  <label class="check"><input type="checkbox" name="remove_{{ $key }}" value="1"> Remove this image</label>
                </div>
              @endif
              <input id="f-{{ $key }}" type="file" name="{{ $key }}" accept="image/jpeg,image/png,image/webp">
              <span class="hint">JPG, PNG or WebP, up to 4 MB. Leave empty to keep the current one.</span>
            @else
              <input id="f-{{ $key }}" type="{{ $kind === 'url' ? 'url' : 'text' }}" name="{{ $key }}" value="{{ $value }}" @required($required)>
            @endif
          @endif

          @isset($field['hint'])
            <span class="hint">{{ $field['hint'] }}</span>
          @endisset
          @error($key)
            <span class="err">{{ $message }}</span>
          @enderror
        </div>
      @endforeach

      <div class="fld">
        <label for="f-position">Order</label>
        <input id="f-position" type="number" name="position" min="0" max="9999" value="{{ old('position', $item->position) }}">
        <span class="hint">Lower numbers show first.</span>
        @error('position')
          <span class="err">{{ $message }}</span>
        @enderror
      </div>
    </div>

    <div class="form-foot">
      <button class="btn btn-primary" type="submit">{{ $item->exists ? 'Save changes' : 'Add '.$collection['singular'] }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
      </button>
      <a class="btn" href="{{ route('admin.items.index', $type) }}">Cancel</a>
    </div>
  </form>
@endsection
