{{-- Base stylesheet of the public site (extracted from the original index.html). --}}
@verbatim
<style>
/* ================= THEME ================= */
:root{
  --bg:#0c0b0a; --bg-2:#121110; --surface:#161514; --surface-2:#1e1d1b; --border:#2b2926;
  --text:#f4efe6; --muted:#a19b90; --faint:#6d685f;
  --accent:#ff5b2e; --accent-2:#ff8a4c; --accent-ink:#1a0d07; --accent-soft:rgba(255,91,46,.12);
  --lime:#c9f25a;
  --shadow:0 30px 60px -30px rgba(0,0,0,.8);
  --nav-bg:rgba(22,21,20,.72);
  --grid-line:rgba(255,255,255,.035);
  --radius:22px;
  --ease:cubic-bezier(.2,.8,.2,1);
}
[data-theme="light"]{
  --bg:#f5f1e8; --bg-2:#efeadf; --surface:#fffcf6; --surface-2:#ebe5d8; --border:#dcd4c4;
  --text:#191714; --muted:#645e54; --faint:#9a9387;
  --accent:#ec4a1c; --accent-2:#ff7a3d; --accent-ink:#fff; --accent-soft:rgba(236,74,28,.10);
  --lime:#5f8a00;
  --shadow:0 30px 60px -30px rgba(80,60,30,.35);
  --nav-bg:rgba(255,252,246,.78);
  --grid-line:rgba(0,0,0,.045);
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth;scroll-padding-top:90px}
body{font-family:'Poppins',system-ui,sans-serif;background:var(--bg);color:var(--text);line-height:1.65;overflow-x:hidden;transition:background .5s,color .5s;-webkit-font-smoothing:antialiased}
body.loading{overflow:hidden}
a{color:inherit;text-decoration:none}
img{max-width:100%;display:block}
button{font-family:inherit}
::selection{background:var(--accent);color:var(--accent-ink)}
.container{width:min(1200px,100% - 32px);margin-inline:auto}
section{padding:120px 0;position:relative}
h1,h2,h3,h4{font-family:'Poppins',sans-serif;letter-spacing:-.02em;line-height:1.15}
.serif{font-family:'Poppins',sans-serif;font-style:italic;font-weight:400;letter-spacing:0}
.accent{color:var(--accent)}

/* grid backdrop + grain */
body::before{content:"";position:fixed;inset:0;z-index:-2;background-image:linear-gradient(var(--grid-line) 1px,transparent 1px),linear-gradient(90deg,var(--grid-line) 1px,transparent 1px);background-size:72px 72px;mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%)}
body::after{content:"";position:fixed;inset:0;z-index:999;pointer-events:none;opacity:.05;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}
.glow{position:fixed;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,var(--accent-soft),transparent 65%);top:-300px;right:-200px;z-index:-1;pointer-events:none}

/* ================= PRELOADER ================= */
#loader{position:fixed;inset:0;z-index:1000;background:var(--bg);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:18px;transition:transform 1s var(--ease)}
#loader.done{transform:translateY(-100%)}
#loader .l-name{font-family:'Poppins';font-size:clamp(2rem,6vw,3.4rem);font-weight:700;overflow:hidden}
#loader .l-name span{display:inline-block;animation:up .8s var(--ease) both}
#loader .l-bar{width:200px;height:2px;background:var(--border);overflow:hidden;border-radius:2px}
#loader .l-bar i{display:block;height:100%;width:0;background:var(--accent);transition:width .2s}
#loader .l-count{font-family:'Poppins';font-size:.85rem;color:var(--muted)}
@keyframes up{from{transform:translateY(110%)}to{transform:none}}

/* ================= CURSOR ================= */
.cur-dot,.cur-ring{position:fixed;top:0;left:0;pointer-events:none;z-index:998;border-radius:50%;display:none}
.cur-dot{width:6px;height:6px;background:var(--accent)}
.cur-ring{width:38px;height:38px;border:1.5px solid var(--accent);opacity:.5;transition:width .3s,height .3s,opacity .3s,background .3s}
.cur-ring.hover{width:70px;height:70px;opacity:.9;background:var(--accent-soft)}
@media (hover:hover) and (pointer:fine){.cur-dot,.cur-ring{display:block}}

#progress{position:fixed;top:0;left:0;height:2px;width:0;background:var(--accent);z-index:200}

/* ================= NAV ================= */
nav{position:fixed;top:16px;left:0;right:0;z-index:100;display:flex;justify-content:center;pointer-events:none}
.nav-inner{pointer-events:auto;display:flex;align-items:center;gap:8px;padding:8px 8px 8px 20px;border-radius:99px;background:var(--nav-bg);backdrop-filter:blur(16px) saturate(1.4);-webkit-backdrop-filter:blur(16px) saturate(1.4);border:1px solid var(--border);box-shadow:var(--shadow);width:min(1200px,100% - 32px);justify-content:space-between}
.logo{font-family:'Poppins';font-weight:700;font-size:1.2rem;display:flex;align-items:center;gap:10px}
.logo b{width:34px;height:34px;border-radius:10px;background:var(--accent);color:var(--accent-ink);display:grid;place-items:center;font-size:.95rem;transition:transform .5s var(--ease)}
.logo:hover b{transform:rotate(-12deg) scale(1.08)}
.nav-links{display:flex;gap:2px;list-style:none}
.nav-links a{position:relative;padding:9px 15px;border-radius:99px;font-weight:600;font-size:.88rem;color:var(--muted);transition:color .25s,background .25s}
.nav-links a:hover{color:var(--text)}
.nav-links a.active{color:var(--text);background:var(--surface-2)}
.nav-actions{display:flex;gap:8px;align-items:center}
.icon-btn{width:42px;height:42px;border-radius:50%;border:1px solid var(--border);background:var(--surface);color:var(--text);display:grid;place-items:center;cursor:pointer;transition:.3s var(--ease)}
.icon-btn:hover{border-color:var(--accent);color:var(--accent)}
.icon-btn svg{width:18px;height:18px}
.theme-toggle svg{transition:transform .6s var(--ease)}
.theme-toggle:hover svg{transform:rotate(40deg)}
.theme-toggle .sun{display:none}
[data-theme="light"] .theme-toggle .sun{display:block}
[data-theme="light"] .theme-toggle .moon{display:none}
.menu-btn{display:none}
.hire{padding:11px 20px;border-radius:99px;background:var(--accent);color:var(--accent-ink);font-weight:700;font-size:.88rem;transition:.3s var(--ease);white-space:nowrap}
.hire:hover{transform:translateY(-2px);box-shadow:0 10px 24px -8px var(--accent)}

/* ================= BUTTONS ================= */
.btn{display:inline-flex;align-items:center;gap:12px;padding:16px 28px;border-radius:99px;font-weight:700;font-size:.95rem;transition:transform .35s var(--ease),box-shadow .35s,background .3s,color .3s,border-color .3s;cursor:pointer;border:none;position:relative}
.btn svg{width:18px;height:18px;transition:transform .35s var(--ease)}
.btn:hover svg{transform:translateX(4px) rotate(-45deg)}
.btn-primary{background:var(--accent);color:var(--accent-ink)}
.btn-primary:hover{box-shadow:0 16px 36px -12px var(--accent)}
.btn-ghost{background:transparent;border:1.5px solid var(--border);color:var(--text)}
.btn-ghost:hover{border-color:var(--text)}
.btn-ghost:hover svg{transform:translateY(3px)}

/* ================= HERO ================= */
.hero{min-height:100vh;display:flex;align-items:center;padding:150px 0 90px}
.hero > .container{width:min(1200px,100% - 32px)}
.hero-grid{display:grid;grid-template-columns:1.25fr .75fr;gap:70px;align-items:center}
.status{display:inline-flex;align-items:center;gap:10px;padding:8px 16px 8px 12px;border-radius:99px;border:1px solid var(--border);background:var(--surface);font-size:.85rem;font-weight:600;color:var(--muted);margin-bottom:30px}
.status .pulse{flex:none;width:10px;height:10px;border-radius:50%;background:var(--lime);position:relative}
.status .pulse::after{content:"";position:absolute;inset:0;border-radius:50%;background:var(--lime);animation:ping 1.8s var(--ease) infinite}
@keyframes ping{to{transform:scale(2.8);opacity:0}}
.hero h1{font-size:clamp(3.4rem,9vw,7.6rem);font-weight:700;line-height:.95;letter-spacing:-.045em;margin-bottom:28px}
.hero h1 .line{display:block;overflow:hidden;padding-bottom:.06em}
.hero h1 .line > span{display:inline-block;transform:translateY(110%);transition:transform 1.1s var(--ease)}
.ready .hero h1 .line > span{transform:none}
.hero h1 .line:nth-child(2) > span{transition-delay:.12s}
.hero h1 .serif{font-size:1.08em;color:var(--accent)}
.hero-sub{font-size:clamp(1.05rem,1.6vw,1.22rem);color:var(--muted);max-width:560px;margin-bottom:16px}
.hero-sub b{color:var(--text);font-weight:600}
.role{font-family:'Poppins';font-size:.92rem;color:var(--text);margin-bottom:38px;min-height:1.6em}
.role .prompt{color:var(--accent)}
.cursor{display:inline-block;width:9px;height:1.1em;background:var(--accent);vertical-align:-3px;margin-left:2px;animation:blink 1s steps(1) infinite}
@keyframes blink{50%{opacity:0}}
.btns{display:flex;gap:14px;flex-wrap:wrap}

.hero-visual{position:relative;justify-self:center;width:min(390px,100%)}
.photo-card{position:relative;border-radius:200px 200px 28px 28px;overflow:hidden;aspect-ratio:4/5;background:#fff;box-shadow:var(--shadow);outline:1px solid var(--border);outline-offset:10px}
.photo-card img{width:100%;height:100%;object-fit:cover;object-position:center 18%}
.photo-card .ph-tag{position:absolute;left:14px;right:14px;bottom:14px;padding:14px 18px;border-radius:18px;background:rgba(12,11,10,.78);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);color:#f4efe6;display:flex;justify-content:space-between;align-items:center;font-size:.83rem}
.ph-tag b{font-family:'Poppins';font-size:1rem;display:block}
.ph-tag span{color:#bdb6aa}
.ph-tag .dotc{width:36px;height:36px;border-radius:50%;background:var(--accent);display:grid;place-items:center;color:#1a0d07}
.ph-tag .dotc svg{width:16px;height:16px}
.spin-badge{position:absolute;top:-30px;left:-50px;width:130px;height:130px;border-radius:50%;background:var(--bg);display:grid;place-items:center;border:1px solid var(--border);z-index:3}
.spin-badge svg{position:absolute;inset:8px;width:calc(100% - 16px);height:calc(100% - 16px);animation:spin 16s linear infinite}
.spin-badge text{font-family:'Poppins';font-size:10.5px;letter-spacing:2.4px;fill:var(--text);text-transform:uppercase}
.spin-badge .star{position:static;width:38px;height:38px;color:var(--accent);animation:spin 8s linear infinite reverse}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes bob{50%{transform:translateY(-12px)}}

.hero-foot{display:flex;justify-content:space-between;align-items:flex-end;margin-top:80px;gap:30px;flex-wrap:wrap;padding-top:34px;border-top:1px solid var(--border)}
.hero-stats{display:flex;gap:56px;flex-wrap:wrap}
.stat b{display:block;font-family:'Poppins';font-size:clamp(2.2rem,4vw,3rem);font-weight:700;line-height:1}
.stat b sup{color:var(--accent);font-size:.55em}
.stat > span{font-size:.85rem;color:var(--muted)}
.scroll-hint{display:flex;align-items:center;gap:12px;font-family:'Poppins';font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:2px}
.scroll-hint i{width:1px;height:46px;background:var(--border);position:relative;overflow:hidden}
.scroll-hint i::after{content:"";position:absolute;left:0;top:-50%;width:100%;height:50%;background:var(--accent);animation:drop 1.8s var(--ease) infinite}
@keyframes drop{to{top:100%}}

/* ================= MARQUEE ================= */
.marquee{border-block:1px solid var(--border);background:var(--bg-2);overflow:hidden}
.marquee.accent-strip{background:var(--accent);color:var(--accent-ink);border:none;transform:rotate(-1.5deg);margin:20px -20px 0}
.m-track{display:flex;width:max-content;animation:scroll 40s linear infinite}
.marquee:hover .m-track{animation-play-state:paused}
.m-track.rev{animation-direction:reverse;animation-duration:30s}
.m-item{display:flex;align-items:center;gap:14px;padding:26px 34px;font-family:'Poppins';font-size:1.45rem;font-weight:600;white-space:nowrap;color:var(--muted);transition:color .3s}
.m-item:hover{color:var(--text)}
.m-item i{font-size:2rem}
.accent-strip .m-item{color:inherit;font-size:1.15rem;padding:16px 24px;text-transform:uppercase;letter-spacing:.04em}
.accent-strip .m-item svg{width:18px;height:18px}
@keyframes scroll{to{transform:translateX(-50%)}}

/* ================= SECTION HEAD ================= */
.sec-head{display:flex;justify-content:space-between;align-items:flex-end;gap:30px;margin-bottom:60px;flex-wrap:wrap}
.eyebrow{display:inline-flex;align-items:center;gap:10px;font-family:'Poppins';font-size:.78rem;color:var(--accent);text-transform:uppercase;letter-spacing:2px;margin-bottom:16px}
.eyebrow::before{content:"";width:28px;height:1.5px;background:var(--accent)}
.sec-head h2{font-size:clamp(2.3rem,5.4vw,4.2rem);font-weight:700;letter-spacing:-.04em;max-width:760px}
.sec-head h2 .serif{color:var(--accent)}
.sec-head > p{color:var(--muted);max-width:360px}

/* ================= CARD + SPOTLIGHT ================= */
.card{position:relative;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;isolation:isolate}
.card::before{content:"";position:absolute;inset:0;z-index:-1;opacity:0;transition:opacity .4s;background:radial-gradient(420px circle at var(--mx,50%) var(--my,50%),var(--accent-soft),transparent 45%)}
.card:hover::before{opacity:1}
.card:hover{border-color:color-mix(in srgb,var(--accent) 45%,var(--border))}

/* ================= ABOUT / BENTO ================= */
.bento{display:grid;grid-template-columns:repeat(4,1fr);grid-auto-rows:minmax(180px,auto);gap:18px}
.b-intro{grid-column:span 2;grid-row:span 2;padding:38px;display:flex;flex-direction:column;justify-content:space-between;gap:24px}
.b-intro h3{font-size:clamp(1.6rem,2.6vw,2.15rem);font-weight:600;line-height:1.2}
.b-intro h3 .serif{color:var(--accent)}
.b-intro p{color:var(--muted)}
.b-intro p + p{margin-top:14px}
.b-intro p strong{color:var(--text)}
.b-sign{display:flex;align-items:center;gap:14px;padding-top:22px;border-top:1px solid var(--border)}
.b-sign img{width:52px;height:52px;border-radius:50%;object-fit:cover;object-position:center 15%;background:#fff}
.b-sign b{display:block;font-family:'Poppins'}
.b-sign span{font-size:.85rem;color:var(--muted)}
.b-role{grid-column:span 2;padding:30px;background:var(--accent);color:var(--accent-ink);border-color:transparent!important;display:flex;flex-direction:column;justify-content:space-between}
.b-role::before{display:none}
.b-role small{font-family:'Poppins';font-size:.75rem;text-transform:uppercase;letter-spacing:2px;opacity:.75}
.b-role h4{font-size:1.8rem;font-weight:700;margin:6px 0 2px}
.b-role p{opacity:.85;font-size:.95rem}
.b-role .arrow{position:absolute;right:26px;top:26px;width:46px;height:46px;border-radius:50%;border:1.5px solid currentColor;display:grid;place-items:center;transition:transform .5s var(--ease)}
.b-role:hover .arrow{transform:rotate(45deg)}
.b-role .arrow svg{width:20px;height:20px}
.b-stat{padding:28px;display:flex;flex-direction:column;justify-content:space-between}
.b-stat b{font-family:'Poppins';font-size:3.2rem;line-height:1;font-weight:700}
.b-stat b sup{color:var(--accent);font-size:.5em}
.b-stat span{color:var(--muted);font-size:.9rem}
.b-stat .ic{width:40px;height:40px;border-radius:12px;background:var(--surface-2);display:grid;place-items:center;color:var(--accent)}
.b-stat .ic svg{width:20px;height:20px}
.b-loc{grid-column:span 2;padding:28px;display:flex;gap:24px;align-items:center}
.b-loc .pin{flex:none;width:92px;height:92px;border-radius:50%;background:var(--surface-2);display:grid;place-items:center;position:relative}
.b-loc .pin::before,.b-loc .pin::after{content:"";position:absolute;inset:0;border-radius:50%;border:1px solid var(--accent);opacity:0;animation:ring 3s var(--ease) infinite}
.b-loc .pin::after{animation-delay:1.5s}
@keyframes ring{0%{transform:scale(.6);opacity:.8}100%{transform:scale(1.5);opacity:0}}
.b-loc .pin svg{width:34px;height:34px;color:var(--accent)}
.b-loc small,.b-hob small{font-family:'Poppins';font-size:.72rem;text-transform:uppercase;letter-spacing:2px;color:var(--muted)}
.b-loc h4{font-size:1.35rem;margin:4px 0}
.b-loc p{color:var(--muted);font-size:.9rem}
.b-hob{grid-column:span 2;padding:28px}
.chips{display:flex;flex-wrap:wrap;gap:10px;margin-top:16px}
.chip{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:99px;border:1px solid var(--border);background:var(--surface-2);font-weight:600;font-size:.9rem;transition:.3s var(--ease)}
.chip:hover{border-color:var(--accent);color:var(--accent);transform:translateY(-3px)}
.chip svg{width:16px;height:16px}

/* ================= SERVICES ================= */
.services{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
.svc{padding:32px 28px 30px;min-height:330px;display:flex;flex-direction:column}
.svc .n{font-family:'Poppins';font-size:.8rem;color:var(--faint)}
.svc .ic{width:62px;height:62px;border-radius:18px;background:var(--surface-2);color:var(--accent);display:grid;place-items:center;margin:28px 0 24px;transition:transform .5s var(--ease),background .4s,color .4s}
.svc .ic svg{width:28px;height:28px}
.svc:hover .ic{background:var(--accent);color:var(--accent-ink);transform:rotate(-8deg) scale(1.06)}
.svc h3{font-size:1.3rem;margin-bottom:10px}
.svc p{color:var(--muted);font-size:.93rem;flex:1}
.svc .stack{margin-top:20px;padding-top:16px;border-top:1px dashed var(--border);font-family:'Poppins';font-size:.75rem;color:var(--muted)}

/* ================= SKILLS ================= */
.skill-groups{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
.sg{padding:32px}
.sg-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.sg-head h3{font-size:1.35rem}
.sg-head span{font-family:'Poppins';font-size:.75rem;color:var(--muted);padding:5px 12px;border:1px solid var(--border);border-radius:99px}
.sk-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px}
.sk{display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:16px;background:var(--surface-2);border:1px solid transparent;transition:.35s var(--ease);font-weight:600;font-size:.92rem}
.sk i{font-size:1.8rem;transition:transform .45s var(--ease)}
.sk:hover{border-color:var(--accent);transform:translateY(-4px);background:var(--bg)}
.sk:hover i,.sk:hover .svg-i{transform:scale(1.2) rotate(-8deg)}
.sk .svg-i{width:28px;height:28px;color:var(--accent);flex:none;transition:transform .45s var(--ease)}
[data-theme="dark"] .devicon-codeigniter-plain.colored{color:#ee4323!important}

/* ================= EXPERIENCE ================= */
.exp-list{border-top:1px solid var(--border)}
.exp{display:grid;grid-template-columns:220px 1fr auto;gap:40px;padding:44px 10px;border-bottom:1px solid var(--border);position:relative;overflow:hidden}
.exp::before{content:"";position:absolute;inset:0;background:var(--surface);transform:scaleY(0);transform-origin:bottom;transition:transform .6s var(--ease);z-index:-1}
.exp:hover::before{transform:scaleY(1)}
.exp > *{transition:transform .6s var(--ease)}
.exp:hover > div:not(.exp-big){transform:translateX(14px)}
.exp{isolation:isolate}
.exp-date{font-family:'Poppins';font-size:.85rem;color:var(--muted)}
.exp-date .now{display:inline-flex;align-items:center;gap:8px;margin-top:10px;padding:5px 12px;border-radius:99px;background:var(--accent-soft);color:var(--accent);font-size:.75rem;font-weight:500}
.exp-date .now::before{content:"";width:7px;height:7px;border-radius:50%;background:currentColor;animation:blink 1.4s infinite}
.exp h3{font-size:clamp(1.6rem,2.8vw,2.2rem);font-weight:600;margin-bottom:4px}
.exp h4{font-family:'Poppins';font-weight:700;color:var(--accent);font-size:1rem;margin-bottom:14px;letter-spacing:0}
.exp p{color:var(--muted);max-width:620px}
.tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:18px}
.tag{font-size:.78rem;padding:6px 12px;border-radius:99px;border:1px solid var(--border);color:var(--muted);font-weight:600}
.exp-loc{font-family:'Poppins';font-size:.8rem;color:var(--muted);text-align:right;white-space:nowrap}
.exp-big{position:absolute;right:14px;bottom:-26px;font-family:'Poppins';font-size:8rem;font-weight:700;color:var(--text);opacity:.04;line-height:1;pointer-events:none}

/* ================= PROJECTS ================= */
.filters{display:flex;gap:8px;flex-wrap:wrap}
.filter{padding:10px 20px;border-radius:99px;border:1px solid var(--border);background:transparent;color:var(--muted);cursor:pointer;font-weight:700;font-size:.85rem;transition:.3s var(--ease)}
.filter:hover{color:var(--text);border-color:var(--text)}
.filter.active{background:var(--text);color:var(--bg);border-color:var(--text)}
.proj-slider{position:relative}
#projSwiper{padding:12px 4px 24px;margin:-12px -4px 0}
#projSwiper .swiper-slide{height:auto;display:flex}
#projSwiper .proj{width:100%}
.proj-nav{display:flex;align-items:center;justify-content:center;gap:18px;margin-top:10px}
.proj-arrow{width:50px;height:50px;border-radius:50%;border:1px solid var(--border);background:var(--surface);color:var(--text);display:grid;place-items:center;cursor:pointer;transition:.3s var(--ease)}
.proj-arrow svg{width:20px;height:20px}
.proj-arrow:hover{background:var(--text);color:var(--bg);border-color:var(--text)}
.proj-arrow.swiper-button-disabled{opacity:.35;pointer-events:none}
.proj-pag{position:static!important;width:auto!important;display:flex;align-items:center;gap:4px}
.proj-pag .swiper-pagination-bullet{width:8px;height:8px;margin:0!important;background:var(--muted);opacity:.4;transition:.3s var(--ease)}
.proj-pag .swiper-pagination-bullet-active{width:26px;border-radius:99px;background:var(--accent);opacity:1}
.proj{display:flex;flex-direction:column}
.proj.wide{grid-column:span 30;flex-direction:row;align-items:stretch}
.proj.wide .proj-img{flex:1.2;aspect-ratio:auto;min-height:260px;margin:10px 0 10px 10px}
.proj.wide .proj-body{flex:1;justify-content:center;padding:34px}
.proj.hide{display:none}
.proj-img{position:relative;aspect-ratio:16/11;margin:10px 10px 0;border-radius:16px;overflow:hidden}
.proj-img .mock{position:absolute;inset:0;transition:transform .8s var(--ease)}
.proj:hover .proj-img .mock{transform:scale(1.05)}
.proj-img .num{position:absolute;left:18px;top:10px;font-family:'Poppins';font-size:3.4rem;font-weight:700;color:rgba(255,255,255,.28);line-height:1}
.proj-img .open{position:absolute;right:14px;top:14px;width:48px;height:48px;border-radius:50%;background:#0c0b0a;color:#f4efe6;display:grid;place-items:center;transform:scale(0) rotate(-90deg);transition:transform .5s var(--ease);z-index:3}
.proj:hover .open{transform:none}
.open svg{width:20px;height:20px}
.proj-body{padding:22px 24px 26px;flex:1;display:flex;flex-direction:column}
.proj-meta{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px}
.proj-cat{font-family:'Poppins';font-size:.74rem;color:var(--accent);text-transform:uppercase;letter-spacing:1px}
.proj-flag{font-size:.74rem;font-weight:700;color:var(--muted);padding:4px 10px;border-radius:99px;background:var(--surface-2);white-space:nowrap}
.proj-body h3{font-size:1.3rem;margin-bottom:8px}
.proj.wide .proj-body h3{font-size:1.65rem}
.proj-body p{color:var(--muted);font-size:.93rem;flex:1}
.proj.wide .proj-body p{flex:0}

.browser{position:absolute;inset:18% 10% -8% 10%;background:#fbf8f2;border-radius:12px 12px 0 0;box-shadow:0 30px 50px -15px rgba(0,0,0,.5);overflow:hidden;transition:transform .8s var(--ease)}
.proj:hover .browser{transform:translateY(-8px)}
.b-bar{height:24px;display:flex;align-items:center;gap:5px;padding:0 11px;background:#ece6da}
.b-bar i{width:8px;height:8px;border-radius:50%;background:#ff5f57}
.b-bar i:nth-child(2){background:#febc2e}.b-bar i:nth-child(3){background:#28c840}
.b-bar em{flex:1;max-width:50%;margin-left:14px;height:10px;border-radius:5px;background:#dcd4c4}
.b-body{padding:13px;display:grid;gap:8px}
.b-bar em.url{height:auto;background:none;font:600 .62rem/1 'Poppins',sans-serif;font-style:normal;color:#8a8275;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.b-body.shot{padding:0;display:block}
.b-body.shot img{display:block;width:100%;height:auto}
a.open{text-decoration:none}
.ln{height:8px;border-radius:4px;background:#e3dccd}
.blk{border-radius:8px}
.row{display:flex;gap:8px}

/* ================= PROCESS ================= */
.process{display:grid;grid-template-columns:repeat(4,1fr);position:relative}
.process::before{content:"";position:absolute;top:36px;left:36px;right:10%;height:1px;background:repeating-linear-gradient(90deg,var(--border) 0 8px,transparent 8px 16px)}
.step{padding-right:30px;position:relative}
.step .dot{width:72px;height:72px;border-radius:50%;background:var(--bg);border:1px solid var(--border);display:grid;place-items:center;position:relative;z-index:1;margin-bottom:28px;transition:.5s var(--ease)}
.step .dot svg{width:28px;height:28px;color:var(--accent);transition:.5s var(--ease)}
.step:hover .dot{background:var(--accent);border-color:var(--accent);transform:scale(1.08)}
.step:hover .dot svg{color:var(--accent-ink)}
.step .k{font-family:'Poppins';font-size:.75rem;color:var(--faint);margin-bottom:6px}
.step h3{font-size:1.4rem;margin-bottom:8px}
.step p{color:var(--muted);font-size:.93rem}

/* ================= GLOBAL ================= */
.global{display:grid;grid-template-columns:1fr 1.3fr;gap:60px;align-items:center}
.globe-wrap{max-width:440px;width:100%;justify-self:center}
.globe-wrap svg{width:100%;height:auto;display:block}
.globe-rot{animation:spin 60s linear infinite;transform-origin:220px 220px;transform-box:view-box}
.countries{display:grid;gap:16px}
.country{padding:26px 28px;display:flex;align-items:center;gap:24px;transition:transform .5s var(--ease)}
.country .code{font-family:'Poppins';font-size:2.6rem;font-weight:700;color:var(--accent);width:78px;flex:none;line-height:1}
.country h4{font-size:1.2rem;margin-bottom:2px}
.country p{color:var(--muted);font-size:.9rem}
.country .cnt{margin-left:auto;font-family:'Poppins';font-size:.72rem;color:var(--muted);white-space:nowrap;padding:5px 12px;border:1px solid var(--border);border-radius:99px}

/* ================= EDUCATION ================= */
.edu-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}
.edu{padding:30px 26px;display:flex;flex-direction:column;min-height:290px}
.edu .yr{font-family:'Poppins';font-size:.74rem;color:var(--muted);display:flex;justify-content:space-between;align-items:center}
.edu .ic{width:54px;height:54px;border-radius:16px;background:var(--surface-2);color:var(--accent);display:grid;place-items:center;margin:26px 0 20px;transition:.5s var(--ease)}
.edu .ic svg{width:26px;height:26px}
.edu:hover .ic{background:var(--accent);color:var(--accent-ink);transform:rotate(-8deg)}
.edu h3{font-size:1.15rem;margin-bottom:8px}
.edu p{color:var(--muted);font-size:.9rem;flex:1}
.edu .board{align-self:flex-start;margin-top:18px;font-family:'Poppins';font-size:.74rem;padding:5px 12px;border-radius:99px;background:var(--accent-soft);color:var(--accent)}
.edu.main{background:var(--text);color:var(--bg);border-color:transparent!important}
.edu.main::before{display:none}
.edu.main p,.edu.main .yr{color:color-mix(in srgb,var(--bg) 62%,var(--text))}
.edu.main .ic{background:var(--accent);color:var(--accent-ink)}

/* ================= CTA ================= */
.cta{padding:40px 0 0}
.cta-box{position:relative;border-radius:36px;background:var(--accent);color:var(--accent-ink);padding:clamp(48px,8vw,100px) 24px;overflow:hidden;text-align:center}
.cta-box::before,.cta-box::after{content:"";position:absolute;border-radius:50%;border:1px solid currentColor;opacity:.2}
.cta-box::before{width:520px;height:520px;left:-160px;top:-260px}
.cta-box::after{width:420px;height:420px;right:-120px;bottom:-240px}
.cta-box small{font-family:'Poppins';text-transform:uppercase;letter-spacing:3px;font-size:.78rem;opacity:.8}
.cta-box h2{font-size:clamp(2.6rem,7.4vw,6rem);font-weight:700;letter-spacing:-.045em;line-height:1;margin:18px 0 38px}
.cta-box h2 .serif{font-weight:400}
.cta-box .btn{background:#0c0b0a;color:#f4efe6}
.cta-box .btn:hover{box-shadow:0 16px 36px -12px rgba(0,0,0,.6)}

/* ================= CONTACT ================= */
.contact-grid{display:grid;grid-template-columns:1fr 1.15fr;gap:24px}
.c-list{display:grid;gap:14px;align-content:start}
.c-item{padding:22px 24px;display:flex;align-items:center;gap:18px}
.c-item .ic{flex:none;width:52px;height:52px;border-radius:50%;background:var(--surface-2);color:var(--accent);display:grid;place-items:center;transition:.4s var(--ease)}
.c-item .ic svg{width:22px;height:22px}
.c-item:hover .ic{background:var(--accent);color:var(--accent-ink)}
.c-item small{color:var(--muted);font-size:.8rem;display:block}
.c-item b{font-weight:700;word-break:break-all}
.c-item .go{margin-left:auto;width:22px;height:22px;color:var(--muted);transition:transform .4s var(--ease),color .3s;flex:none}
a.c-item:hover .go{transform:rotate(-45deg);color:var(--accent)}
form.card{padding:36px}
form h3{font-size:1.6rem;margin-bottom:6px}
form > p{color:var(--muted);font-size:.9rem;margin-bottom:26px}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.field{position:relative;margin-bottom:16px}
.field input,.field textarea{width:100%;padding:26px 16px 10px;border-radius:14px;border:1px solid var(--border);background:var(--surface-2);color:var(--text);font-family:inherit;font-size:.97rem;transition:.3s;resize:vertical}
.field label{position:absolute;left:17px;top:18px;font-size:.93rem;color:var(--muted);pointer-events:none;transition:.25s var(--ease)}
.field input:focus,.field textarea:focus{outline:none;border-color:var(--accent);background:var(--surface)}
.field input:focus + label,.field input:not(:placeholder-shown) + label,.field textarea:focus + label,.field textarea:not(:placeholder-shown) + label{top:8px;font-size:.7rem;color:var(--accent);font-weight:700;letter-spacing:.5px}
form .btn{width:100%;justify-content:center}

/* ================= FOOTER ================= */
footer{padding:90px 0 30px;overflow:hidden}
.hero-social{display:flex;align-items:center;gap:10px;margin-top:28px}
.hero-social span{font-family:'Poppins';font-size:.78rem;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-right:6px}
.hero-social a{width:42px;height:42px;border-radius:50%;border:1px solid var(--border);display:grid;place-items:center;color:var(--text);transition:.3s var(--ease)}
.hero-social a svg{width:18px;height:18px}
.hero-social a:hover{background:var(--accent);border-color:var(--accent);color:var(--accent-ink);transform:translateY(-3px)}
.f-big{font-family:'Poppins';font-weight:700;font-size:clamp(3.4rem,15.5vw,13.5rem);line-height:.85;letter-spacing:-.06em;text-align:center;white-space:nowrap;background:linear-gradient(180deg,var(--text) 20%,transparent 100%);-webkit-background-clip:text;background-clip:text;color:transparent;user-select:none}
.f-bottom{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-top:34px;padding-top:26px;border-top:1px solid var(--border);color:var(--muted);font-size:.88rem}
.f-bottom a{transition:color .3s}
.f-bottom a:hover{color:var(--accent)}
.f-links{display:flex;gap:22px;flex-wrap:wrap}

#toTop{position:fixed;right:22px;bottom:22px;z-index:90;width:52px;height:52px;opacity:0;pointer-events:none;transform:translateY(20px);background:var(--accent);color:var(--accent-ink);border:none}
#toTop.show{opacity:1;pointer-events:auto;transform:none}

/* ================= REVEAL ================= */
.reveal{opacity:0;translate:0 50px;transition:opacity 1s var(--ease),translate 1s var(--ease),transform .5s var(--ease),border-color .4s,box-shadow .5s}
.reveal.in{opacity:1;translate:0 0}
.svc:hover,.edu:hover,.proj:hover{transform:translateY(-8px);box-shadow:var(--shadow)}
.country:hover{transform:translateX(10px)}

/* ================= RESPONSIVE ================= */
@media (max-width:1100px){
  .services,.edu-grid{grid-template-columns:repeat(2,1fr)}
    .nav-links a{padding:9px 10px;font-size:.84rem}
}
@media (max-width:960px){
  .hero-grid,.global,.contact-grid{grid-template-columns:1fr}
  .hero-grid{gap:90px}
  .hero-visual{width:min(340px,80%)}
  .skill-groups{grid-template-columns:1fr}
  .bento{grid-template-columns:repeat(2,1fr)}
  .exp{grid-template-columns:1fr;gap:10px}
  .exp-loc{text-align:left}
  .process{grid-template-columns:repeat(2,1fr);row-gap:50px}
  .process::before{display:none}
  .hire{display:none}
  .proj.wide{flex-direction:column}
  .proj.wide .proj-img{margin:10px 10px 0;min-height:0;aspect-ratio:16/8}
  .proj.wide .proj-body{padding:22px 24px 26px}
}
@media (max-width:880px){
  .menu-btn{display:grid}
  .nav-links{position:fixed;top:84px;left:16px;right:16px;flex-direction:column;padding:12px;background:var(--surface);border:1px solid var(--border);border-radius:24px;box-shadow:var(--shadow);opacity:0;pointer-events:none;transform:translateY(-10px) scale(.98);transition:.35s var(--ease)}
  .nav-links.open{opacity:1;pointer-events:auto;transform:none}
  .nav-links a{display:block;padding:14px 18px;font-size:1rem}
}
@media (max-width:640px){
  section{padding:84px 0}
  .hero{padding-top:130px}
  .bento{grid-template-columns:1fr}
  .b-intro,.b-role,.b-loc,.b-hob{grid-column:span 1}
  .b-intro{grid-row:auto;padding:28px}
  .b-loc{flex-direction:column;align-items:flex-start}
  .services,.edu-grid,.process{grid-template-columns:1fr}
  .f-row{grid-template-columns:1fr}
  .spin-badge{width:100px;height:100px;left:-22px;top:-26px}
  .spin-badge text{font-size:10px;letter-spacing:1.4px}
  .spin-badge .star{width:28px;height:28px}
    .hero-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;width:100%}
  .stat b{font-size:2.2rem}
  .stat > span{font-size:.75rem;line-height:1.3;display:block;margin-top:6px}
  .hero-foot{margin-top:56px}
  .scroll-hint{display:none}
  .hero h1{font-size:clamp(3.4rem,17vw,5rem)}
  .btns .btn{flex:1;justify-content:center;padding:15px 18px}
  .sec-head{margin-bottom:40px}
  .sg{padding:22px}
  .sk-list{grid-template-columns:repeat(2,1fr)}
  .proj-body{padding:20px}
  .cta-box{border-radius:26px}
  .cta-box h2{font-size:2.5rem}
  .f-bottom{flex-direction:column;align-items:center;text-align:center}
  #toTop{right:16px;bottom:16px;width:46px;height:46px}
  .country{flex-wrap:wrap;gap:12px}
  .country .code{width:auto}
  .country .cnt{margin-left:0}
  form.card{padding:24px}
  .c-item{padding:18px}
  .c-item b{font-size:.9rem}
  .m-item{font-size:1.15rem;padding:20px 22px}
  .exp-big{font-size:5rem}
}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}html{scroll-behavior:auto}.reveal{opacity:1;translate:none}.hero h1 .line>span{transform:none}}
</style>
@endverbatim
