<!-- EDUCATION -->
<section id="education" style="padding-top:40px">
  <div class="container">
    @include('site.sections._head', ['group' => 'education'])
    <div class="edu-grid">
      @foreach (items('education') as $edu)
        <div @class(['card', 'edu', 'main' => $edu->field('main'), 'reveal'])>
          <div class="yr"><span>{{ $edu->field('level') }}</span><span>{{ $edu->field('status') }}</span></div>
          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($edu->field('icon'), 'graduation') }}</svg></div>
          <h3>{{ $edu->field('title') }}</h3>
          <p>{{ $edu->field('description') }}</p>
          @if ($edu->field('board'))
            <span class="board">{{ $edu->field('board') }}</span>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
