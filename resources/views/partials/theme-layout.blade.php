{{--
  Public pages only: restyles the header, hero, cards, buttons and footer for the theme chosen
  in the admin panel. "classic" is the page's own design, so it needs nothing here.
  Desktop-only layout changes sit inside min-width queries so the mobile layout stays intact.
--}}
<style>
@switch($theme['layout'])

    @case('showcase')
/* ---------- header: three separate floating islands (logo / links / button) ---------- */
.nav-inner{flex-direction:row-reverse;background:transparent;border:none;box-shadow:none;backdrop-filter:none;-webkit-backdrop-filter:none;padding:0}
.logo{padding:7px 20px 7px 7px;border-radius:99px;background:var(--nav-bg);border:1px solid var(--border);box-shadow:var(--shadow);backdrop-filter:blur(16px) saturate(1.4);-webkit-backdrop-filter:blur(16px) saturate(1.4)}
.logo b{border-radius:50%;width:38px;height:38px;background:linear-gradient(135deg,var(--accent),var(--accent-2))}
.hire{display:inline-flex;align-items:center;height:54px;padding:0 26px;background:linear-gradient(135deg,var(--accent),var(--accent-2));box-shadow:0 12px 26px -12px var(--accent)}
.nav-inner .icon-btn{width:50px;height:50px;box-shadow:var(--shadow)}
@media (min-width:881px){
  .nav-links{position:absolute;left:50%;transform:translateX(-50%);gap:4px;padding:7px;align-items:center;border-radius:99px;background:var(--nav-bg);border:1px solid var(--border);box-shadow:var(--shadow);backdrop-filter:blur(16px) saturate(1.4);-webkit-backdrop-filter:blur(16px) saturate(1.4)}
  /* same 7px inset and 38px inner height as the logo pill, so both islands are equally tall */
  .nav-links a{display:flex;align-items:center;height:38px;padding:0 16px}
  .nav-links a.active{background:linear-gradient(135deg,var(--accent),var(--accent-2));color:var(--accent-ink)}
}
/* ---------- hero: text left, photo right, gradient accents ---------- */
.hero h1 .line:nth-child(2) > span{background:linear-gradient(100deg,var(--accent),var(--accent-2));-webkit-background-clip:text;background-clip:text;color:transparent}
.status{border:1.5px solid transparent;background:linear-gradient(var(--surface),var(--surface)) padding-box,linear-gradient(100deg,var(--accent),var(--accent-2)) border-box}
.role{display:inline-block;padding:10px 20px;border-radius:99px;background:var(--surface);border:1px solid var(--border);box-shadow:var(--shadow)}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent-2))}
.btn-ghost{background:var(--surface)}
.hero-social a{background:var(--surface);box-shadow:var(--shadow)}
.photo-card{border-radius:44px;outline:none;box-shadow:0 0 0 6px var(--bg),0 0 0 8px var(--accent),var(--shadow)}
.photo-card .ph-tag{border-radius:99px;padding:12px 14px 12px 22px}
/* ---------- sections: centered headings ---------- */
.sec-head{flex-direction:column;align-items:center;text-align:center}
.sec-head > p{max-width:560px}
.eyebrow::after{content:"";width:28px;height:1.5px;background:var(--accent)}
.card{box-shadow:var(--shadow)}
/* ---------- footer: floating rounded card, gradient name, pill links ---------- */
footer{margin:90px auto 24px;width:min(1200px,100% - 32px);padding:54px 28px 30px;border-radius:44px;background:var(--surface);border:1px solid var(--border);box-shadow:var(--shadow)}
footer .container{width:100%}
.f-big{font-size:clamp(2.6rem,9.5vw,8rem);line-height:1.05;letter-spacing:-.05em;background:linear-gradient(100deg,var(--accent),var(--accent-2));-webkit-background-clip:text;background-clip:text;color:transparent}
.f-bottom{flex-direction:column-reverse;align-items:center;text-align:center;gap:18px;margin-top:26px;padding-top:0;border-top:none}
.f-links{justify-content:center;gap:10px}
.f-links a{padding:9px 20px;border-radius:99px;border:1px solid var(--border);background:var(--bg);font-weight:600;color:var(--text)}
.f-links a:hover{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
        @break

    @case('developer')
/* ---------- header: solid full-width bar, underline links ---------- */
nav{top:0}
.nav-inner{width:100%;border-radius:0;border:none;border-bottom:1px solid var(--border);background:var(--surface);backdrop-filter:none;-webkit-backdrop-filter:none;box-shadow:none;padding:10px max(16px,calc((100% - 1200px)/2))}
.logo b{border-radius:6px}
.logo::after{content:"_";color:var(--accent);animation:blink 1s steps(1) infinite;margin-left:-8px}
@media (min-width:881px){
  .nav-links{margin-left:auto;gap:0}
  .nav-links a{border-radius:0;text-transform:lowercase;letter-spacing:.02em;padding:9px 13px}
  .nav-links a::before{content:"./";color:var(--accent);opacity:.7}
  .nav-links a.active{background:transparent;box-shadow:inset 0 -2px 0 var(--accent)}
}
/* ---------- squared controls ---------- */
.btn,.hire,.filter,.tag,.chip,.status,.icon-btn{border-radius:10px}
.eyebrow::before{content:"//";width:auto;height:auto;background:none}
/* ---------- hero: photo on the left ---------- */
@media (min-width:981px){
  .hero-grid{grid-template-columns:.75fr 1.25fr}
  .hero-visual{order:-1}
  .spin-badge{left:auto;right:-46px}
}
.photo-card{border-radius:var(--radius);outline-style:dashed}
.photo-card .ph-tag{border-radius:10px}
.role{padding:12px 16px;border:1px solid var(--border);border-radius:10px;background:var(--surface);display:inline-block}
.card{border-left:3px solid var(--accent)}
/* ---------- footer: compact single line ---------- */
footer{margin-top:80px;padding:22px 0;background:var(--surface);border-top:1px solid var(--border)}
.f-big{display:none}
.f-bottom{margin-top:0;padding-top:0;border-top:none;align-items:center}
.f-bottom > span::before{content:"$ ";color:var(--accent)}
        @break

    @case('minimal')
/* ---------- header: plain text bar, no box ---------- */
.nav-inner{background:transparent;border:none;box-shadow:none;backdrop-filter:none;-webkit-backdrop-filter:none;padding:10px 0}
@media (min-width:881px){
  nav{position:absolute;top:18px}
  .nav-links{margin-left:auto;gap:6px}
  .nav-links a{padding:6px 10px;font-weight:500}
  .nav-links a.active{background:transparent;text-decoration:underline;text-underline-offset:6px;text-decoration-thickness:2px;text-decoration-color:var(--accent)}
}
@media (max-width:880px){.nav-inner{background:var(--bg);padding:8px 8px 8px 16px;border:1px solid var(--border)}}
.logo b{background:transparent;color:var(--text);border:1.5px solid var(--text)}
.hire{background:transparent;color:var(--text);border:1.5px solid var(--text)}
.hire:hover{background:var(--text);color:var(--bg);box-shadow:none}
/* ---------- hero: quiet, monochrome photo ---------- */
.hero h1{font-weight:600;font-size:clamp(3rem,7vw,5.6rem);letter-spacing:-.035em}
.status{background:transparent;padding-left:0;border:none}
.photo-card{border-radius:var(--radius);outline:none;box-shadow:none}
.photo-card img{filter:grayscale(1)}
.photo-card .ph-tag{border-radius:8px}
.spin-badge{display:none}
.btn{border-radius:var(--radius)}
/* ---------- flat cards ---------- */
.card{background:transparent;box-shadow:none}
.card:hover{border-color:var(--text)}
.card::before{display:none}
.marquee{background:transparent}
.marquee.accent-strip{transform:none;margin:20px 0 0;background:transparent;color:var(--text);border-block:1px solid var(--border)}
.cta-box{background:transparent;color:var(--text);border:1px solid var(--border);border-radius:var(--radius)}
.cta-box::before,.cta-box::after{display:none}
.cta-box .btn{background:var(--text);color:var(--bg)}
/* ---------- footer: small centered text ---------- */
footer{padding:60px 0 40px}
.f-big{display:none}
.f-bottom{flex-direction:column;align-items:center;text-align:center;margin-top:0;gap:14px}
.f-links{justify-content:center}
        @break

    @case('agency')
/* ---------- header: solid accent bar ---------- */
nav{top:0}
.nav-inner{width:100%;border-radius:0;border:none;background:var(--accent);color:var(--accent-ink);backdrop-filter:none;-webkit-backdrop-filter:none;box-shadow:0 10px 30px -18px var(--accent);padding:12px max(16px,calc((100% - 1200px)/2))}
.logo b{background:var(--accent-ink);color:var(--accent)}
.hire{background:var(--accent-ink);color:var(--accent)}
.hire:hover{box-shadow:0 10px 24px -8px rgba(0,0,0,.5)}
.nav-inner .icon-btn{background:transparent;border-color:color-mix(in srgb,var(--accent-ink) 35%,transparent);color:var(--accent-ink)}
@media (min-width:881px){
  .nav-links{margin-left:auto}
  .nav-links a{color:color-mix(in srgb,var(--accent-ink) 72%,transparent);text-transform:uppercase;font-size:.78rem;letter-spacing:.08em}
  .nav-links a:hover{color:var(--accent-ink)}
  .nav-links a.active{color:var(--accent-ink);background:color-mix(in srgb,var(--accent-ink) 16%,transparent)}
}
/* ---------- hero: loud uppercase, offset photo block ---------- */
.hero h1{text-transform:uppercase;letter-spacing:-.03em}
.sec-head h2{text-transform:uppercase;letter-spacing:-.02em}
.photo-card{border-radius:var(--radius);outline:none;box-shadow:16px 16px 0 var(--accent)}
.spin-badge{background:var(--accent);color:var(--accent-ink);border:none}
.spin-badge text{fill:var(--accent-ink)}
.spin-badge .star{color:var(--accent-ink)}
.btn{border-radius:12px;text-transform:uppercase;letter-spacing:.06em;font-size:.84rem}
.card{border-width:2px}
.card:hover{transform:translateY(-4px);box-shadow:8px 8px 0 var(--accent);border-color:var(--accent)}
.card{transition:transform .35s var(--ease),box-shadow .35s,border-color .35s}
/* ---------- footer: inverted block ---------- */
footer{margin-top:90px;background:var(--text);color:var(--bg);padding:80px 0 34px}
.f-big{background:none;-webkit-background-clip:border-box;background-clip:border-box;color:var(--accent);text-transform:uppercase;font-size:clamp(2.8rem,12.2vw,10.4rem)}
.f-bottom{color:color-mix(in srgb,var(--bg) 70%,transparent);border-top-color:color-mix(in srgb,var(--bg) 22%,transparent)}
        @break

    @case('blueprint')
/* ---------- header: sharp outlined box ---------- */
nav{top:12px}
.nav-inner{border-radius:var(--radius);border:1px solid var(--accent);box-shadow:5px 5px 0 var(--accent-soft)}
.logo b,.icon-btn,.hire{border-radius:var(--radius)}
@media (min-width:881px){
  .nav-links a{border-radius:0;text-transform:uppercase;font-size:.74rem;letter-spacing:.12em}
  .nav-links li + li{border-left:1px solid var(--border)}
  .nav-links a.active{background:transparent;color:var(--accent)}
}
/* ---------- sharp controls ---------- */
.btn,.filter,.tag,.chip,.status{border-radius:var(--radius)}
/* ---------- hero: outlined second line, dashed photo frame ---------- */
.hero h1 .line:nth-child(2) > span{color:transparent;-webkit-text-stroke:2px var(--text)}
.photo-card{border-radius:var(--radius);outline:1.5px dashed var(--accent);box-shadow:none}
.photo-card .ph-tag{border-radius:var(--radius)}
.spin-badge{border-style:dashed;border-color:var(--accent)}
.card{border-style:dashed;border-color:color-mix(in srgb,var(--accent) 38%,var(--border))}
.card:hover{border-style:solid}
.cta-box{border-radius:var(--radius)}
.eyebrow::before{width:10px;height:10px;background:transparent;border:1.5px solid var(--accent)}
/* ---------- footer: left-aligned outlined name ---------- */
footer{margin-top:80px;padding:60px 0 30px;border-top:1px dashed var(--accent)}
.f-big{text-align:left;font-size:clamp(2.6rem,9vw,7rem);background:none;-webkit-background-clip:border-box;background-clip:border-box;color:transparent;-webkit-text-stroke:1.5px var(--text);line-height:1}
.f-bottom{border-top-style:dashed}
        @break

@endswitch
</style>
@include('partials.theme-components')
