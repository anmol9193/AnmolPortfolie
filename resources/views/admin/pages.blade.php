@extends('admin.layout')

@section('title', 'Site pages')

@section('content')
  <div class="head">
    <div class="eyebrow">Site pages</div>
    <h1>Your public <span class="serif accent">pages</span></h1>
  </div>

  <div class="card">
    <div class="card-head"><h2>All pages</h2><span>{{ $pages->count() }} pages</span></div>
    <ul class="list">
      @foreach ($pages->values() as $i => $page)
        <li>
          <span class="pos">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
          <span class="info">
            <b>{{ $page['label'] }} @if ($page['nav'])<span class="badge" style="margin-left:8px">In menu</span>@endif</b>
            <small>{{ $page['path'] }} · {{ collect($page['sections'])->map(fn ($s) => Str::before(config("site.sections.$s"), ' ('))->implode(', ') }}</small>
          </span>
          <span class="acts">
            <a class="sm" href="{{ url($page['path']) }}" target="_blank" rel="noopener">View</a>
            <a class="sm" href="{{ route('admin.pages.edit', $page['key']) }}">Edit</a>
          </span>
        </li>
      @endforeach
    </ul>
  </div>
@endsection
