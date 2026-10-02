{{--
  Backdrop CSS for one theme background. $scope is the element that owns the ::before backdrop
  ("html body" for real pages, a preview box selector in the admin theme picker).
  "grid" is the default backdrop already defined by each page, so it needs nothing here.
--}}
@switch($background)
    @case('aurora')
{{ $scope }}::before{background-image:radial-gradient(ellipse 70% 45% at 50% -8%,color-mix(in srgb,var(--accent) 13%,transparent),transparent 72%),radial-gradient(ellipse 45% 35% at 100% 100%,color-mix(in srgb,var(--accent-2) 7%,transparent),transparent 70%);background-size:auto;mask-image:none;-webkit-mask-image:none}
{{ $scope }} > .glow{display:none}
        @break
    @case('dots')
{{ $scope }}::before{background-image:radial-gradient(color-mix(in srgb,var(--text) 11%,transparent) 1px,transparent 1.5px);background-size:24px 24px;mask-image:radial-gradient(ellipse at top,#000 25%,transparent 80%);-webkit-mask-image:radial-gradient(ellipse at top,#000 25%,transparent 80%)}
        @break
    @case('plain')
{{ $scope }}::before,{{ $scope }}::after{display:none}
{{ $scope }} > .glow{display:none}
        @break
    @case('lines')
{{ $scope }}::before{background-image:repeating-linear-gradient(135deg,color-mix(in srgb,var(--text) 4%,transparent) 0 1px,transparent 1px 26px);background-size:auto;mask-image:linear-gradient(180deg,#000 10%,transparent 75%);-webkit-mask-image:linear-gradient(180deg,#000 10%,transparent 75%)}
        @break
    @case('blueprint')
{{ $scope }}::before{background-image:linear-gradient(color-mix(in srgb,var(--text) 7%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--text) 7%,transparent) 1px,transparent 1px),linear-gradient(var(--grid-line) 1px,transparent 1px),linear-gradient(90deg,var(--grid-line) 1px,transparent 1px);background-size:120px 120px,120px 120px,24px 24px,24px 24px;mask-image:linear-gradient(180deg,#000 30%,rgba(0,0,0,.35) 100%);-webkit-mask-image:linear-gradient(180deg,#000 30%,rgba(0,0,0,.35) 100%)}
{{ $scope }} > .glow{display:none}
        @break
@endswitch
