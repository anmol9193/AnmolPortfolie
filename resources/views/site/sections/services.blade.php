<!-- SERVICES -->
<section id="services" style="padding-top:40px">
  <div class="container">
    @include('site.sections._head', ['group' => 'services'])
    <div class="services">
      @foreach (items('services') as $i => $service)
        <div class="card svc reveal">
          <span class="n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($service->field('icon'), 'code') }}</svg></div>
          <h3>{{ $service->field('title') }}</h3>
          <p>{{ $service->field('description') }}</p>
          @if ($service->field('stack'))
            <div class="stack">{{ $service->field('stack') }}</div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
