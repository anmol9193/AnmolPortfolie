<!DOCTYPE html>
<html lang="en" data-theme="{{ $theme['mode'] }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.favicon')
<title>Admin Login — {{ content('site.name') }}</title>
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
@verbatim
<style>
/* Tokens and components copied from the home page (welcome.blade.php) so both look the same */
:root{
  --bg:#0c0b0a; --bg-2:#121110; --surface:#161514; --surface-2:#1e1d1b; --border:#2b2926;
  --text:#f4efe6; --muted:#a19b90; --faint:#6d685f;
  --accent:#ff5b2e; --accent-2:#ff8a4c; --accent-ink:#1a0d07; --accent-soft:rgba(255,91,46,.12);
  --lime:#c9f25a;
  --shadow:0 30px 60px -30px rgba(0,0,0,.8);
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
  --grid-line:rgba(0,0,0,.045);
}
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%;overflow:hidden}
body{font-family:'Poppins',system-ui,sans-serif;background:var(--bg);color:var(--text);line-height:1.65;-webkit-font-smoothing:antialiased}
a{color:inherit;text-decoration:none}
button{font-family:inherit}
::selection{background:var(--accent);color:var(--accent-ink)}
h1,h2,h3{font-family:'Poppins',sans-serif;letter-spacing:-.02em;line-height:1.15}
.serif{font-family:'Poppins',sans-serif;font-style:italic;font-weight:400;letter-spacing:0}

/* grid backdrop + grain + glow (home page) */
body::before{content:"";position:fixed;inset:0;z-index:-2;background-image:linear-gradient(var(--grid-line) 1px,transparent 1px),linear-gradient(90deg,var(--grid-line) 1px,transparent 1px);background-size:72px 72px;mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%)}
body::after{content:"";position:fixed;inset:0;z-index:999;pointer-events:none;opacity:.05;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}
.glow{position:fixed;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,var(--accent-soft),transparent 65%);top:-300px;right:-200px;z-index:-1;pointer-events:none}

/* ================= CURSOR (home page) ================= */
.cur-dot,.cur-ring{position:fixed;top:0;left:0;pointer-events:none;z-index:998;border-radius:50%;display:none}
.cur-dot{width:6px;height:6px;background:var(--accent)}
.cur-ring{width:38px;height:38px;border:1.5px solid var(--accent);opacity:.5;transition:width .3s,height .3s,opacity .3s,background .3s}
.cur-ring.hover{width:70px;height:70px;opacity:.9;background:var(--accent-soft)}
@media (hover:hover) and (pointer:fine){.cur-dot,.cur-ring{display:block}}

/* ================= LAYOUT ================= */
.split{width:min(1200px,100% - 32px);margin-inline:auto;height:100vh;height:100dvh;display:grid;grid-template-columns:1.25fr .75fr;gap:70px;align-items:center}
.intro,.side{min-width:0}

/* ================= LEFT: HERO (home page) ================= */
.status{display:inline-flex;align-items:center;gap:10px;padding:8px 16px 8px 12px;border-radius:99px;border:1px solid var(--border);background:var(--surface);font-size:.85rem;font-weight:600;color:var(--muted);margin-bottom:30px}
.status .pulse{flex:none;width:10px;height:10px;border-radius:50%;background:var(--lime);position:relative}
.status .pulse::after{content:"";position:absolute;inset:0;border-radius:50%;background:var(--lime);animation:ping 1.8s var(--ease) infinite}
@keyframes ping{to{transform:scale(2.8);opacity:0}}
.intro h1{font-size:clamp(3.4rem,9vw,7.6rem);font-weight:700;line-height:.95;letter-spacing:-.045em;margin-bottom:28px}
.intro h1 .line{display:block;overflow:hidden;padding-bottom:.06em}
.intro h1 .line > span{display:inline-block;animation:up 1.1s var(--ease) both}
.intro h1 .line:nth-child(2) > span{animation-delay:.12s}
@keyframes up{from{transform:translateY(110%)}to{transform:none}}
.intro h1 .serif{font-size:1.08em;color:var(--accent)}
.hero-sub{font-size:clamp(1.05rem,1.6vw,1.22rem);color:var(--muted);max-width:560px;margin-bottom:16px}
.hero-sub b,.hero-sub strong{color:var(--text);font-weight:600}
.role{font-family:'Poppins',sans-serif;font-size:.92rem;color:var(--text);min-height:1.6em}
.role .prompt{color:var(--accent)}
.cursor{display:inline-block;width:9px;height:1.1em;background:var(--accent);vertical-align:-3px;margin-left:2px;animation:blink 1s steps(1) infinite}
@keyframes blink{50%{opacity:0}}

/* ================= RIGHT: FORM CARD (home contact form) ================= */
.eyebrow{display:inline-flex;align-items:center;gap:10px;font-family:'Poppins',sans-serif;font-size:.78rem;color:var(--accent);text-transform:uppercase;letter-spacing:2px;margin-bottom:16px}
.eyebrow::before{content:"";width:28px;height:1.5px;background:var(--accent)}
.card{position:relative;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;isolation:isolate;box-shadow:var(--shadow)}
.card::before{content:"";position:absolute;inset:0;z-index:-1;opacity:0;transition:opacity .4s;background:radial-gradient(420px circle at var(--mx,50%) var(--my,50%),var(--accent-soft),transparent 45%)}
.card:hover::before{opacity:1}
.card:hover{border-color:color-mix(in srgb,var(--accent) 45%,var(--border))}
form.card{padding:36px}
form h3{font-size:1.6rem;margin-bottom:6px}
form h3 .serif{color:var(--accent);font-size:1.12em}
form > p{color:var(--muted);font-size:.9rem;margin-bottom:26px}
.error{padding:12px 16px;border-radius:14px;border:1px solid var(--accent);background:var(--accent-soft);color:var(--text);font-size:.9rem;margin-bottom:16px}
.field{margin-bottom:16px}
.control{position:relative}
.field input{width:100%;padding:15px 16px;border-radius:14px;border:1px solid var(--border);background:var(--surface-2);color:var(--text);font-family:inherit;font-size:.97rem;transition:.3s}
.field label{display:block;font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:8px}
.field input::placeholder{color:var(--faint)}
.field input:focus{outline:none;border-color:var(--accent);background:var(--surface)}
.field.has-eye input{padding-right:56px}
.eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);width:38px;height:38px;border:none;border-radius:50%;background:transparent;color:var(--muted);display:grid;place-items:center;cursor:pointer;transition:.3s}
.eye:hover,.eye:focus-visible{color:var(--accent);outline:none}
.eye svg{width:18px;height:18px}
.eye .off{display:none}
.eye.on .off{display:block}
.eye.on .show{display:none}
.remember{display:inline-flex;align-items:center;gap:10px;font-size:.9rem;color:var(--muted);cursor:pointer;margin:4px 0 24px}
.remember input{width:18px;height:18px;accent-color:var(--accent);cursor:pointer}
.btn{display:inline-flex;align-items:center;gap:12px;padding:16px 28px;border-radius:99px;font-weight:700;font-size:.95rem;transition:transform .35s var(--ease),box-shadow .35s,background .3s,color .3s,border-color .3s;cursor:pointer;border:none;position:relative}
.btn svg{width:18px;height:18px;transition:transform .35s var(--ease)}
.btn:hover svg{transform:translateX(4px) rotate(-45deg)}
.btn-primary{background:var(--accent);color:var(--accent-ink)}
.btn-primary:hover{box-shadow:0 16px 36px -12px var(--accent)}
form .btn{width:100%;justify-content:center}
.back{display:inline-flex;align-items:center;gap:12px;margin-top:22px;font-family:'Poppins',sans-serif;font-size:.75rem;color:var(--muted);text-transform:uppercase;letter-spacing:2px;transition:color .3s}
.back:hover{color:var(--accent)}
.back svg{width:16px;height:16px;transition:transform .35s var(--ease)}
.back:hover svg{transform:translateX(-4px)}

/* ================= EXTRAS ================= */
/* left: what the admin panel can do */
.feats{display:flex;flex-wrap:wrap;gap:10px;margin-top:30px}
.feat{display:inline-flex;align-items:center;gap:10px;padding:9px 16px 9px 9px;border-radius:99px;border:1px solid var(--border);background:var(--surface);font-size:.86rem;font-weight:600;color:var(--muted);transition:.3s var(--ease)}
.feat i{width:30px;height:30px;border-radius:50%;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;flex:none}
.feat svg{width:15px;height:15px}
.feat:hover{border-color:var(--accent);color:var(--text);transform:translateY(-3px)}
/* staggered entrance */
.fade{animation:fade-up .9s var(--ease) both;animation-delay:var(--d,0s)}
@keyframes fade-up{from{opacity:0;transform:translateY(18px)}}
/* right: card */
.side{position:relative}
@keyframes spin{to{transform:rotate(360deg)}}
form.card::after{content:"";position:absolute;inset:0 0 auto 0;height:4px;background:linear-gradient(90deg,var(--accent),var(--accent-2))}
.card-top{display:flex;align-items:center;gap:16px;margin-bottom:24px}
.card-top h3{margin-bottom:2px}
.card-top p{color:var(--muted);font-size:.88rem}
.control > .lead{position:absolute;left:16px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--faint);pointer-events:none;transition:color .3s}
.control:focus-within > .lead{color:var(--accent)}
.field .control input{padding-left:46px}
.field input:focus{box-shadow:0 0 0 4px var(--accent-soft)}
.caps{display:none;align-items:center;gap:8px;margin-top:8px;font-size:.8rem;font-weight:600;color:var(--accent)}
.caps.on{display:flex}
.caps svg{width:14px;height:14px}
.btn.busy{pointer-events:none;opacity:.85}
.btn.busy svg{animation:spin .8s linear infinite}
.secure{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:16px;font-size:.78rem;color:var(--faint)}
.secure svg{width:13px;height:13px}
.foot-row{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap}
.foot-row .copy{margin-top:22px;font-size:.75rem;color:var(--faint)}

/* short screens: tighten so the page never needs to scroll */
@media (max-height:720px){
  .feats{margin-top:20px}
  .card-top{margin-bottom:18px}
  .secure{margin-top:12px}
  .status{margin-bottom:20px}
  .intro h1{font-size:clamp(3rem,7vw,5.4rem);margin-bottom:18px}
  form.card{padding:28px}
  form > p{margin-bottom:18px}
  .remember{margin-bottom:18px}
  .back{margin-top:16px}
}
@media (max-height:620px){
  .feats,.secure{display:none}
}
@media (max-height:560px){
  .eyebrow{display:none}
}
@media (max-width:900px){
  .split{grid-template-columns:1fr;gap:22px;align-content:center}
  .status{margin-bottom:14px;font-size:.8rem}
  .intro h1{font-size:clamp(2.6rem,12vw,3.6rem);margin-bottom:0}
  .intro h1 .line{display:inline}
  .hero-sub,.role,.eyebrow,.feats,.foot-row .copy{display:none}
  .card-top{margin-bottom:18px}
  form.card{padding:24px 20px}
  form h3{font-size:1.35rem}
  form > p{margin-bottom:18px}
  .remember{margin-bottom:18px}
  .back{margin-top:16px}
}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
</style>
@endverbatim
@include('partials.theme')
</head>
<body>
<div class="cur-dot" id="curDot"></div><div class="cur-ring" id="curRing"></div>
<div class="glow"></div>

<div class="split">

  <div class="intro">
    @if (content('login.status'))
      <div class="status fade"><span class="pulse"></span>{{ content('login.status') }}</div>
    @endif
    <h1>
      <span class="line"><span>{{ content('hero.first_name') }}</span></span>
      <span class="line"><span>{{ content('hero.last_name') }}<span class="serif">.</span></span></span>
    </h1>
    <p class="hero-sub fade" style="--d:.25s">{{ rich(content('login.text')) }}</p>
    <div class="role fade" style="--d:.35s"><span class="prompt">~/{{ Str::slug(content('hero.first_name')) ?: 'me' }} $</span> <span id="typed" data-text="{{ content('login.prompt') }}">{{ content('login.prompt') }}</span><span class="cursor"></span></div>
    <div class="feats fade" style="--d:.5s">
      <span class="feat"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></i>Edit every text</span>
      <span class="feat"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 0 0 18z" fill="currentColor"/></svg></i>Themes &amp; colors</span>
      <span class="feat"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></i>Projects &amp; CV</span>
    </div>
  </div>

  <main class="side fade" style="--d:.2s">
    <div class="eyebrow">{{ content('login.eyebrow') }}</div>
    <form class="card" id="loginForm" method="POST" action="{{ route('login') }}">
      @csrf
      <div class="card-top">
        <div>
          <h3>{{ rich(content('login.title')) }}</h3>
          <p>{{ content('login.note') }}</p>
        </div>
      </div>

      @if ($errors->any())
        <div class="error" role="alert">{{ $errors->first() }}</div>
      @endif

      <div class="field">
        <label for="email">Your email</label>
        <div class="control">
          <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/></svg>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
        </div>
      </div>
      <div class="field has-eye">
        <label for="password">Password</label>
        <div class="control">
          <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
          <button class="eye" id="eyeBtn" type="button" aria-label="Show password" aria-pressed="false">
          <svg class="show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a20.3 20.3 0 0 1 5.06-5.94M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a20.5 20.5 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24M1 1l22 22"/></svg>
        </button>
        </div>
        <div class="caps" id="capsHint" role="status"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 9h-5v5H9v-5H4zM9 21h6"/></svg>Caps Lock is on</div>
      </div>

      <label class="remember"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Remember me</label>

      <button class="btn btn-primary" id="submitBtn" type="submit"><span>{{ content('login.button') }}</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
      <div class="secure"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Private area · only the site owner can sign in</div>
    </form>

    <div class="foot-row">
      <a class="back" href="{{ url('/') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ content('login.back') }}
      </a>
      <span class="copy">© {{ date('Y') }} {{ content('site.name') }}</span>
    </div>
  </main>

</div>

<script>
// ---------- Show / hide password ----------
const eye=document.getElementById('eyeBtn'), pw=document.getElementById('password');
eye.addEventListener('click',()=>{
  const show=pw.type==='password';
  pw.type=show?'text':'password';
  eye.classList.toggle('on',show);
  eye.setAttribute('aria-pressed',show);
  eye.setAttribute('aria-label',show?'Hide password':'Show password');
});

// ---------- Caps Lock warning ----------
const caps=document.getElementById('capsHint');
['keydown','keyup'].forEach(t=>pw.addEventListener(t,e=>{
  if(e.getModifierState) caps.classList.toggle('on',e.getModifierState('CapsLock'));
}));
pw.addEventListener('blur',()=>caps.classList.remove('on'));

// ---------- Button loading state ----------
document.getElementById('loginForm').addEventListener('submit',()=>{
  const btn=document.getElementById('submitBtn');
  btn.classList.add('busy');
  btn.querySelector('span').textContent='Signing in…';
  btn.querySelector('svg').innerHTML='<path d="M21 12a9 9 0 1 1-6.2-8.56"/>';
});

// ---------- Type the terminal line once ----------
(function(){
  const el=document.getElementById('typed');
  if(!el||matchMedia('(prefers-reduced-motion:reduce)').matches) return;
  const text=el.dataset.text; let i=0; el.textContent='';
  setTimeout(function type(){el.textContent=text.slice(0,++i);if(i<text.length)setTimeout(type,70)},900);
})();

// ---------- Spotlight card (same as home) ----------
document.addEventListener('pointermove',e=>{
  const c=e.target.closest && e.target.closest('.card'); if(!c) return;
  const r=c.getBoundingClientRect();
  c.style.setProperty('--mx',(e.clientX-r.left)+'px'); c.style.setProperty('--my',(e.clientY-r.top)+'px');
});

// ---------- Custom cursor (desktop only, same as home) ----------
if(matchMedia('(hover:hover) and (pointer:fine)').matches && !matchMedia('(prefers-reduced-motion:reduce)').matches){
  const dot=document.getElementById('curDot'), ring=document.getElementById('curRing');
  let mx=-100,my=-100,rx=mx,ry=my;
  addEventListener('pointermove',e=>{mx=e.clientX;my=e.clientY;dot.style.transform=`translate(${mx}px,${my}px) translate(-50%,-50%)`});
  (function loop(){rx+=(mx-rx)*.16;ry+=(my-ry)*.16;ring.style.transform=`translate(${rx}px,${ry}px) translate(-50%,-50%)`;requestAnimationFrame(loop)})();
  document.addEventListener('pointerover',e=>ring.classList.toggle('hover',!!e.target.closest('a,button,.card')));
}
</script>
</body>
</html>
