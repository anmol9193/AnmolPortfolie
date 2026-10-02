<!-- PROCESS -->
<section id="process" style="padding-top:40px">
  <div class="container">
    @include('site.sections._head', ['group' => 'process'])
    <div class="process">
      @foreach (items('process') as $i => $step)
        <div class="step reveal">
          <div class="dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($step->field('icon'), 'code') }}</svg></div>
          <div class="k">{{ content('labels.step') }} {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div><h3>{{ $step->field('title') }}</h3><p>{{ $step->field('description') }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
