@php
    $wa = content('contact.whatsapp');
    $cv = content('site.cv');
@endphp
<!-- HERO -->
<section class="hero" id="home">
  <div class="container">
    <div class="hero-grid">
      <div>
        @if (content('hero.status'))
          <div class="status"><span class="pulse"></span>{{ content('hero.status') }}</div>
        @endif
        <h1>
          <span class="line"><span>{{ content('hero.first_name') }}</span></span>
          <span class="line"><span>{{ content('hero.last_name') }}<span class="serif">.</span></span></span>
        </h1>
        <p class="hero-sub">{{ rich(content('hero.sub')) }}</p>
        <div class="role"><span class="prompt">~/{{ Str::slug(content('hero.first_name')) ?: 'me' }} $</span> <span id="typed"></span><span class="cursor"></span></div>
        <div class="btns">
          <a href="{{ $onHome ? '#projects' : url('projects') }}" class="btn btn-primary magnetic">{{ content('hero.primary_button') }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
          @if ($cv)
            <a href="{{ media($cv) }}" download class="btn btn-ghost magnetic">{{ content('hero.cv_button') }}
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></a>
          @endif
        </div>
        <div class="hero-social"><span>{{ content('labels.hero_social') }}</span>
          @if (content('contact.linkedin'))
            <a href="{{ content('contact.linkedin') }}" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor">@include('site.icons.linkedin')</svg></a>
          @endif
          @if (content('contact.instagram'))
            <a href="{{ content('contact.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">@include('site.icons.instagram')</svg></a>
          @endif
          @if ($wa)
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor">@include('site.icons.whatsapp')</svg></a>
          @endif
        </div>
      </div>
      <div class="hero-visual reveal">
        <div class="spin-badge">
          <svg viewBox="0 0 120 120"><defs><path id="circ" d="M60,60 m-47,0 a47,47 0 1,1 94,0 a47,47 0 1,1 -94,0"/></defs><text><textPath href="#circ">{{ content('hero.badge') }}</textPath></text></svg>
          <svg class="star" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2.6 9.4L24 12l-9.4 2.6L12 24l-2.6-9.4L0 12l9.4-2.6z"/></svg>
        </div>
        <div class="photo-card">
          <img src="{{ media(content('site.photo')) }}" alt="Portrait of {{ content('site.name') }}">
          <div class="ph-tag"><div><b>{{ content('hero.photo_name') }}</b><span>{{ content('hero.photo_role') }}</span></div><span class="dotc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg></span></div>
        </div>
      </div>
    </div>
    <div class="hero-foot">
      <div class="hero-stats">
        @foreach (\App\Support\Content::stats('hero.stats') as [$number, $suffix, $label])
          <div class="stat"><b><span class="count" data-to="{{ $number }}">0</span>@if ($suffix)<sup>{{ $suffix }}</sup>@endif</b><span>{{ $label }}</span></div>
        @endforeach
      </div>
      @if (content('labels.scroll_hint'))
        <a href="#about" class="scroll-hint"><i></i>{{ content('labels.scroll_hint') }}</a>
      @endif
    </div>
  </div>
</section>
