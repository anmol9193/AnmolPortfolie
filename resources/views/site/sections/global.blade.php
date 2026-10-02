@php
    // Dot positions on the 440x440 globe. Countries take them in list order (max 8);
    // the first country is the home base and a dashed route runs from it to every other dot.
    $spots = [[300, 190], [150, 150], [270, 320], [120, 285], [235, 85], [345, 95], [185, 365], [85, 205]];

    $dots = items('countries')->take(count($spots))->values()->map(function ($country, $i) use ($spots) {
        [$x, $y] = $spots[$i];
        $left = $x < 220;

        return [
            'x' => $x,
            'y' => $y,
            'name' => $country->field('city') ?: $country->field('name'),
            // label sits outside the dot, away from the centre of the globe
            'lx' => $left ? $x - 16 : $x + 16,
            'ly' => $y + 5,
            'anchor' => $left ? 'end' : 'start',
        ];
    });

    $home = $dots->first();
@endphp
<!-- GLOBAL REACH -->
<section id="global" style="padding-top:40px">
  <div class="container">
    @include('site.sections._head', ['group' => 'global'])
    <div class="global">
      <div class="globe-wrap reveal">
        <svg viewBox="0 0 440 440" fill="none">
          <circle cx="220" cy="220" r="200" stroke="var(--border)"/>
          <g class="globe-rot" stroke="var(--border)">
            <ellipse cx="220" cy="220" rx="200" ry="70"/><ellipse cx="220" cy="220" rx="200" ry="140"/>
            <ellipse cx="220" cy="220" rx="70" ry="200"/><ellipse cx="220" cy="220" rx="140" ry="200"/>
            <line x1="20" y1="220" x2="420" y2="220"/><line x1="220" y1="20" x2="220" y2="420"/>
          </g>
          @foreach ($dots->skip(1) as $dot)
            {{-- curved route from the home base: the control point is pushed sideways from the middle --}}
            <path d="M{{ $home['x'] }} {{ $home['y'] }} Q {{ round(($home['x'] + $dot['x']) / 2 + ($home['y'] - $dot['y']) * .3) }} {{ round(($home['y'] + $dot['y']) / 2 + ($dot['x'] - $home['x']) * .3) }} {{ $dot['x'] }} {{ $dot['y'] }}" stroke="var(--accent)" stroke-width="1.5" stroke-dasharray="5 6"><animate attributeName="stroke-dashoffset" from="0" to="-44" dur="2s" repeatCount="indefinite"/></path>
          @endforeach
          <g font-family="Poppins, sans-serif" font-size="13" fill="var(--text)">
            @foreach ($dots as $i => $dot)
              <circle cx="{{ $dot['x'] }}" cy="{{ $dot['y'] }}" r="7" fill="var(--accent)"/><circle cx="{{ $dot['x'] }}" cy="{{ $dot['y'] }}" r="16" stroke="var(--accent)" opacity=".5"><animate attributeName="r" values="8;22;8" dur="2.6s" begin="{{ round($i * .5, 1) }}s" repeatCount="indefinite"/></circle><text x="{{ $dot['lx'] }}" y="{{ $dot['ly'] }}" text-anchor="{{ $dot['anchor'] }}">{{ $dot['name'] }}</text>
            @endforeach
          </g>
        </svg>
      </div>
      <div class="countries">
        @foreach (items('countries') as $country)
          <div class="card country reveal">
            <div class="code">{{ $country->field('code') }}</div>
            <div><h4>{{ $country->field('name') }}</h4><p>{{ $country->field('description') }}</p></div>
            @if ($country->field('label'))
              <div class="cnt">{{ $country->field('label') }}</div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
