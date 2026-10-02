<!-- EXPERIENCE -->
<section id="experience" style="padding-top:40px">
  <div class="container">
    @include('site.sections._head', ['group' => 'experience'])
    <div class="exp-list">
      @foreach (items('experiences') as $job)
        <div class="exp reveal">
          <div class="exp-date">{{ $job->field('date') }}@if ($job->field('current'))<br><span class="now">{{ content('labels.current_role') }}</span>@endif</div>
          <div>
            <h3>{{ $job->field('title') }}</h3>
            <h4>{{ $job->field('company') }}</h4>
            <p>{{ $job->field('description') }}</p>
            @if ($job->list('tags'))
              <div class="tags">@foreach ($job->list('tags') as $tag)<span class="tag">{{ $tag }}</span>@endforeach</div>
            @endif
          </div>
          <div class="exp-loc">{{ $job->field('location') }}</div>
          <div class="exp-big">{{ $job->field('year') }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>
