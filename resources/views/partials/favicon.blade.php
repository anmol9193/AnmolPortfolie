{{--
  Browser tab icon. Uses the favicon uploaded in Admin → Page content → Site & files;
  until one is uploaded, a small badge with the logo letters in the accent color is drawn.
--}}
@php
    $favicon = content('site.favicon');

    if (! $favicon) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="16" fill="'.$theme['accent'].'"/>'
            .'<text x="32" y="43" text-anchor="middle" font-family="Poppins,Arial,sans-serif" font-size="27" font-weight="700" fill="'.$theme['ink'].'">'
            .e(mb_substr(content('site.logo_mark'), 0, 2)).'</text></svg>';
    }
@endphp
@if ($favicon)
<link rel="icon" href="{{ media($favicon) }}">
<link rel="apple-touch-icon" href="{{ media($favicon) }}">
@else
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,{{ rawurlencode($svg) }}">
@endif
