@extends('admin.layout')

@section('title', $collection['label'])

@php
    $imageField = collect($collection['fields'])->search(fn ($field) => $field[1] === 'image');
@endphp

@section('content')
  <div class="head head-row">
    <div>
      <div class="eyebrow">{{ $collection['label'] }}</div>
      <h1>Your <span class="serif accent">{{ Str::lower($collection['label']) }}</span></h1>
    </div>
    <a href="{{ route('admin.items.create', $type) }}" class="btn btn-primary">Add {{ $collection['singular'] }}
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg></a>
  </div>


  <div class="card">
    <div class="card-head"><h2>All {{ Str::lower($collection['label']) }}</h2><span>{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</span></div>

    @if ($items->isEmpty())
      <p class="empty">Nothing here yet. Add your first {{ $collection['singular'] }}.</p>
    @else
      <ul class="list">
        @foreach ($items as $item)
          <li>
            <span class="pos">{{ str_pad($item->position, 2, '0', STR_PAD_LEFT) }}</span>
            @if ($imageField)
              @if ($item->field($imageField))
                <img src="{{ media($item->field($imageField)) }}" alt="">
              @else
                <span class="ph" style="background:linear-gradient(145deg,{{ $item->field('color_1', 'var(--surface-2)') }},{{ $item->field('color_2', 'var(--border)') }})"></span>
              @endif
            @endif
            <span class="info">
              <b>{{ $item->field($collection['title']) ?: 'Untitled' }}</b>
              <small>{{ $item->field($collection['subtitle']) }}</small>
            </span>
            <span class="acts">
              <a class="sm" href="{{ route('admin.items.edit', [$type, $item]) }}">Edit</a>
              <form method="POST" action="{{ route('admin.items.destroy', [$type, $item]) }}" data-confirm="Delete this {{ $collection['singular'] }}?" data-confirm-text="“{{ $item->field($collection['title']) ?: 'Untitled' }}” will be removed from the site. This cannot be undone." data-confirm-button="Delete">
                @csrf
                @method('DELETE')
                <button class="sm danger" type="submit">Delete</button>
              </form>
            </span>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
@endsection
