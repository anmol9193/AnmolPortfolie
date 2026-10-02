<ul class="pages">
  @foreach ($pages->values() as $i => $page)
    <li>
      <a href="{{ url($page['path']) }}" target="_blank" rel="noopener">
        <span class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
        <span class="info"><b>{{ $page['label'] }}</b><small>{{ collect($page['sections'])->map(fn ($s) => Str::before(config("site.sections.$s"), ' ('))->implode(', ') }}</small></span>
        <span class="path">{{ $page['path'] }}</span>
        <span class="go"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg></span>
      </a>
    </li>
  @endforeach
</ul>
