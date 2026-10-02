@extends('admin.layout')

@section('title', 'Dashboard')

@php
    use App\Models\PortfolioItem;

    $user = auth()->user();
    $pages = \App\Support\Pages::all();
    $collections = config('content.collections');
    $counts = PortfolioItem::selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');
    $totalItems = $counts->sum();
    $maxCount = max(1, $counts->max() ?? 1);
    $recent = PortfolioItem::whereIn('type', array_keys($collections))->latest('updated_at')->take(6)->get();
    $layout = config('theme.layouts.'.$theme['layout']);

    // icon (inside of a 24x24 stroke svg) for each collection tile
    $tileIcons = [
        'projects' => 'layout', 'experiences' => 'briefcase', 'education' => 'graduation', 'services' => 'server',
        'skills' => 'code', 'process' => 'rocket', 'countries' => 'globe',
    ];
@endphp

@push('styles')
<style>
/* ---------- welcome banner ---------- */
.banner{position:relative;isolation:isolate;overflow:hidden;display:flex;align-items:center;gap:28px;padding:34px 36px;border-radius:calc(var(--radius) + 6px);background:linear-gradient(120deg,var(--accent),var(--accent-2));color:var(--accent-ink);margin-bottom:18px;animation:rise .7s var(--ease) both}
.banner::before,.banner::after{content:"";position:absolute;z-index:-1;border-radius:50%;border:1px solid currentColor;opacity:.22}
.banner::before{width:420px;height:420px;right:-120px;top:-230px}
.banner::after{width:300px;height:300px;right:120px;bottom:-220px}
.banner .dots{position:absolute;inset:0;z-index:-1;opacity:.18;background-image:radial-gradient(currentColor 1.2px,transparent 1.6px);background-size:18px 18px;mask-image:linear-gradient(90deg,transparent,#000 70%);-webkit-mask-image:linear-gradient(90deg,transparent,#000 70%)}
.banner .who{flex:none;width:132px;height:132px;border-radius:36px;overflow:hidden;background:rgba(255,255,255,.2);box-shadow:0 0 0 4px color-mix(in srgb,currentColor 22%,transparent);display:grid;place-items:center;font-size:2.4rem;font-weight:800}
.banner .who img{width:100%;height:100%;object-fit:cover;object-position:center 15%}
.banner .txt{flex:1;min-width:0}
.banner .day{font-size:.74rem;font-weight:600;text-transform:uppercase;letter-spacing:2px;opacity:.8}
.banner h1{font-size:clamp(1.7rem,3.2vw,2.5rem);font-weight:700;letter-spacing:-.03em;line-height:1.12;margin:6px 0 8px}
.banner h1 .serif{font-weight:400}
.banner p{opacity:.88;font-size:.95rem;max-width:520px}

/* ---------- collection tiles ---------- */
.tiles{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
.tile{position:relative;overflow:hidden;display:block;padding:18px 16px;border-radius:var(--radius);background:var(--surface);border:1px solid var(--border);transition:transform .4s var(--ease),border-color .3s;animation:rise .7s var(--ease) both}
.tile::after{content:"";position:absolute;right:-26px;top:-26px;width:90px;height:90px;border-radius:50%;background:var(--accent-soft);transition:transform .5s var(--ease)}
.tile:hover{transform:translateY(-4px);border-color:var(--accent)}
.tile:hover::after{transform:scale(1.6)}
.tile > *{position:relative;z-index:1}
.tile .ic{width:40px;height:40px;border-radius:12px;background:var(--accent);color:var(--accent-ink);display:grid;place-items:center;margin-bottom:16px}
.tile .ic svg{width:19px;height:19px}
.tile b{display:block;font-size:2rem;font-weight:700;letter-spacing:-.03em;line-height:1}
.tile span{display:block;color:var(--muted);font-size:.85rem;margin-top:4px}
.tile em{position:absolute;right:16px;bottom:16px;font-style:normal;color:var(--faint);transition:.3s var(--ease)}
.tile em svg{width:16px;height:16px;display:block}
.tile:hover em{color:var(--accent);transform:translate(2px,-2px)}

/* ---------- lower grid ---------- */
.dash{display:grid;grid-template-columns:1.45fr 1fr;gap:18px;align-items:start}
.bars{list-style:none;display:grid;gap:14px}
.bars li{display:grid;grid-template-columns:110px 1fr 28px;align-items:center;gap:12px;font-size:.86rem}
.bars span{color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bars i{display:block;height:10px;border-radius:99px;background:var(--surface-2);overflow:hidden}
.bars i::before{content:"";display:block;height:100%;width:var(--w);border-radius:inherit;background:linear-gradient(90deg,var(--accent),var(--accent-2));transform-origin:left;animation:grow 1.1s var(--ease) both;animation-delay:var(--d,0s)}
.bars b{text-align:right;font-weight:600}
@keyframes grow{from{transform:scaleX(0)}}
.feed{list-style:none}
.feed li + li{border-top:1px solid var(--border)}
.feed a{display:flex;align-items:center;gap:14px;padding:12px;margin:0 -12px;border-radius:14px;transition:background .25s}
.feed a:hover{background:var(--surface-2)}
.feed .ic{flex:none;width:38px;height:38px;border-radius:12px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center}
.feed .ic svg{width:18px;height:18px}
.feed .info{flex:1;min-width:0}
.feed .info b{display:block;font-weight:600;font-size:.92rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.feed .info small{display:block;color:var(--muted);font-size:.78rem}
.feed time{flex:none;font-size:.76rem;color:var(--faint);white-space:nowrap}
/* look & feel */
.look{display:flex;align-items:center;gap:16px;margin-bottom:16px}
.look .sw{display:flex;flex:none}
.look .sw i{width:44px;height:44px;border-radius:50%;border:3px solid var(--surface);margin-left:-12px;box-shadow:0 0 0 1px var(--border)}
.look .sw i:first-child{margin-left:0}
.look b{display:block;font-size:1.05rem;line-height:1.25}
.look small{color:var(--muted);font-size:.84rem;text-transform:capitalize}
.pills{display:flex;flex-wrap:wrap;gap:8px}
.pills a{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:99px;border:1px solid var(--border);background:var(--bg-2);font-size:.82rem;font-weight:600;color:var(--muted);transition:.25s var(--ease)}
.pills a:hover{border-color:var(--accent);color:var(--accent)}
.pills a i{width:7px;height:7px;border-radius:50%;background:var(--faint)}
.pills a.on i{background:var(--lime)}
.sys{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.sys div{padding:14px;border-radius:14px;background:var(--bg-2);border:1px solid var(--border);min-width:0}
.sys small{display:block;font-size:.68rem;text-transform:uppercase;letter-spacing:1.4px;color:var(--muted)}
.sys b{display:block;font-size:.92rem;margin-top:4px;overflow-wrap:anywhere}

@media (max-width:1100px){.dash{grid-template-columns:1fr}}
@media (max-width:760px){
  .banner{flex-direction:column;align-items:flex-start;padding:26px 22px}
  .banner .who{width:104px;height:104px;border-radius:30px}
  .bars li{grid-template-columns:86px 1fr 24px}
}
</style>
@endpush

@section('content')
  <div class="banner">
    <span class="dots"></span>
    <div class="who">
      {{-- the admin's own profile picture (Account), else their initials --}}
      @if ($user->avatar)
        <img src="{{ media($user->avatar) }}" alt="">
      @else
        {{ collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
      @endif
    </div>
    <div class="txt">
      <div class="day" id="today">Dashboard</div>
      <h1><span id="greet">Welcome</span>, <span class="serif">{{ strtok($user->name, ' ') }}</span>.</h1>
      <p>Your portfolio has {{ $totalItems }} pieces of content across {{ $pages->count() }} pages. Jump straight into what you want to change.</p>
    </div>
  </div>

  <div class="tiles">
    @foreach (collect($collections)->only(['projects', 'experiences', 'education']) as $key => $collection)
      <a class="tile" href="{{ route('admin.items.index', $key) }}" style="animation-delay:{{ $loop->index * .05 }}s">
        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($tileIcons[$key] ?? 'star') }}</svg></div>
        <b>{{ $counts[$key] ?? 0 }}</b>
        <span>{{ $collection['label'] }}</span>
        <em><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg></em>
      </a>
    @endforeach
  </div>

  <div class="dash">
    <div class="stack">
      <div class="card" style="animation-delay:.2s">
        <div class="card-head"><h2>Recently updated</h2><span>Latest {{ $recent->count() }}</span></div>
        @if ($recent->isEmpty())
          <p class="empty">Nothing yet. Add your first project to see it here.</p>
        @else
          <ul class="feed">
            @foreach ($recent as $item)
              @php($collection = $collections[$item->type])
              <li>
                <a href="{{ route('admin.items.edit', [$item->type, $item]) }}">
                  <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{{ icon($tileIcons[$item->type] ?? 'star') }}</svg></span>
                  <span class="info"><b>{{ $item->field($collection['title']) ?: 'Untitled' }}</b><small>{{ ucfirst($collection['singular']) }}</small></span>
                  <time datetime="{{ $item->updated_at?->toIso8601String() }}">{{ $item->updated_at?->diffForHumans() }}</time>
                </a>
              </li>
            @endforeach
          </ul>
        @endif
      </div>

      <div class="card" style="animation-delay:.28s">
        <div class="card-head"><h2>Content overview</h2><span>{{ $totalItems }} items</span></div>
        <ul class="bars">
          @foreach ($collections as $key => $collection)
            <li><span>{{ $collection['label'] }}</span><i style="--w:{{ round(($counts[$key] ?? 0) / $maxCount * 100) }}%;--d:{{ $loop->index * .08 }}s"></i><b>{{ $counts[$key] ?? 0 }}</b></li>
          @endforeach
        </ul>
      </div>
    </div>

    <div class="stack">
      <div class="card" style="animation-delay:.24s">
        <div class="card-head"><h2>Look &amp; feel</h2><a href="{{ route('admin.appearance') }}">Change</a></div>
        <div class="look">
          <div class="sw"><i style="background:var(--accent)"></i><i style="background:var(--accent-2)"></i><i style="background:var(--bg)"></i></div>
          <div><b>{{ $theme['color_label'] }} · {{ $layout['label'] }}</b><small>{{ $theme['mode'] }} mode · {{ $theme['bg_choice'] === 'default' ? 'default' : (config('theme.backgrounds.'.$theme['bg_choice'].'.label') ?? 'custom') }} background</small></div>
        </div>
        <div class="pills">
          <a href="{{ route('admin.themes') }}">Theme: {{ $layout['label'] }}</a>
          <a href="{{ route('admin.appearance') }}">Colors</a>
        </div>
      </div>

      <div class="card" style="animation-delay:.32s">
        <div class="card-head"><h2>Pages</h2><a href="{{ route('admin.pages') }}">Manage</a></div>
        <div class="pills">
          @foreach ($pages as $page)
            <a href="{{ route('admin.pages.edit', $page['key']) }}" @class(['on' => $page['nav'] || $page['key'] === 'home']) title="{{ count($page['sections']) }} sections"><i></i>{{ $page['label'] }}</a>
          @endforeach
        </div>
        <p class="hint">Green dot: shown in the header menu.</p>
      </div>

      <div class="card" style="animation-delay:.4s">
        <div class="card-head"><h2>System</h2><span>{{ app()->environment() }}</span></div>
        <div class="sys">
          <div><small>Laravel</small><b>{{ app()->version() }}</b></div>
          <div><small>PHP</small><b>{{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}.{{ PHP_RELEASE_VERSION }}</b></div>
          <div><small>Database</small><b>{{ config('database.connections.'.config('database.default').'.database') }}</b></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
// ---------- Greeting and date by local time ----------
const now=new Date(), h=now.getHours();
document.getElementById('greet').textContent=h<12?'Good morning':h<17?'Good afternoon':'Good evening';
document.getElementById('today').textContent=now.toLocaleDateString(undefined,{weekday:'long',day:'numeric',month:'long',year:'numeric'});
</script>
@endpush
