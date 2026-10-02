@php($pages = \App\Support\Pages::all())
<footer>
  <div class="container">
    <div class="f-big">{{ content('site.name') }}</div>
    <div class="f-bottom">
      <span>{{ str_replace('{year}', date('Y'), content('site.footer')) }}</span>
      <div class="f-links">
        @foreach (['about', 'projects', 'contact'] as $key)
          <a href="{{ url($pages[$key]['path']) }}">{{ $pages[$key]['label'] }}</a>
        @endforeach
        @if (content('site.cv') && content('labels.footer_resume'))
          <a href="{{ media(content('site.cv')) }}" download>{{ content('labels.footer_resume') }}</a>
        @endif
      </div>
    </div>
  </div>
</footer>
