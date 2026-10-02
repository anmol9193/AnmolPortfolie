{{--
  Public pages only: restyles the page components (marquee, about, services, skills, experience,
  projects, process, global, education, CTA, contact) for the theme chosen in the admin panel.
  Grid/column changes are desktop-only so the mobile layout of each page stays as designed.
--}}
<style>
@switch($theme['layout'])

    @case('showcase')
/* marquee: floating pills */
.marquee{background:transparent;border:none}
.m-item{padding:12px 22px;margin:18px 8px;border:1px solid var(--border);border-radius:99px;background:var(--surface);font-size:1.02rem;box-shadow:var(--shadow)}
.m-item i{font-size:1.4rem}
.marquee.accent-strip{transform:none;margin:20px auto 0;width:min(1200px,100% - 32px);border-radius:99px;background:linear-gradient(100deg,var(--accent),var(--accent-2))}
.accent-strip .m-item{margin:0;border:none;background:transparent;box-shadow:none}
/* about */
.b-intro{text-align:center;align-items:center}
.b-stat{align-items:center;text-align:center;gap:14px}
.b-stat .ic,.b-loc .pin{border-radius:50%}
/* services: centered cards */
.svc{text-align:center;align-items:center}
.svc .ic{border-radius:50%;margin:22px 0}
.svc .stack{align-self:stretch}
/* skills: one column, pill cloud */
.skill-groups{grid-template-columns:1fr}
.sg-head{flex-direction:column;gap:10px}
.sk-list{display:flex;flex-wrap:wrap;justify-content:center}
.sk{border-radius:99px;padding:12px 22px}
/* experience: stacked cards */
.exp-list{border-top:none;display:grid;gap:18px}
.exp{border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);padding:34px;box-shadow:var(--shadow)}
.exp::before{background:var(--accent-soft)}
/* projects */
.proj-img{border-radius:calc(var(--radius) - 8px)}
.proj-body{text-align:center;align-items:center}
.proj-meta{width:100%}
.filters{justify-content:center}
.filter.active{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
/* process: centered cards */
.process{gap:18px}
.process::before{display:none}
.step{padding:30px 22px;text-align:center;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
.step .dot{margin-inline:auto}
/* global: globe on top, countries in a row */
@media (min-width:981px){
  .global{grid-template-columns:1fr;justify-items:center;gap:40px}
  .globe-wrap{max-width:340px}
  .countries{grid-template-columns:repeat(3,1fr);width:100%}
  .country{flex-direction:column;text-align:center;gap:12px}
  .country .code{width:auto}
  .country .cnt{margin:0}
}
/* education: centered */
.edu{text-align:center;align-items:center}
.edu .yr{width:100%}
.edu .ic{border-radius:50%}
.edu .board{align-self:center}
/* cta + contact */
.cta-box{background:linear-gradient(135deg,var(--accent),var(--accent-2));border-radius:60px}
.c-item{border-radius:99px}
.field input{border-radius:99px;padding-left:24px}
.field textarea{border-radius:24px;padding-left:24px}
.field label{left:25px}
@media (min-width:981px){form.card{order:-1}}
        @break

    @case('developer')
/* marquee: prompt list */
.m-item{font-size:1rem;padding:16px 26px;text-transform:lowercase;font-weight:500;gap:10px}
.m-item::before{content:">";color:var(--accent);font-weight:700}
.m-item i{font-size:1.3rem}
.marquee.accent-strip{transform:none;margin:20px 0 0;background:var(--surface);color:var(--text);border-block:1px solid var(--border)}
.accent-strip .m-item::before{content:"#"}
/* about */
.b-stat .ic{border-radius:8px}
.b-loc .pin{border-radius:14px}
.b-loc .pin::before,.b-loc .pin::after{border-radius:14px}
.b-role .arrow{border-radius:10px}
/* services: two columns, icon beside text */
.svc .n::before{content:"// "}
.svc .ic{border-radius:10px}
@media (min-width:981px){
  .services{grid-template-columns:repeat(2,1fr)}
  .svc{min-height:0;display:grid;grid-template-columns:62px 1fr;column-gap:20px;align-content:start}
  .svc .n{grid-column:1/-1;margin-bottom:14px}
  .svc .ic{margin:4px 0 0;grid-row:span 2}
  .svc .stack{grid-column:1/-1}
}
/* skills: dashed tiles */
.sg-head h3::before{content:"$ ";color:var(--accent)}
.sg-head span{border-radius:6px}
.sk-list{grid-template-columns:repeat(auto-fill,minmax(170px,1fr))}
.sk{border-radius:8px;background:transparent;border:1px dashed var(--border)}
/* experience: timeline */
.exp-list{border-top:none;border-left:2px solid var(--border);margin-left:6px}
.exp{grid-template-columns:1fr;gap:10px;padding:26px 10px 30px 36px;border-bottom:none}
.exp::after{content:"";position:absolute;left:-1px;top:32px;width:14px;height:14px;border-radius:4px;background:var(--accent)}
.exp-loc{text-align:left}
.exp-date .now{border-radius:6px}
/* projects */
.proj-img{border-radius:8px}
.proj-flag,.proj-arrow{border-radius:8px}
.proj-cat::before{content:"~/"}
/* process: two-column step rows */
.process::before{display:none}
.process{gap:18px}
.step{display:grid;grid-template-columns:72px 1fr;column-gap:18px;align-content:start;padding:22px;border:1px solid var(--border);border-left:3px solid var(--accent);border-radius:var(--radius);background:var(--surface)}
.step .dot{grid-row:span 3;margin:0;border-radius:12px}
@media (min-width:981px){.process{grid-template-columns:repeat(2,1fr)}}
/* global: globe on the right */
@media (min-width:981px){
  .global{grid-template-columns:1.3fr 1fr}
  .globe-wrap{order:2}
}
.country .cnt{border-radius:6px}
/* education: two columns */
@media (min-width:981px){.edu-grid{grid-template-columns:repeat(2,1fr)}.edu{min-height:0}}
.edu .ic{border-radius:10px;margin:18px 0 16px}
.edu .board{border-radius:6px}
/* cta: left-aligned console box */
.cta-box{background:var(--surface);color:var(--text);border:1px solid var(--border);border-left:4px solid var(--accent);border-radius:var(--radius);text-align:left;padding-inline:clamp(24px,6vw,70px)}
.cta-box::before,.cta-box::after{display:none}
.cta-box h2{font-size:clamp(2.2rem,5vw,4rem)}
.cta-box h2 .serif{color:var(--accent)}
.cta-box .btn{background:var(--accent);color:var(--accent-ink)}
/* contact */
.c-item .ic{border-radius:10px}
.field input,.field textarea{border-radius:8px}
.hero-social a{border-radius:10px}
        @break

    @case('minimal')
/* marquee: quiet */
.m-item{font-size:1rem;font-weight:500;padding:18px 26px}
.m-item i{font-size:1.3rem;filter:grayscale(1)}
/* cards become ruled blocks */
.card{border:none;border-top:1px solid var(--border);border-radius:0}
.card:hover{border-color:var(--text)}
.chip,.tag{background:transparent;border-radius:6px}
/* about */
.b-role{background:transparent;color:var(--text);border-top:2px solid var(--accent)!important}
.b-stat .ic,.b-loc .pin{background:transparent}
.b-stat .ic{justify-content:start;width:auto}
/* services */
.svc{min-height:0;padding:26px 0 30px}
.svc .ic{background:transparent;width:auto;height:auto;justify-content:start;margin:18px 0}
.svc:hover .ic{background:transparent;color:var(--accent);transform:none}
@media (min-width:981px){.services{grid-template-columns:repeat(2,1fr);column-gap:50px}}
/* skills */
.sg{padding:24px 0}
.sk{background:transparent;border-radius:0;border-bottom:1px solid var(--border);padding:12px 0}
.sk:hover{background:transparent;transform:none;border-color:transparent;border-bottom-color:var(--accent)}
.sg-head span{border:none;padding:0}
/* experience */
.exp-big{display:none}
.exp::before{display:none}
.exp:hover > div:not(.exp-big){transform:none}
/* projects */
.proj-img{margin:0;border-radius:var(--radius)}
.proj-body{padding:18px 0 8px}
.proj-flag{background:transparent;padding:0}
/* process */
.process{gap:30px}
.process::before{display:none}
.step{border-top:1px solid var(--border);padding-top:22px}
.step .dot{background:transparent;border:none;width:auto;height:auto;justify-content:start;margin-bottom:14px}
.step:hover .dot{background:transparent;transform:none}
.step:hover .dot svg{color:var(--accent)}
/* global: list only */
.global{grid-template-columns:1fr}
.globe-wrap{display:none}
.country{padding:22px 0}
/* education */
.edu{min-height:0;padding:24px 0}
.edu .ic{background:transparent;width:auto;height:auto;justify-content:start;margin:16px 0}
.edu:hover .ic{background:transparent;color:var(--accent);transform:none}
.edu.main{background:transparent;color:var(--text);border-top:2px solid var(--text)!important}
.edu.main p,.edu.main .yr{color:var(--muted)}
.edu.main .ic{background:transparent;color:var(--accent)}
@media (min-width:981px){.edu-grid{grid-template-columns:repeat(2,1fr);column-gap:50px}}
/* contact */
.c-item{padding:18px 0}
.c-item .ic{background:transparent;width:auto;height:auto}
.c-item:hover .ic{background:transparent;color:var(--accent)}
form.card{padding:24px 0}
.field input,.field textarea{background:transparent;border:none;border-bottom:1px solid var(--border);border-radius:0;padding-left:0}
.field input:focus,.field textarea:focus{background:transparent}
.field label{left:0}
        @break

    @case('agency')
/* marquee: inverted, loud */
.marquee{background:var(--text);border:none}
.m-item{color:var(--bg);text-transform:uppercase;font-weight:800;font-size:1.5rem}
.m-item:hover{color:var(--accent)}
.accent-strip .m-item{font-size:1.3rem;font-weight:800}
.marquee.accent-strip{background:var(--accent)}
.accent-strip .m-item,.accent-strip .m-item:hover{color:var(--accent-ink)}
/* about */
.b-stat b{color:var(--accent)}
.b-stat .ic{background:var(--text);color:var(--bg)}
.b-intro h3{text-transform:uppercase}
/* services: big numbers, two columns */
.svc .n{font-size:3rem;font-weight:800;color:var(--accent);line-height:1}
.svc .ic{border-radius:12px;background:var(--text);color:var(--bg);margin:20px 0}
.svc h3{text-transform:uppercase}
@media (min-width:981px){.services{grid-template-columns:repeat(2,1fr)}.svc{min-height:0}}
/* skills */
.sg-head h3{text-transform:uppercase}
.sg-head span{background:var(--accent);color:var(--accent-ink);border:none;font-weight:700}
.sk{border-radius:10px;border:2px solid var(--border);background:var(--surface)}
/* experience: heavy rules */
.exp-list{border-top:3px solid var(--text)}
.exp{border-bottom:3px solid var(--text)}
.exp h3{text-transform:uppercase}
.exp::before{background:var(--accent-soft)}
.exp-big{opacity:.1;color:var(--accent)}
/* projects */
.filter{border-radius:10px;border-width:2px;text-transform:uppercase}
.filter.active{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
.proj-body h3{text-transform:uppercase}
.proj-flag{background:var(--accent);color:var(--accent-ink)}
.proj-arrow{border-radius:12px;border-width:2px}
/* process */
.process::before{background:var(--text);height:3px}
.step .dot{border-radius:16px;background:var(--accent);border:none}
.step .dot svg{color:var(--accent-ink)}
.step:hover .dot{background:var(--text)}
.step:hover .dot svg{color:var(--bg)}
.step .k{font-size:1.5rem;font-weight:800;color:var(--text)}
.step h3{text-transform:uppercase}
/* global */
.country{border-left:8px solid var(--accent)}
.country .code{font-size:3.2rem}
/* education */
.edu h3{text-transform:uppercase}
.edu .ic{border-radius:12px}
.edu.main{background:var(--accent);color:var(--accent-ink)}
.edu.main p,.edu.main .yr{color:inherit;opacity:.85}
.edu.main .ic{background:var(--accent-ink);color:var(--accent)}
.edu.main .board{background:var(--accent-ink);color:var(--accent)}
/* cta: inverted block */
.cta-box{background:var(--text);color:var(--bg);border-radius:var(--radius)}
.cta-box h2{text-transform:uppercase}
.cta-box h2 .serif{color:var(--accent)}
.cta-box .btn{background:var(--accent);color:var(--accent-ink)}
/* contact */
.c-item .ic{border-radius:12px;background:var(--accent);color:var(--accent-ink)}
.c-item:hover .ic{background:var(--text);color:var(--bg)}
.field input,.field textarea{border-width:2px;border-radius:10px}
.hero-social a{border-radius:12px;border-width:2px}
        @break

    @case('blueprint')
/* marquee: dashed ruler */
.marquee{background:transparent;border-block:1px dashed var(--accent)}
.m-item{font-size:.95rem;text-transform:uppercase;letter-spacing:.14em;font-weight:500;padding:20px 30px;border-left:1px dashed var(--border)}
.m-item i{font-size:1.4rem}
.marquee.accent-strip{transform:none;margin:20px 0 0;background:transparent;color:var(--accent)}
/* corner mark on every card */
.card::after{content:"";position:absolute;top:8px;right:8px;width:9px;height:9px;border-top:1.5px solid var(--accent);border-right:1.5px solid var(--accent);pointer-events:none}
.tag,.chip{border-style:dashed}
.hero-social a{border-radius:var(--radius)}
/* about */
.b-stat .ic,.b-loc .pin{border-radius:0;background:transparent;border:1.5px solid var(--accent)}
.b-loc .pin::before,.b-loc .pin::after{border-radius:0}
.b-role .arrow{border-radius:0}
.b-stat b{color:transparent;-webkit-text-stroke:1.5px var(--text)}
/* services */
.svc .n{color:var(--accent);letter-spacing:.2em}
.svc .n::before{content:"FIG. "}
.svc .ic{border-radius:0;background:transparent;border:1.5px solid var(--accent)}
/* skills */
.sg-head span{border-radius:0;border-style:dashed}
.sk{border-radius:0;background:transparent;border:1px solid var(--border)}
/* experience */
.exp-list{border-top-style:dashed}
.exp{border-bottom-style:dashed}
.exp-date .now{border-radius:0}
.exp-big{opacity:.2;color:transparent;-webkit-text-stroke:1px var(--text)}
/* projects */
.proj-img,.proj-flag,.proj-arrow{border-radius:0}
.filter.active{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
/* process: diamond markers */
.process::before{background:repeating-linear-gradient(90deg,var(--accent) 0 8px,transparent 8px 16px)}
.step .dot{border-radius:0;border:1.5px dashed var(--accent);transform:rotate(45deg) scale(.82)}
.step .dot svg{transform:rotate(-45deg)}
.step:hover .dot{transform:rotate(45deg) scale(.9)}
/* global */
.country .code{color:transparent;-webkit-text-stroke:1.5px var(--accent)}
.country .cnt{border-radius:0;border-style:dashed}
/* education */
.edu .ic{border-radius:0;background:transparent;border:1.5px solid var(--accent)}
.edu .board{border-radius:0}
.edu.main{background:transparent;color:var(--text);border:2px solid var(--accent)!important}
.edu.main p,.edu.main .yr{color:var(--muted)}
/* cta */
.cta-box{background:transparent;color:var(--text);border:1.5px dashed var(--accent)}
.cta-box h2 .serif{color:var(--accent)}
.cta-box .btn{background:var(--accent);color:var(--accent-ink)}
/* contact */
.c-item .ic{border-radius:0;background:transparent;border:1px solid var(--accent)}
.field input,.field textarea{border-radius:0;background:transparent;border-style:dashed}
        @break

@endswitch
</style>
