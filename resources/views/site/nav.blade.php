@php
    $pages = \App\Support\Pages::all();
    $contact = $pages->get('contact');
@endphp
<!-- NAV -->
<nav>
  <div class="nav-inner">
    <a href="{{ $onHome ? '#home' : url('/') }}" class="logo"><b>{{ content('site.logo_mark') }}</b>{{ content('site.logo_text') }}</a>
    <ul class="nav-links" id="navLinks">
      @foreach ($pages->where('nav', true) as $link)
        <li><a href="{{ url($link['path']) }}" @class(['active' => $page === $link['key']])>{{ $link['label'] }}</a></li>
      @endforeach
    </ul>
    <div class="nav-actions">
      {{-- the header button always points to the Contact page and carries its menu name --}}
      <a href="{{ url($contact['path']) }}" class="hire">{{ $contact['label'] }}</a>
      <button class="icon-btn menu-btn" id="menuBtn" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 8h16M4 16h16"/></svg>
      </button>
    </div>
  </div>
</nav>
