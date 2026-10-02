{{-- Every public page. $current comes from App\Support\Pages (edited in Admin → Site pages). --}}
@extends('layouts.site', ['page' => $current['key'], 'title' => $current['title'] ?: null])

@if ($current['key'] === 'home')
  @push('head')
  @include('partials.section-order')
  @endpush
@endif

@section('content')
@foreach ($current['sections'] as $section)
@include("site.sections.$section")
@endforeach
@endsection
