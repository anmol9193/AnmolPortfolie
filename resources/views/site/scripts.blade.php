{{-- Data from the admin panel for the page script below. --}}
@php
    $siteData = [
        'words' => \App\Support\Content::lines('hero.typed'),
        'strip' => \App\Support\Content::lines('services.strip'),
        'tech' => items('skills')
            ->filter(fn ($s) => ($s->field('logo') || $s->field('devicon')) && $s->field('marquee', true))
            ->map(fn ($s) => [$s->field('devicon'), $s->field('name'), media($s->field('logo'))])
            ->values(),
        'projects' => items('projects')->map(fn ($p) => [
            't' => $p->field('title'),
            'c' => $p->field('category'),
            'f' => Str::slug($p->field('filter')),
            'flag' => $p->field('flag'),
            'd' => $p->field('description'),
            'tags' => $p->list('tags'),
            'g' => [$p->field('color_1', '#ff5b2e'), $p->field('color_2', '#b8321a')],
            'img' => media($p->field('image')),
            'url' => $p->field('url'),
        ])->values(),
    ];
@endphp
<script>window.SITE = @json($siteData);</script>
@verbatim
<script>
// Every block checks that its elements exist, because inner pages only include some sections.
const $id=id=>document.getElementById(id);
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

// ---------- Preloader ----------
(function(){
  const bar=$id('lBar'), cnt=$id('lCount'), loader=$id('loader');
  let p=0; const t=setInterval(()=>{
    p=Math.min(100,p+Math.ceil(Math.random()*14));
    bar.style.width=p+'%'; cnt.textContent=p+'%';
    if(p>=100){clearInterval(t);setTimeout(()=>{loader.classList.add('done');document.body.classList.remove('loading');document.body.classList.add('ready')},250)}
  },60);
})();

// ---------- Marquees ----------
if($id('techTrack') && SITE.tech.length){
  const techHTML=SITE.tech.map(([c,n,logo])=>`<div class="m-item">${logo?`<img src="${esc(logo)}" alt="">`:`<i class="devicon-${esc(c)} colored"></i>`}${esc(n)}</div>`).join('');
  $id('techTrack').innerHTML=techHTML+techHTML;
}
if($id('strip') && SITE.strip.length){
  const star='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2.6 9.4L24 12l-9.4 2.6L12 24l-2.6-9.4L0 12l9.4-2.6z"/></svg>';
  const stripHTML=SITE.strip.map(w=>`<div class="m-item">${esc(w)} ${star}</div>`).join('');
  $id('strip').innerHTML=stripHTML+stripHTML;
}

// ---------- Projects ----------
if($id('projGrid')){
  const projects=SITE.projects;
  const mockBody=a=>{
    const L=w=>`<div class="ln" style="width:${w}%"></div>`, S='#efe9dd';
    return `<div class="blk" style="height:50px;background:${a}"></div>${L(70)}${L(48)}<div class="row">${[1,2,3].map(()=>`<div class="blk" style="flex:1;height:34px;background:${S}"></div>`).join('')}</div>`;
  };
  const projCard=(p,i)=>`
  <div class="swiper-slide"><article class="card proj" data-f="${esc(p.f)}">
    <div class="proj-img">
      <div class="mock" style="background:linear-gradient(145deg,${esc(p.g[0])},${esc(p.g[1])})">
        <span class="num">${String(i+1).padStart(2,'0')}</span>
        <div class="browser">${p.img
          ?`<div class="b-bar"><i></i><i></i><i></i><em class="url">${p.url?esc(p.url.replace(/^https?:\/\/|\/$/g,'')):''}</em></div><div class="b-body shot"><img src="${esc(p.img)}" alt="${esc(p.t)} screenshot" loading="lazy"></div>`
          :`<div class="b-bar"><i></i><i></i><i></i><em></em></div><div class="b-body">${mockBody(esc(p.g[0]))}</div>`}</div>
      </div>
      <${p.url?`a href="${esc(p.url)}" target="_blank" rel="noopener" aria-label="Visit ${esc(p.t)}"`:'span'} class="open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg></${p.url?'a':'span'}>
    </div>
    <div class="proj-body">
      <div class="proj-meta"><span class="proj-cat">${esc(p.c)}</span><span class="proj-flag">${esc(p.flag)}</span></div>
      <h3>${esc(p.t)}</h3>
      <p>${esc(p.d)}</p>
      <div class="tags">${p.tags.map(t=>`<span class="tag">${esc(t)}</span>`).join('')}</div>
    </div>
  </article></div>`;
  const projGrid=$id('projGrid');
  function renderProjects(f){
    projGrid.innerHTML=projects.map((p,i)=>[p,i]).filter(([p])=>f==='all'||p.f===f).map(([p,i])=>projCard(p,i)).join('');
  }
  renderProjects('all');
  const projSwiper=new Swiper('#projSwiper',{
    slidesPerView:1.08,spaceBetween:16,grabCursor:true,speed:700,watchOverflow:true,
    keyboard:{enabled:true,onlyInViewport:true},
    autoplay:{delay:4500,disableOnInteraction:false,pauseOnMouseEnter:true},
    navigation:{prevEl:'.proj-prev',nextEl:'.proj-next'},
    pagination:{el:'.proj-pag',clickable:true},
    breakpoints:{640:{slidesPerView:2,spaceBetween:20},1100:{slidesPerView:3,spaceBetween:22}}
  });
  document.querySelectorAll('.filter').forEach(b=>b.addEventListener('click',()=>{
    document.querySelectorAll('.filter').forEach(x=>x.classList.remove('active'));
    b.classList.add('active');
    renderProjects(b.dataset.f);
    projSwiper.update(); projSwiper.slideTo(0,0); projSwiper.autoplay.start();
  }));
}

// ---------- Mobile menu ----------
const navLinks=$id('navLinks');
$id('menuBtn').addEventListener('click',()=>navLinks.classList.toggle('open'));
navLinks.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>navLinks.classList.remove('open')));

// ---------- Scroll: progress, to-top ----------
const prog=$id('progress'), toTop=$id('toTop');
function onScroll(){
  const y=scrollY, h=document.documentElement.scrollHeight-innerHeight;
  prog.style.width=(h>0?y/h*100:0)+'%';
  toTop.classList.toggle('show',y>700);
}
addEventListener('scroll',onScroll,{passive:true}); onScroll();
toTop.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));

// ---------- Reveal on scroll (staggered) ----------
const io=new IntersectionObserver(es=>es.forEach(e=>{
  if(!e.isIntersecting) return;
  const sibs=[...e.target.parentElement.children].filter(c=>c.classList.contains('reveal'));
  e.target.style.transitionDelay=Math.min(Math.max(sibs.indexOf(e.target),0),6)*90+'ms';
  e.target.classList.add('in'); io.unobserve(e.target);
  setTimeout(()=>{e.target.style.transitionDelay=''},1700);
}),{threshold:.1,rootMargin:'0px 0px -40px 0px'});
document.querySelectorAll('.reveal').forEach(el=>io.observe(el));

// ---------- Typing ----------
if($id('typed') && SITE.words.length){
  const words=SITE.words, typed=$id('typed'); let wi=0,ci=0,del=false;
  (function type(){
    const w=words[wi]; typed.textContent=w.slice(0,ci);
    if(!del&&ci<w.length){ci++;setTimeout(type,70)}
    else if(!del){del=true;setTimeout(type,1700)}
    else if(ci>0){ci--;setTimeout(type,30)}
    else{del=false;wi=(wi+1)%words.length;setTimeout(type,300)}
  })();
}

// ---------- Counters ----------
const cio=new IntersectionObserver(es=>es.forEach(e=>{
  if(!e.isIntersecting) return;
  const el=e.target,to=+el.dataset.to,start=performance.now(),dur=1400;
  (function tick(now){const k=Math.min(1,(now-start)/dur);el.textContent=Math.round(to*(1-Math.pow(1-k,3)));if(k<1)requestAnimationFrame(tick)})(start);
  cio.unobserve(el);
}));
document.querySelectorAll('.count').forEach(c=>cio.observe(c));

// ---------- Spotlight cards ----------
document.addEventListener('pointermove',e=>{
  const c=e.target.closest && e.target.closest('.card'); if(!c) return;
  const r=c.getBoundingClientRect();
  c.style.setProperty('--mx',(e.clientX-r.left)+'px'); c.style.setProperty('--my',(e.clientY-r.top)+'px');
});

// ---------- Custom cursor + magnetic buttons (desktop only) ----------
if(matchMedia('(hover:hover) and (pointer:fine)').matches && !matchMedia('(prefers-reduced-motion:reduce)').matches){
  const dot=$id('curDot'), ring=$id('curRing');
  let mx=-100,my=-100,rx=mx,ry=my;
  addEventListener('pointermove',e=>{mx=e.clientX;my=e.clientY;dot.style.transform=`translate(${mx}px,${my}px) translate(-50%,-50%)`});
  (function loop(){rx+=(mx-rx)*.16;ry+=(my-ry)*.16;ring.style.transform=`translate(${rx}px,${ry}px) translate(-50%,-50%)`;requestAnimationFrame(loop)})();
  document.addEventListener('pointerover',e=>ring.classList.toggle('hover',!!e.target.closest('a,button,.card')));
  document.querySelectorAll('.magnetic').forEach(b=>{
    b.addEventListener('pointermove',e=>{const r=b.getBoundingClientRect();b.style.transform=`translate(${(e.clientX-r.left-r.width/2)*.25}px,${(e.clientY-r.top-r.height/2)*.35}px)`});
    b.addEventListener('pointerleave',()=>b.style.transform='');
  });
}

// ---------- Contact form: show progress while the message is being sent ----------
if($id('contactForm')){
  $id('contactForm').addEventListener('submit',()=>{
    const btn=$id('contactBtn');
    btn.style.pointerEvents='none'; btn.style.opacity='.8';
    btn.querySelector('span').textContent='Sending…';
  });
}
if($id('year')) $id('year').textContent=new Date().getFullYear();
</script>
@endverbatim
