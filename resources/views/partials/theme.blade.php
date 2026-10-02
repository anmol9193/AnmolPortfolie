{{-- Appearance chosen in the admin panel; overrides the page's default tokens and backdrop. --}}
<style>
html[data-theme]{--accent:{{ $theme['accent'] }};--accent-2:{{ $theme['accent2'] }};--accent-ink:{{ $theme['ink'] }};--accent-soft:{{ $theme['soft'] }};--radius:{{ $theme['radius'] }}}
@if ($theme['bg'])
{{-- custom background: the surface shades are derived from it (towards white on dark, both ways on light) --}}
@if ($theme['mode'] === 'dark')
html[data-theme]{--bg:{{ $theme['bg'] }};--bg-2:color-mix(in srgb,{{ $theme['bg'] }} 96%,#fff);--surface:color-mix(in srgb,{{ $theme['bg'] }} 93%,#fff);--surface-2:color-mix(in srgb,{{ $theme['bg'] }} 88%,#fff);--border:color-mix(in srgb,{{ $theme['bg'] }} 81%,#fff);--nav-bg:color-mix(in srgb,{{ $theme['bg'] }} 68%,transparent)}
@else
html[data-theme]{--bg:{{ $theme['bg'] }};--bg-2:color-mix(in srgb,{{ $theme['bg'] }} 96%,#000);--surface:color-mix(in srgb,{{ $theme['bg'] }} 45%,#fff);--surface-2:color-mix(in srgb,{{ $theme['bg'] }} 92%,#000);--border:color-mix(in srgb,{{ $theme['bg'] }} 85%,#000);--nav-bg:color-mix(in srgb,{{ $theme['bg'] }} 55%,rgba(255,255,255,.55))}
@endif
@endif
@include('partials.theme-background', ['background' => $theme['background'], 'scope' => 'html body'])
</style>
