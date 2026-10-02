@php
    $user = auth()->user();
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $nav = [
        ['admin.dashboard', 'Dashboard', '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
        ['admin.themes', 'Themes', '<rect x="3" y="3" width="18" height="6" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="15" y="13" width="6" height="8" rx="1.5"/>'],
        ['admin.appearance', 'Appearance', '<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 0 0 18z" fill="currentColor"/>'],
        ['admin.pages', 'Site pages', '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>'],
        ['admin.messages', 'Messages', '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/>'],
        ['admin.account', 'Account', '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>'],
    ];
    // unread contact form messages, shown as a badge in the menu
    $unreadMessages = rescue(fn () => \App\Models\ContactMessage::whereNull('read_at')->count(), 0, false);
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $theme['mode'] }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('partials.favicon')
<title>@yield('title', 'Dashboard') — Admin · Anmol Saini</title>
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
@verbatim
<style>
/* ================= THEME (same tokens as the portfolio) ================= */
:root{
  --bg:#0c0b0a; --bg-2:#121110; --surface:#161514; --surface-2:#1e1d1b; --border:#2b2926;
  --text:#f4efe6; --muted:#a19b90; --faint:#6d685f;
  --accent:#ff5b2e; --accent-2:#ff8a4c; --accent-ink:#1a0d07; --accent-soft:rgba(255,91,46,.12);
  --lime:#c9f25a;
  --shadow:0 30px 60px -30px rgba(0,0,0,.8);
  --grid-line:rgba(255,255,255,.035);
  --radius:22px;
  --side:264px;
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
body{font-family:'Poppins',system-ui,sans-serif;background:var(--bg);color:var(--text);line-height:1.65;min-height:100vh;overflow-x:hidden;-webkit-font-smoothing:antialiased}
a{color:inherit;text-decoration:none}
button{font-family:inherit}
::selection{background:var(--accent);color:var(--accent-ink)}
h1,h2,h3{letter-spacing:-.02em;line-height:1.15}
.serif{font-family:'Poppins',sans-serif;font-style:italic;font-weight:400;letter-spacing:0}
.accent{color:var(--accent)}
body::before{content:"";position:fixed;inset:0;z-index:-2;background-image:linear-gradient(var(--grid-line) 1px,transparent 1px),linear-gradient(90deg,var(--grid-line) 1px,transparent 1px);background-size:72px 72px;mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at top,#000 30%,transparent 75%)}
.glow{position:fixed;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,var(--accent-soft),transparent 65%);top:-300px;right:-200px;z-index:-1;pointer-events:none}

/* ================= CURSOR (same as the public site) ================= */
.cur-dot,.cur-ring{position:fixed;top:0;left:0;pointer-events:none;z-index:998;border-radius:50%;display:none}
.cur-dot{width:6px;height:6px;background:var(--accent)}
.cur-ring{width:38px;height:38px;border:1.5px solid var(--accent);opacity:.5;transition:width .3s,height .3s,opacity .3s}
.cur-ring.hover{width:70px;height:70px;opacity:.9}
@media (hover:hover) and (pointer:fine){.cur-dot,.cur-ring{display:block}}

/* ================= SIDEBAR ================= */
.sidebar{position:fixed;inset:0 auto 0 0;width:var(--side);z-index:100;display:flex;flex-direction:column;overflow-y:auto;scrollbar-width:thin;padding:22px 16px 18px;background:var(--surface);border-right:1px solid var(--border);transition:transform .45s var(--ease)}
.logo{font-weight:700;font-size:1.2rem;display:flex;align-items:center;gap:10px;padding:4px 8px 22px}
.logo b{width:36px;height:36px;border-radius:10px;background:var(--accent);color:var(--accent-ink);display:grid;place-items:center;font-size:.95rem;transition:transform .5s var(--ease)}
.logo:hover b{transform:rotate(-12deg) scale(1.08)}
.logo small{font-family:'Poppins',sans-serif;font-weight:500;font-size:.66rem;text-transform:uppercase;letter-spacing:1.6px;color:var(--accent);padding:3px 9px;border-radius:99px;background:var(--accent-soft);margin-left:auto}
.side-label{font-family:'Poppins',sans-serif;font-size:.68rem;text-transform:uppercase;letter-spacing:2px;color:var(--faint);padding:14px 12px 8px}
.menu{list-style:none;display:grid;gap:4px}
.menu a,.menu button{width:100%;display:flex;align-items:center;gap:12px;padding:9px 12px;border-radius:14px;border:none;background:transparent;font-size:.92rem;font-weight:600;color:var(--muted);cursor:pointer;text-align:left;transition:background .25s,color .25s}
.menu svg{width:19px;height:19px;flex:none}
.menu a:hover,.menu button:hover{color:var(--text);background:var(--surface-2)}
.menu a.active{color:var(--accent-ink);background:var(--accent)}
.menu .ext{margin-left:auto;width:14px;height:14px;opacity:.6}
.menu .count{margin-left:auto;min-width:22px;padding:1px 7px;border-radius:99px;background:var(--accent);color:var(--accent-ink);font-size:.72rem;font-weight:700;text-align:center}
.menu a.active .count{background:var(--accent-ink);color:var(--accent)}
.user-menu{position:relative;flex:none}
.user{display:flex;align-items:center;gap:10px;padding:4px 12px 4px 4px;border-radius:99px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);max-width:280px;cursor:pointer;text-align:left;transition:border-color .25s}
.user:hover,.user[aria-expanded="true"]{border-color:var(--accent)}
.user .who{min-width:0}
.user .chev{width:16px;height:16px;flex:none;color:var(--muted);transition:transform .3s var(--ease)}
.user[aria-expanded="true"] .chev{transform:rotate(180deg)}
.user-drop{position:absolute;right:0;top:calc(100% + 10px);z-index:60;width:240px;padding:8px;border-radius:18px;background:var(--surface);border:1px solid var(--border);box-shadow:var(--shadow);animation:drop-in .25s var(--ease)}
.user-drop[hidden]{display:none}
@keyframes drop-in{from{opacity:0;transform:translateY(-8px)}}
.user-drop .ud-head{padding:10px 12px 12px;margin-bottom:6px;border-bottom:1px solid var(--border)}
.user-drop .ud-head b{display:block;font-size:.9rem}
.user-drop .ud-head small{display:block;color:var(--muted);font-size:.76rem;overflow-wrap:anywhere}
.user-drop a,.user-drop .ud-out{width:100%;display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;border:none;background:transparent;color:var(--text);font-family:inherit;font-size:.9rem;font-weight:600;cursor:pointer;text-align:left;transition:background .2s,color .2s}
.user-drop svg{width:17px;height:17px;flex:none;color:var(--muted);transition:color .2s}
.user-drop a:hover,.user-drop .ud-out:hover{background:var(--surface-2)}
.user-drop a:hover svg{color:var(--accent)}
.user-drop form{margin-top:6px;padding-top:6px;border-top:1px solid var(--border)}
.user-drop .ud-out:hover,.user-drop .ud-out:hover svg{color:var(--accent)}
.avatar{width:36px;height:36px;border-radius:50%;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;font-size:.85rem;font-weight:700;flex:none}
.avatar{overflow:hidden}
.avatar img{width:100%;height:100%;object-fit:cover;object-position:center 15%;display:block}
.user b{display:block;font-size:.88rem;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user small{display:block;color:var(--muted);font-size:.74rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.side-foot{margin-top:auto;padding-top:14px;position:sticky;bottom:-18px;background:var(--surface);padding-bottom:18px;margin-bottom:-18px;border-top:1px solid var(--border)}
.menu .side-logout{background:var(--accent);color:var(--accent-ink);justify-content:center;font-weight:700}
.menu .side-logout:hover{background:var(--accent);color:var(--accent-ink);box-shadow:0 10px 24px -10px var(--accent);transform:translateY(-1px)}
.overlay{position:fixed;inset:0;z-index:90;background:rgba(0,0,0,.5);opacity:0;pointer-events:none;transition:opacity .35s}

/* ================= MAIN ================= */
.main{margin-left:var(--side);min-height:100vh;display:flex;flex-direction:column}
.topbar{position:sticky;top:0;z-index:50;display:flex;align-items:center;gap:14px;padding:16px 32px;background:var(--surface);border-bottom:1px solid var(--border)}
.topbar .crumb{font-family:'Poppins',sans-serif;font-size:.74rem;text-transform:uppercase;letter-spacing:2px;color:var(--muted)}
.topbar .crumb b{color:var(--accent);font-weight:500}
.topbar .sp{flex:1}
.icon-btn{width:42px;height:42px;border-radius:50%;border:1px solid var(--border);background:var(--surface);color:var(--text);display:none;place-items:center;cursor:pointer;transition:.3s var(--ease);flex:none}
.icon-btn:hover{border-color:var(--accent);color:var(--accent)}
.icon-btn svg{width:18px;height:18px}
.btn{display:inline-flex;align-items:center;gap:10px;padding:10px 20px;border-radius:99px;font-weight:700;font-size:.86rem;border:1.5px solid var(--border);color:var(--text);background:transparent;cursor:pointer;white-space:nowrap;transition:transform .35s var(--ease),border-color .3s,box-shadow .35s}
.btn svg{width:16px;height:16px;transition:transform .35s var(--ease)}
.btn:hover{border-color:var(--text);transform:translateY(-2px)}
.btn:hover svg{transform:translate(2px,-2px)}
.btn-primary{background:var(--accent);border-color:var(--accent);color:var(--accent-ink);padding:13px 24px;font-size:.92rem}
.btn-primary:hover{border-color:var(--accent);box-shadow:0 16px 36px -12px var(--accent)}
.btn-primary:hover svg{transform:none}
.content{flex:1;padding:20px;width:100%;max-width:1180px}
footer{padding:20px 32px 30px;color:var(--faint);font-size:.8rem}

/* ================= PAGE HEAD ================= */
.head{margin-bottom:30px}
.eyebrow{display:inline-flex;align-items:center;gap:10px;font-family:'Poppins',sans-serif;font-size:.78rem;color:var(--accent);text-transform:uppercase;letter-spacing:2px;margin-bottom:12px}
.eyebrow::before{content:"";width:28px;height:1.5px;background:var(--accent)}
.head h1{font-size:clamp(1.9rem,3.6vw,2.8rem);font-weight:700;letter-spacing:-.03em;line-height:1.1}
.head p{color:var(--muted);margin-top:8px;max-width:560px}

/* ================= CARDS ================= */
.card{position:relative;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:26px;overflow:hidden;isolation:isolate;transition:border-color .35s;animation:rise .7s var(--ease) both}
.card::before{content:"";position:absolute;inset:0;z-index:-1;opacity:0;transition:opacity .4s;background:radial-gradient(420px circle at var(--mx,50%) var(--my,50%),var(--accent-soft),transparent 45%)}
.card:hover::before{opacity:1}
@keyframes rise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
.card-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px}
.card-head h2{font-size:1.15rem;font-weight:700}
.card-head span,.card-head a{font-family:'Poppins',sans-serif;font-size:.72rem;text-transform:uppercase;letter-spacing:1.6px;color:var(--muted)}
.card-head a{color:var(--accent)}
.card-head a:hover{text-decoration:underline}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:18px}
.stat .ic{width:44px;height:44px;border-radius:14px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;margin-bottom:16px}
.stat .ic svg{width:20px;height:20px}
.stat b{display:block;font-size:1.55rem;font-weight:700;letter-spacing:-.02em;line-height:1.15;overflow-wrap:anywhere;text-transform:capitalize}
.stat span{color:var(--muted);font-size:.85rem}
.cols{display:grid;grid-template-columns:1.5fr 1fr;gap:18px;align-items:start}
.stack{display:grid;gap:18px}

.pages{list-style:none}
.pages a{display:flex;align-items:center;gap:14px;padding:12px;margin:0 -12px;border-radius:14px;transition:background .25s}
.pages li + li{border-top:1px solid var(--border)}
.pages a:hover{background:var(--surface-2)}
.pages .num{font-family:'Poppins',sans-serif;font-size:.75rem;color:var(--faint);width:22px;flex:none}
.pages .info{flex:1;min-width:0}
.pages .info b{display:block;font-weight:600;font-size:.95rem}
.pages .info small{display:block;color:var(--muted);font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pages .path{font-family:'Poppins',sans-serif;font-size:.76rem;color:var(--muted);padding:4px 10px;border-radius:99px;border:1px solid var(--border);white-space:nowrap}
.pages .go{width:34px;height:34px;border-radius:50%;border:1px solid var(--border);display:grid;place-items:center;color:var(--muted);flex:none;transition:.3s var(--ease)}
.pages .go svg{width:15px;height:15px}
.pages a:hover .go{background:var(--accent);border-color:var(--accent);color:var(--accent-ink);transform:rotate(45deg)}

.rows{list-style:none}
.rows li{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:11px 0;font-size:.88rem}
.rows li + li{border-top:1px solid var(--border)}
.rows span{color:var(--muted)}
.rows b{font-family:'Poppins',sans-serif;font-weight:500;font-size:.82rem;text-align:right;overflow-wrap:anywhere}
.rows b.cap{text-transform:capitalize}
.dot{display:inline-block;width:12px;height:12px;border-radius:50%;background:var(--accent);margin-right:8px;vertical-align:-1px}
.badge{display:inline-flex;align-items:center;gap:8px;font-family:'Poppins',sans-serif;font-size:.72rem;text-transform:uppercase;letter-spacing:1.4px;padding:4px 10px;border-radius:99px;background:var(--accent-soft);color:var(--accent)!important}
.profile{display:flex;align-items:center;gap:16px;margin-bottom:18px}
.profile .avatar{width:60px;height:60px;font-size:1.15rem;border-radius:20px}
.profile b{display:block;font-size:1.1rem;line-height:1.3}
.profile small{color:var(--muted);font-size:.86rem;overflow-wrap:anywhere}

/* ================= CONTENT FORMS ================= */
.tabs{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:22px}
.tabs a{padding:8px 16px;border-radius:99px;border:1px solid var(--border);background:var(--surface);font-size:.84rem;font-weight:600;color:var(--muted);transition:.3s var(--ease)}
.tabs a:hover{color:var(--text);border-color:var(--faint)}
.tabs a.active{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px 22px}
.fld{min-width:0}
.fld.wide{grid-column:1/-1}
.fld > label,.fld > .lbl{display:block;font-size:.84rem;font-weight:600;margin-bottom:8px}
.fld .req{color:var(--accent)}
.fld input[type=text],.fld input[type=password],.fld input[type=url],.fld input[type=number],.fld textarea,.fld select{width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--border);background:var(--bg-2);color:var(--text);font-family:inherit;font-size:.92rem;transition:border-color .25s,box-shadow .25s}
.fld textarea{resize:vertical;min-height:96px;line-height:1.55}
.fld input:focus,.fld textarea:focus,.fld select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
.fld input[type=file]{width:100%;font-family:inherit;font-size:.86rem;color:var(--muted)}
.fld input[type=file]::file-selector-button{margin-right:12px;padding:9px 16px;border-radius:99px;border:1px solid var(--border);background:var(--surface-2);color:var(--text);font-family:inherit;font-weight:600;cursor:pointer}
.fld input[type=color]{width:56px;height:44px;padding:4px;border-radius:12px;border:1px solid var(--border);background:var(--bg-2);cursor:pointer}
.fld .hint{display:block;margin-top:6px;font-size:.78rem;color:var(--muted)}
.fld .err{display:block;margin-top:6px;font-size:.8rem;color:var(--accent);font-weight:600}
.fld .check{display:inline-flex;align-items:center;gap:10px;font-size:.9rem;font-weight:600;cursor:pointer}
.fld .check input{width:18px;height:18px;accent-color:var(--accent)}
.fld .thumb{display:flex;align-items:center;gap:14px;margin-bottom:10px}
.fld .thumb img{width:96px;height:72px;object-fit:cover;border-radius:10px;border:1px solid var(--border);background:var(--surface-2)}
.fld .thumb a{color:var(--accent);font-size:.84rem;font-weight:600}
.icons{display:flex;flex-wrap:wrap;gap:8px}
.icons label{cursor:pointer}
.icons input{position:absolute;opacity:0;pointer-events:none}
.icons span{width:44px;height:44px;border-radius:12px;border:1.5px solid var(--border);background:var(--bg-2);display:grid;place-items:center;color:var(--muted);transition:.25s var(--ease)}
.icons svg{width:20px;height:20px}
.icons input:checked + span{border-color:var(--accent);background:var(--accent-soft);color:var(--accent)}
.icons input:focus-visible + span{box-shadow:0 0 0 3px var(--accent-soft)}
.checks{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:12px 18px}
.form-foot{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:26px;padding-top:22px;border-top:1px solid var(--border)}
.list{list-style:none}
.list li{display:flex;align-items:center;gap:14px;padding:12px 0}
.list li + li{border-top:1px solid var(--border)}
.list .pos{font-size:.76rem;color:var(--faint);width:26px;flex:none}
.list img,.list .ph{width:64px;height:46px;border-radius:8px;object-fit:cover;border:1px solid var(--border);flex:none}
.list .ph{display:block}
.list .info{flex:1;min-width:0}
.list .info b{display:block;font-weight:600;font-size:.95rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.list .info small{display:block;color:var(--muted);font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.list .acts{display:flex;gap:8px;flex:none}
.sm{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:99px;border:1px solid var(--border);background:transparent;color:var(--text);font-family:inherit;font-size:.8rem;font-weight:600;cursor:pointer;transition:.25s var(--ease)}
.sm:hover{border-color:var(--text)}
.sm.danger:hover{border-color:var(--accent);color:var(--accent)}
.empty{padding:26px 0;color:var(--muted);text-align:center}
.head-row{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap}
@media (max-width:760px){.form-grid{grid-template-columns:1fr}}

/* ================= APPEARANCE FORM ================= */
fieldset{border:none;min-width:0;margin-bottom:26px}
legend{font-family:'Poppins',sans-serif;font-size:.72rem;text-transform:uppercase;letter-spacing:1.6px;color:var(--muted);margin-bottom:12px}
.modes{display:inline-flex;padding:5px;border-radius:99px;border:1px solid var(--border);background:var(--bg-2)}
.mode{cursor:pointer}
.mode input,.swatch input{position:absolute;opacity:0;pointer-events:none}
.mode span{display:flex;align-items:center;gap:8px;padding:10px 22px;border-radius:99px;font-weight:600;font-size:.88rem;color:var(--muted);transition:.3s var(--ease)}
.mode svg{width:16px;height:16px}
.mode input:checked + span{background:var(--accent);color:var(--accent-ink)}
.mode input:focus-visible + span{box-shadow:0 0 0 3px var(--accent-soft)}
.swatches{display:flex;flex-wrap:wrap;gap:10px}
.swatch{display:flex;align-items:center;gap:10px;padding:7px 16px 7px 7px;border-radius:99px;border:1.5px solid var(--border);background:var(--bg-2);cursor:pointer;font-size:.86rem;font-weight:600;color:var(--muted);transition:.3s var(--ease)}
.swatch i{width:28px;height:28px;border-radius:50%;display:block;flex:none;transition:transform .3s var(--ease)}
.swatch:hover{border-color:var(--faint);color:var(--text)}
.swatch:hover i{transform:scale(1.1)}
.swatch:has(input:checked){border-color:var(--accent);color:var(--text);background:var(--accent-soft)}
.swatch:has(input:focus-visible){box-shadow:0 0 0 3px var(--accent-soft)}
.note{padding:11px 14px;border-radius:14px;font-size:.88rem;margin-bottom:20px;border:1px solid var(--border)}
.note.ok{border-color:var(--lime);color:var(--lime)}
.note.bad{border-color:var(--accent);color:var(--accent)}
.hint{margin-top:16px;font-size:.84rem;color:var(--muted)}
.preview{display:grid;gap:14px}
.preview .pv-h{font-size:1.5rem;font-weight:700;letter-spacing:-.03em}
.preview .pv-row{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.preview .pv-chip{font-family:'Poppins',sans-serif;font-size:.72rem;text-transform:uppercase;letter-spacing:1.4px;padding:5px 12px;border-radius:99px;background:var(--accent-soft);color:var(--accent)}
.preview .pv-btn{padding:10px 20px;border-radius:99px;background:var(--accent);color:var(--accent-ink);font-weight:700;font-size:.85rem}
.preview .pv-bar{height:6px;border-radius:99px;background:linear-gradient(90deg,var(--accent),var(--accent-2))}

@media (max-width:1100px){
  .stats{grid-template-columns:repeat(2,1fr)}
  .topbar .user small{display:none}
  .cols{grid-template-columns:1fr}
}
@media (max-width:860px){
  .sidebar{transform:translateX(-100%);box-shadow:var(--shadow)}
  body.nav-open .sidebar{transform:none}
  body.nav-open .overlay{opacity:1;pointer-events:auto}
  .main{margin-left:0}
  .icon-btn{display:grid}
  .topbar{padding:12px 16px}
  .content{padding:20px 16px}
  footer{padding:16px 16px 26px}
}
@media (max-width:560px){
  .topbar .btn span{display:none}
  .topbar .btn{padding:12px}
  .topbar .user .who,.topbar .user .chev{display:none}
  .topbar .user{padding:4px}
  .pages .path{display:none}
  .card{padding:22px 18px}
  .stat b{font-size:1.3rem}
}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
</style>
@endverbatim
@include('partials.theme')
@stack('styles')
</head>
<body>
<div class="cur-dot" id="curDot"></div><div class="cur-ring" id="curRing"></div>
<div class="glow"></div>

<aside class="sidebar" id="sidebar">
  <a href="{{ route('admin.dashboard') }}" class="logo"><b>AS</b>Anmol <small>Admin</small></a>

  <div class="side-label">Menu</div>
  <ul class="menu">
    @foreach ($nav as [$route, $label, $icon])
      <li>
        <a href="{{ route($route) }}" @class(['active' => request()->routeIs($route, $route.'.*')]) @if (request()->routeIs($route, $route.'.*')) aria-current="page" @endif>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
          {{ $label }}
          @if ($route === 'admin.messages' && $unreadMessages)
            <span class="count">{{ $unreadMessages }}</span>
          @endif
        </a>
      </li>
    @endforeach
  </ul>

  <div class="side-label">Content</div>
  <ul class="menu">
    <li>
      <a href="{{ route('admin.content') }}" @class(['active' => request()->routeIs('admin.content')])>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
        Page content
      </a>
    </li>
    @foreach (config('content.collections') as $key => $collection)
      <li>
        <a href="{{ route('admin.items.index', $key) }}" @class(['active' => request()->routeIs('admin.items.*') && request()->route('type') === $key])>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
          {{ $collection['label'] }}
        </a>
      </li>
    @endforeach
  </ul>

  <div class="side-label">Site</div>
  <ul class="menu">
    <li>
      <a href="{{ url('/') }}" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20"/></svg>
        View portfolio
        <svg class="ext" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg>
      </a>
    </li>
  </ul>

  <div class="side-foot">
    <form method="POST" action="{{ route('logout') }}" class="menu">
      @csrf
      <button class="side-logout" type="submit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Logout
      </button>
    </form>
  </div>
</aside>
<div class="overlay" id="overlay"></div>

<div class="main">
  <header class="topbar">
    <button class="icon-btn" id="menuBtn" type="button" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 8h16M4 16h16"/></svg>
    </button>
    <div class="crumb">Admin / <b>@yield('title', 'Dashboard')</b></div>
    <div class="sp"></div>
    <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn"><span>View portfolio</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg></a>
    <div class="user-menu" id="userMenu">
      <button class="user" id="userBtn" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="userDrop">
        <span class="avatar">@if ($user->avatar)<img src="{{ media($user->avatar) }}" alt="">@else{{ $initials }}@endif</span>
        <span class="who"><b>{{ $user->name }}</b><small>{{ $user->email }}</small></span>
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <div class="user-drop" id="userDrop" hidden>
        <div class="ud-head"><b>{{ $user->name }}</b><small>{{ $user->email }}</small></div>
        <a href="{{ route('admin.account') }}#profile">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>Profile</a>
        <a href="{{ route('admin.account') }}#password">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Change password</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="ud-out">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>Logout</button>
        </form>
      </div>
    </div>
  </header>

  <main class="content">
    @yield('content')
  </main>

  <footer>© {{ date('Y') }} {{ content('site.name') }} · Admin panel</footer>
</div>

<script>
// ---------- User menu (header) ----------
const userBtn=document.getElementById('userBtn'), userDrop=document.getElementById('userDrop');
function setUserMenu(open){userDrop.hidden=!open;userBtn.setAttribute('aria-expanded',open)}
userBtn.addEventListener('click',e=>{e.stopPropagation();setUserMenu(userDrop.hidden)});
document.addEventListener('click',e=>{if(!e.target.closest('#userMenu'))setUserMenu(false)});
addEventListener('keydown',e=>{if(e.key==='Escape'){setUserMenu(false)}});

// ---------- Sidebar (mobile) ----------
const menuBtn=document.getElementById('menuBtn');
function setNav(open){document.body.classList.toggle('nav-open',open);menuBtn.setAttribute('aria-expanded',open)}
menuBtn.addEventListener('click',()=>setNav(!document.body.classList.contains('nav-open')));
document.getElementById('overlay').addEventListener('click',()=>setNav(false));
addEventListener('keydown',e=>{if(e.key==='Escape')setNav(false)});

// ---------- Custom cursor (desktop only, same as the public site) ----------
if(matchMedia('(hover:hover) and (pointer:fine)').matches && !matchMedia('(prefers-reduced-motion:reduce)').matches){
  const dot=document.getElementById('curDot'), ring=document.getElementById('curRing');
  let mx=-100,my=-100,rx=mx,ry=my;
  addEventListener('pointermove',e=>{mx=e.clientX;my=e.clientY;dot.style.transform=`translate(${mx}px,${my}px) translate(-50%,-50%)`});
  (function loop(){rx+=(mx-rx)*.16;ry+=(my-ry)*.16;ring.style.transform=`translate(${rx}px,${ry}px) translate(-50%,-50%)`;requestAnimationFrame(loop)})();
  document.addEventListener('pointerover',e=>ring.classList.toggle('hover',!!e.target.closest('a,button,label,select,.card')));
}

// ---------- Spotlight cards ----------
document.addEventListener('pointermove',e=>{
  const c=e.target.closest && e.target.closest('.card'); if(!c) return;
  const r=c.getBoundingClientRect();
  c.style.setProperty('--mx',(e.clientX-r.left)+'px'); c.style.setProperty('--my',(e.clientY-r.top)+'px');
});
</script>
@include('admin.partials.confirm')
@include('admin.partials.toast')
@stack('scripts')
</body>
</html>
