{{-- Section heading. $group is the content group, e.g. "services". --}}
<div class="sec-head">
  <div class="reveal">
    <div class="eyebrow">{{ content("$group.eyebrow") }}</div>
    <h2>{{ rich(content("$group.title")) }}</h2>
  </div>
  @if (content("$group.sub"))
    <p class="reveal">{{ content("$group.sub") }}</p>
  @endif
</div>
