@php
    $groups = items('skills')->groupBy(fn ($skill) => $skill->field('group') ?: 'Skills');
@endphp
<!-- SKILLS -->
<section id="skills">
  <div class="container">
    @include('site.sections._head', ['group' => 'skills'])
    <div class="skill-groups">
      @foreach ($groups as $group => $skills)
        {{-- an odd last card spans the full row --}}
        <div class="card sg reveal" @if ($loop->last && $loop->odd) style="grid-column:1/-1" @endif>
          <div class="sg-head"><h3>{{ $group }}</h3><span>{{ $skills->count() }} {{ content('labels.tools') }}</span></div>
          <div class="sk-list">
            @foreach ($skills as $skill)
              <div class="sk">
                @if ($skill->field('logo'))
                  <img class="sk-logo" src="{{ media($skill->field('logo')) }}" alt="">
                @elseif ($skill->field('devicon'))
                  <i class="devicon-{{ $skill->field('devicon') }} colored"></i>
                @else
                  <svg class="svg-i" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon('devices') }}</svg>
                @endif
                {{ $skill->field('name') }}
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
