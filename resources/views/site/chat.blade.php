<style>
.chat-launch{position:fixed;right:22px;bottom:22px;z-index:110;display:flex;align-items:center;gap:10px;padding:16px 22px;border:0;border-radius:99px;background:var(--accent);color:var(--accent-ink);font-weight:700;box-shadow:var(--shadow);cursor:pointer;transition:transform .3s var(--ease)}
.chat-launch:hover{transform:translateY(-3px)}
.chat-launch[aria-expanded="true"]{opacity:0;pointer-events:none}
#toTop{bottom:96px!important}
.chat-panel{position:fixed;right:22px;bottom:94px;z-index:111;width:min(420px,calc(100vw - 32px));height:min(600px,calc(100dvh - 120px));display:flex;flex-direction:column;background:var(--surface);color:var(--text);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;opacity:0;transform:translateY(16px) scale(.97);visibility:hidden;transition:opacity .25s var(--ease),transform .25s var(--ease),visibility .25s}
.chat-panel.open{opacity:1;transform:none;visibility:visible}
.chat-header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:20px;border-bottom:1px solid var(--border);background:var(--surface-2);flex-shrink:0}
.chat-header strong{display:block;font-size:1rem}.chat-header small{color:var(--muted);font-size:.73rem}.chat-mark{color:var(--accent)}
.chat-messages{flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;padding:18px;display:block;scroll-behavior:smooth;scrollbar-width:thin}
.chat-bubble{width:fit-content;margin:0 0 14px;padding:12px 15px;border-radius:16px;max-width:90%;font-size:.85rem;line-height:1.65;white-space:pre-wrap;overflow-wrap:anywhere;flex-shrink:0;background:var(--surface-2);align-self:flex-start}
.chat-bubble.user{margin-left:auto;align-self:flex-end;background:var(--accent);color:var(--accent-ink);border-bottom-right-radius:4px}.chat-bubble.bot{border-bottom-left-radius:4px}
.chat-tools{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding:8px 18px;border-bottom:1px solid var(--border);flex-shrink:0}
.chat-clear{border:0;background:transparent;color:var(--muted);font:inherit;font-size:.68rem;cursor:pointer;padding:5px 0}
.chat-clear:hover{color:var(--accent)}
.chat-clear:disabled{opacity:.45;cursor:wait}
.chat-suggestions-toggle{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border:1px solid var(--border);border-radius:99px;background:transparent;color:var(--muted);font:inherit;font-size:.68rem;cursor:pointer}
.chat-suggestions-toggle:hover,.chat-suggestions-toggle[aria-expanded="true"]{color:var(--accent);border-color:var(--accent)}
.chat-suggestions-toggle:disabled{opacity:.45;cursor:default}
.chat-hint[hidden]{display:none}
.chat-hint{width:100%;padding-top:0}
.chat-suggestion{display:block;width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:10px;background:var(--surface-2);color:var(--text);font:inherit;font-size:.75rem;text-align:left;cursor:pointer;overflow-wrap:anywhere}
.chat-suggestion:hover{border-color:var(--accent);color:var(--accent)}
.chat-form{padding:14px!important;border:0!important;border-top:1px solid var(--border)!important;border-radius:0!important;background:var(--surface)!important;display:flex;gap:8px;box-shadow:none!important}
.chat-form input{min-width:0;flex:1;background:var(--surface-2);color:var(--text);border:1px solid var(--border);border-radius:99px;padding:12px 16px;font:inherit;font-size:.85rem}.chat-form button{flex-shrink:0}.chat-form button:disabled{opacity:.5;cursor:wait}
.chat-panel :focus-visible,.chat-launch:focus-visible{outline:2px solid var(--accent);outline-offset:3px}.chat-typing{color:var(--muted)}
.chat-dots{display:inline-flex;align-items:center;gap:5px;height:16px}
.chat-dots i{width:6px;height:6px;border-radius:50%;background:currentColor;animation:chat-dot 1s ease-in-out infinite}
.chat-dots i:nth-child(2){animation-delay:.15s}.chat-dots i:nth-child(3){animation-delay:.3s}
@keyframes chat-dot{0%,70%,100%{transform:translateY(0);opacity:.45}35%{transform:translateY(-4px);opacity:1}}
@keyframes chat-pulse{to{opacity:.4}}
@media(max-width:480px){.chat-panel{right:16px;bottom:90px}.chat-launch{right:16px;bottom:max(16px,env(safe-area-inset-bottom));padding:14px 18px}}
@media(max-width:480px){.chat-panel{right:12px;bottom:max(16px,env(safe-area-inset-bottom));width:calc(100vw - 24px);height:min(680px,calc(100dvh - 32px))}.chat-header{padding:15px}.chat-header strong{font-size:.92rem}.chat-messages{padding:14px}.chat-form input{font-size:16px}.chat-bubble{max-width:95%}}
@media(max-height:500px){.chat-panel{bottom:10px;height:calc(100dvh - 20px)}}
@media(prefers-reduced-motion:reduce){.chat-panel,.chat-launch{transition:none}.chat-messages{scroll-behavior:auto}.chat-typing,.chat-dots i{animation:none}}
</style>
<button type="button" class="chat-launch" id="chat-launch" aria-expanded="false" aria-controls="portfolio-chat">✦ <span>Chat with me</span></button>
<div class="chat-panel" id="portfolio-chat" role="dialog" aria-modal="false" aria-labelledby="chat-title" inert>
    <div class="chat-header"><div><strong id="chat-title"><span class="chat-mark">✦</span> Portfolio assistant</strong><small>Ask about Anmol and his work</small></div><button type="button" class="icon-btn" id="chat-close" aria-label="Close chat">✕</button></div>
    <div class="chat-tools">
        <button type="button" class="chat-suggestions-toggle" id="chat-suggestions-toggle" aria-expanded="false" aria-controls="chat-hint">Suggestions <span aria-hidden="true">⌄</span></button>
        <button type="button" class="chat-clear" id="chat-clear" data-url="{{ route('chat.clear', [], false) }}">Clear chat</button>
        <div class="chat-hint" id="chat-hint" hidden></div>
    </div>
    <div class="chat-messages" id="chat-messages" role="log" aria-live="polite" aria-relevant="additions" aria-label="Chat messages"></div>
    <form class="chat-form" id="chat-form" action="{{ route('chat.store', [], false) }}" method="post">
        @csrf
        <input id="chat-input" name="message" aria-label="Your question" placeholder="Ask me something…" maxlength="1000" autocomplete="off" required>
        <button class="icon-btn" type="submit" aria-label="Send message">➜</button>
    </form>
</div>
<script>
(() => {
    const launch = document.getElementById('chat-launch'), panel = document.getElementById('portfolio-chat');
    const messages = document.getElementById('chat-messages'), form = document.getElementById('chat-form'), input = document.getElementById('chat-input');
    const sendButton = form.querySelector('button');
    const clearButton = document.getElementById('chat-clear');
    const hintsToggle = document.getElementById('chat-suggestions-toggle'), hint = document.getElementById('chat-hint');
    let opened = false, busy = false;
    function fitKeyboard() {
        const viewport = window.visualViewport;
        if (viewport && window.innerWidth <= 480 && panel.classList.contains('open') && window.innerHeight - viewport.height > 150) {
            panel.style.bottom = Math.max(12, window.innerHeight - viewport.height - viewport.offsetTop + 12) + 'px';
            panel.style.height = Math.min(600, Math.max(120, viewport.height - 24)) + 'px';
        } else { panel.style.removeProperty('bottom'); panel.style.removeProperty('height'); }
    }
    window.visualViewport?.addEventListener('resize', fitKeyboard);
    window.visualViewport?.addEventListener('scroll', fitKeyboard);
    const scroll = () => { messages.scrollTop = messages.scrollHeight; };
    function bubble(text, role) {
        const node = document.createElement('div'); node.className = 'chat-bubble ' + role; node.textContent = text; messages.append(node); scroll(); return node;
    }
    function typingBubble() {
        const node = bubble('', 'bot chat-typing'); node.setAttribute('aria-label', 'Assistant is typing');
        const dots = document.createElement('span'); dots.className = 'chat-dots'; dots.setAttribute('aria-hidden', 'true');
        for (let i = 0; i < 3; i++) dots.append(document.createElement('i'));
        node.append(dots); return node;
    }
    function hideHint() {
        hint.hidden = true; hintsToggle.setAttribute('aria-expanded', 'false');
    }
    function suggestion(question) {
        hint.replaceChildren(); hintsToggle.disabled = !question;
        if (!question) { hideHint(); return; }
        const button = document.createElement('button');
        button.type = 'button'; button.className = 'chat-suggestion'; button.textContent = question;
        button.addEventListener('click', () => { if (!busy) { hideHint(); submit(question); } });
        hint.append(button);
    }

    function toggle(open) {
        panel.classList.toggle('open', open); panel.inert = !open; launch.setAttribute('aria-expanded', String(open));
        fitKeyboard();
        if (open) {
            if (!opened) {
                opened = true;
                const history = @json(session('chat.history', []));
                if (history.length) history.forEach(turn => bubble(turn.parts[0].text, turn.role === 'user' ? 'user' : 'bot'));
                else bubble('Hi! I am Anmol\'s portfolio assistant. What would you like to know?', 'bot');
                suggestion(@json(session('chat.suggestion')) || 'Tell me about yourself.');
            }
            input.focus();
        } else { launch.focus(); }
    }
    async function submit(question) {
        if (busy || !question.trim()) return;
        hideHint(); busy = true; sendButton.disabled = true; clearButton.disabled = true; input.value = '';
        bubble(question, 'user'); const typing = typingBubble(); messages.setAttribute('aria-busy', 'true');
        const controller = new AbortController(), timer = setTimeout(() => controller.abort(), 90000);
        try {
            const response = await fetch(form.action, {method:'POST', credentials:'same-origin', signal:controller.signal, headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':form.querySelector('[name="_token"]').value}, body:JSON.stringify({message:question})});
            const data = await response.json();
            if (!response.ok) throw new Error(response.status === 429 ? 'Too many questions. Please wait a minute.' : response.status === 419 ? 'Your session expired. Please refresh this page.' : (data.message || 'Could not get a reply. Please try again.'));
            typing.remove(); bubble(data.answer, 'bot'); suggestion(data.suggestions?.[0]);
        } catch (error) { typing.remove(); bubble(error.name === 'AbortError' ? 'That took too long. Please try again.' : (error instanceof TypeError ? 'Could not connect. Please try again.' : error.message), 'bot'); }
        finally { clearTimeout(timer); busy = false; sendButton.disabled = false; clearButton.disabled = false; messages.setAttribute('aria-busy', 'false'); if (!panel.inert) input.focus(); scroll(); }
    }
    clearButton.addEventListener('click', async () => {
        if (busy) return;
        busy = true; clearButton.disabled = true; sendButton.disabled = true;
        const controller = new AbortController(), timer = setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(clearButton.dataset.url, {method:'DELETE', credentials:'same-origin', signal:controller.signal, headers:{'Accept':'application/json','X-CSRF-TOKEN':form.querySelector('[name="_token"]').value}});
            if (!response.ok) throw new Error('Could not clear chat. Please try again.');
            messages.replaceChildren(); input.value = ''; hideHint(); suggestion('Tell me about yourself.');
            bubble('Chat cleared. How can I help you?', 'bot');
        } catch (error) { bubble('Could not clear chat. Please try again.', 'bot'); }
        finally { clearTimeout(timer); busy = false; clearButton.disabled = false; sendButton.disabled = false; if (!panel.inert) input.focus(); }
    });
    hintsToggle.addEventListener('click', () => { hint.hidden = !hint.hidden; hintsToggle.setAttribute('aria-expanded', String(!hint.hidden)); });
    launch.addEventListener('click', () => toggle(!panel.classList.contains('open')));
    document.getElementById('chat-close').addEventListener('click', () => toggle(false));
    panel.addEventListener('keydown', event => { if(event.key === 'Escape') toggle(false); });
    form.addEventListener('submit', event => { event.preventDefault(); submit(input.value.trim()); });
})();
</script>
