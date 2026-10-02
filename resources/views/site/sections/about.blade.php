@php
    $statIcons = ['clock', 'code', 'star', 'bolt'];
    $chipIcons = ['bolt', 'clock', 'ball', 'plane', 'star'];
@endphp
<!-- ABOUT (BENTO) -->
<section id="about">
  <div class="container">
    @include('site.sections._head', ['group' => 'about'])
    <div class="bento">
      <div class="card b-intro reveal">
        <h3>{{ rich(content('about.intro')) }}</h3>
        <div>
          @foreach (['about.text_1', 'about.text_2'] as $key)
            @if (content($key))
              <p>{{ rich(content($key)) }}</p>
            @endif
          @endforeach
        </div>
        <div class="b-sign"><img src="{{ media(content('site.photo')) }}" alt=""><div><b>{{ content('site.name') }}</b><span>{{ content('about.sign_role') }}</span></div></div>
      </div>
      <a href="{{ $onHome ? '#experience' : url('experience') }}" class="card b-role reveal">
        <span class="arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg></span>
        <small>{{ content('about.role_label') }}</small>
        <div><h4>{{ content('about.role_company') }}</h4><p>{{ content('about.role_text') }}</p></div>
      </a>
      @foreach (\App\Support\Content::stats('about.stats') as $i => [$number, $suffix, $label])
        <div class="card b-stat reveal">
          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($statIcons[$i % count($statIcons)]) }}</svg></div>
          <div><b>{{ $number }}@if ($suffix)<sup>{{ $suffix }}</sup>@endif</b><span>{{ $label }}</span></div>
        </div>
      @endforeach
      <div class="card b-loc reveal">
        <div class="pin"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <div><small>{{ content('about.location_label') }}</small><h4>{{ content('about.location') }}</h4><p>{{ content('about.location_text') }}</p></div>
      </div>
      <div class="card b-hob reveal">
        <small>{{ content('about.hobbies_label') }}</small>
        <div class="chips">
          @foreach (\App\Support\Content::lines('about.hobbies') as $i => $hobby)
            <span class="chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($chipIcons[$i % count($chipIcons)]) }}</svg>{{ $hobby }}</span>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
