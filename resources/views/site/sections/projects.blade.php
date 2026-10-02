@php
    // filter tabs are the distinct "Filter tab" names used by the projects, in first-seen order
    $filters = items('projects')->map(fn ($project) => trim($project->field('filter')))->filter()->unique();
@endphp
<!-- PROJECTS (cards are rendered by site/scripts from window.SITE.projects) -->
<section id="projects">
  <div class="container">
    <div class="sec-head">
      <div class="reveal">
        <div class="eyebrow">{{ content('projects.eyebrow') }}</div>
        <h2>{{ rich(content('projects.title')) }}</h2>
      </div>
      @if ($filters->count() > 1)
        <div class="filters reveal">
          <button class="filter active" data-f="all">{{ content('labels.filter_all') }}</button>
          @foreach ($filters as $filter)
            <button class="filter" data-f="{{ Str::slug($filter) }}">{{ $filter }}</button>
          @endforeach
        </div>
      @endif
    </div>
    <div class="proj-slider reveal">
      <div class="swiper" id="projSwiper">
        <div class="swiper-wrapper" id="projGrid"></div>
      </div>
      <div class="proj-nav">
        <button class="proj-arrow proj-prev" aria-label="Previous project"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
        <div class="proj-pag"></div>
        <button class="proj-arrow proj-next" aria-label="Next project"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
      </div>
    </div>
  </div>
</section>
