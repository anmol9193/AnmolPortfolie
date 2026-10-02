{{--
  Public site layout. Each page extends it with:
    @extends('layouts.site', ['page' => 'about', 'title' => 'About'])
  "page" marks the active nav link ("home" for the index page).
--}}
@php
    $page ??= 'home';
    $onHome = $page === 'home';
    $name = content('site.name');
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $theme['mode'] }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.favicon')
<title>{{ isset($title) ? $title.' — '.$name : $name.' — '.content('site.tagline') }}</title>
<meta name="description" content="{{ content('site.description') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
@include('site.styles')
<style>
.hero-sub strong{color:var(--text);font-weight:600}
/* uploaded skill logos */
.sk .sk-logo{width:28px;height:28px;object-fit:contain;flex:none;transition:transform .45s var(--ease)}
.sk:hover .sk-logo{transform:scale(1.2) rotate(-8deg)}
.m-item img{height:2rem;width:auto;max-width:3rem;object-fit:contain}
/* contact form result */
.form-note{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-radius:14px;margin-bottom:18px;font-size:.92rem;font-weight:500;border:1px solid var(--accent);background:var(--accent-soft);color:var(--text)}
.form-note svg{width:20px;height:20px;flex:none;margin-top:2px;color:var(--accent)}
.form-note.ok{border-color:var(--lime);background:color-mix(in srgb,var(--lime) 12%,transparent)}
.form-note.ok svg{color:var(--lime)}
@unless ($onHome)
/* inner pages: the first section sits right under the fixed header */
nav + section:not(.hero){padding-top:170px!important}
nav + :not(section){margin-top:118px}
@endunless
</style>
@include('partials.theme')
@include('partials.theme-layout')
@stack('head')
</head>
<body class="loading">

<!-- PRELOADER -->
<div id="loader">
  <div class="l-name"><span>{{ $name }}</span><span class="accent">.</span></div>
  <div class="l-bar"><i id="lBar"></i></div>
  <div class="l-count" id="lCount">0%</div>
</div>

<div class="cur-dot" id="curDot"></div><div class="cur-ring" id="curRing"></div>
<div id="progress"></div>
<div class="glow"></div>

@include('site.nav')

@yield('content')

@include('site.footer')

<button class="icon-btn" id="toTop" aria-label="Back to top"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@include('site.scripts')
</body>
</html>
