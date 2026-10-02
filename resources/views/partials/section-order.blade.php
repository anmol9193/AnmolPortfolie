{{-- Home page only: puts the sections in the order of the theme chosen in the admin panel. --}}
<style>
body{display:flex;flex-direction:column}
body > *{flex:none}
.hero{order:0}
.marquee{order:1}
@foreach ($theme['order'] as $i => $id)
#{{ $id }}{order:{{ ($i + 1) * 10 }}}
@if ($id === 'services')
.marquee.accent-strip{order:{{ ($i + 1) * 10 }}}
@endif
@endforeach
.cta{order:900}
#contact{order:910}
footer{order:990}
</style>
