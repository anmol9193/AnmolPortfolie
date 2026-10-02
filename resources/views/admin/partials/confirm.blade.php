{{--
  Confirmation modal. Any form with data-confirm="Title" asks here before it submits.
  Optional: data-confirm-text="..." (details) and data-confirm-button="Delete" (button label).
--}}
@verbatim
<style>
.modal{position:fixed;inset:0;z-index:400;display:grid;place-items:center;padding:16px;background:rgba(8,8,8,.55);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);animation:modal-fade .25s ease both}
.modal[hidden]{display:none}
.modal-box{width:min(420px,100%);padding:30px 28px 24px;border-radius:24px;background:var(--surface);border:1px solid var(--border);text-align:center;animation:modal-pop .35s var(--ease) both}
.modal-ic{width:64px;height:64px;margin:0 auto 18px;border-radius:20px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center}
.modal-ic svg{width:28px;height:28px}
.modal-box h3{font-size:1.3rem;font-weight:700;letter-spacing:-.02em;line-height:1.25}
.modal-box p{color:var(--muted);font-size:.92rem;margin-top:8px;overflow-wrap:anywhere}
.modal-acts{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:24px}
.modal-acts button{padding:13px 18px;border-radius:99px;font-family:inherit;font-weight:700;font-size:.92rem;cursor:pointer;transition:transform .3s var(--ease),border-color .25s,box-shadow .3s}
.modal-acts .m-no{border:1.5px solid var(--border);background:transparent;color:var(--text)}
.modal-acts .m-no:hover{border-color:var(--text)}
.modal-acts .m-yes{border:none;background:var(--accent);color:var(--accent-ink)}
.modal-acts .m-yes:hover{transform:translateY(-2px);box-shadow:0 14px 30px -12px var(--accent)}
.modal-acts button:focus-visible{outline:2px solid var(--text);outline-offset:3px}
@keyframes modal-fade{from{opacity:0}}
@keyframes modal-pop{from{opacity:0;transform:translateY(18px) scale(.95)}}
</style>
<div class="modal" id="confirmModal" hidden role="dialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmText">
  <div class="modal-box">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6M10 11v6M14 11v6"/></svg></div>
    <h3 id="confirmTitle">Are you sure?</h3>
    <p id="confirmText"></p>
    <div class="modal-acts">
      <button class="m-no" type="button" id="confirmNo">Cancel</button>
      <button class="m-yes" type="button" id="confirmYes">Confirm</button>
    </div>
  </div>
</div>
<script>
// ---------- Confirm modal for forms with data-confirm ----------
(function(){
  const modal=document.getElementById('confirmModal'), yes=document.getElementById('confirmYes'), no=document.getElementById('confirmNo');
  let pending=null, opener=null;
  function close(){modal.hidden=true;pending=null;if(opener){opener.focus();opener=null}}
  document.addEventListener('submit',e=>{
    const form=e.target;
    if(!form.dataset.confirm||form.dataset.confirmed) return;
    e.preventDefault();
    pending=form; opener=document.activeElement;
    document.getElementById('confirmTitle').textContent=form.dataset.confirm;
    document.getElementById('confirmText').textContent=form.dataset.confirmText||'';
    yes.textContent=form.dataset.confirmButton||'Confirm';
    modal.hidden=false; no.focus();
  });
  yes.addEventListener('click',()=>{if(!pending) return;const form=pending;form.dataset.confirmed='1';modal.hidden=true;form.submit()});
  no.addEventListener('click',close);
  modal.addEventListener('click',e=>{if(e.target===modal) close()});
  addEventListener('keydown',e=>{
    if(modal.hidden) return;
    if(e.key==='Escape') close();
    // keep Tab inside the two buttons
    if(e.key==='Tab'){e.preventDefault();(document.activeElement===no?yes:no).focus()}
  });
})();
</script>
@endverbatim
