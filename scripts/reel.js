import { chromium } from 'playwright';
import { mkdir, readFile, writeFile, readdir } from 'node:fs/promises';
import { spawn } from 'node:child_process';
import path from 'node:path';

const root = process.cwd();
const base = process.env.REEL_URL || 'http://127.0.0.1:8002';
const output = path.join(root, 'out');
const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));
const run = (command, args) => new Promise((resolve, reject) => {
    const child = spawn(command, args, { cwd: root, stdio: 'inherit', windowsHide: true });
    child.on('error', reject);
    child.on('exit', code => code === 0 ? resolve() : reject(new Error(`${command} exited ${code}`)));
});
const secretPattern = /sm_live_[a-z\d]{12,}|AIza[\w-]{30,}|sk-(?:proj-)?[\w-]{30,}/i;
let browser;
let server;

async function audit() {
    const files = [];
    async function walk(folder) {
        for (const entry of await readdir(folder, { withFileTypes: true })) {
            const full = path.join(folder, entry.name);
            if (entry.isDirectory()) await walk(full);
            else if (/\.(php|js|css|json)$/.test(entry.name)) {
                const content = await readFile(full, 'utf8');
                files.push({ file: path.relative(root, full), lines: content.split('\n').length });
            }
        }
    }
    for (const folder of ['app', 'bootstrap', 'config', 'database', 'routes', 'resources', 'tests']) {
        if (folder === 'bootstrap') {
            for (const file of ['app.php', 'providers.php']) {
                const full = path.join(root, folder, file);
                const content = await readFile(full, 'utf8');
                files.push({ file: path.relative(root, full), lines: content.split('\n').length });
            }
        } else await walk(path.join(root, folder));
    }
    await writeFile(path.join(output, 'audit.json'), JSON.stringify({ files,
        existing: ['Blade responsive portfolio', 'Hero typing', 'Swiper projects', 'Scroll reveals', 'Database-grounded chatbot', 'Email OTP admin'],
        upgraded: ['Animated name intro', 'Hero stagger', 'Directional reveals', 'Project tilt', 'Chat typing dots', 'Mobile overflow and chat height'],
        remaining: ['Review real project images and copy before publishing', 'Set Instagram bio link manually'],
    }, null, 2));
    console.log(`Audit: read ${files.length} application files. No environment files included.`);
}

async function ensureSite() {
    try { if ((await fetch(base)).ok) return; } catch {}
    const url = new URL(base);
    if (!['localhost', '127.0.0.1'].includes(url.hostname)) throw new Error('Public site is unavailable.');
    server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', `--port=${url.port || 8000}`, '--no-reload', '--no-interaction'], { cwd: root, env: { ...process.env, APP_URL: base, CHAT_ALLOWED_ORIGIN: base }, stdio: 'ignore', windowsHide: true });
    for (let index = 0; index < 40; index++) {
        await sleep(500);
        try { if ((await fetch(base)).ok) return; } catch {}
    }
    throw new Error('Could not start the portfolio server.');
}

async function capture() {
    await ensureSite();
    browser = await chromium.launch({ headless: true, args: ['--disable-gpu', '--renderer-process-limit=1', '--disable-extensions', '--disable-background-networking'] });
    const options = { viewport: { width: 360, height: 640 }, deviceScaleFactor: 1, isMobile: true, hasTouch: false };
    const rehearsal = await browser.newContext(options);
    const page = await rehearsal.newPage();
    console.log('Discover/rehearse: public sections, mobile bounds and chatbot selectors.');
    await page.goto(base, { waitUntil: 'networkidle' });
    await page.locator('body.ready').waitFor();
    const selectors = ['#home', '#about', '#skills', '#projects', '.proj', '#contact', '#chat-launch', '#portfolio-chat', '#chat-input', '#chat-form'];
    for (const selector of selectors) {
        if (!await page.locator(selector).count()) throw new Error(`Missing required selector: ${selector}`);
    }
    const sections = await page.locator('section[id]').evaluateAll(items => items.map(item => item.id));
    for (const width of [320, 360, 390]) {
        await page.setViewportSize({ width, height: 640 });
        const bounds = await page.evaluate(() => ({ viewport: innerWidth, width: document.documentElement.scrollWidth }));
        if (bounds.width > width + 1) throw new Error(`Horizontal overflow at ${width}px: ${bounds.width}`);
    }
    await page.setViewportSize(options.viewport);
    for (const id of ['about', 'skills', 'projects', 'contact']) {
        await page.locator(`#${id}`).scrollIntoViewIfNeeded();
        await sleep(300);
    }
    await writeFile(path.join(output, 'discovery.json'), JSON.stringify({ selectors, sections, mobileWidths: [320, 360, 390] }, null, 2));
    await rehearsal.close();

    console.log('Record: real anonymous visitor, real chatbot request; no admin pages.');
    const context = await browser.newContext({ ...options, viewport: { width: 1080, height: 1920 }, recordVideo: { dir: path.join(output, 'recordings'), size: { width: 1080, height: 1920 }, fps: 30 } });
    await context.addInitScript(() => {
        const observer = new MutationObserver(() => {
            const meta = document.querySelector('meta[name="viewport"]');
            if (meta) {
                meta.content = 'width=360, initial-scale=3, maximum-scale=3';
                observer.disconnect();
            }
        });
        observer.observe(document, { childList: true, subtree: true });
    });
    const recorded = await context.newPage();
    const epoch = await recorded.evaluate(() => performance.timeOrigin);
    const errors = [];
    recorded.on('pageerror', error => errors.push(error.message));
    recorded.on('request', request => { if (request.url().includes('/api/chat')) console.log('Browser: chat POST started.'); });
    recorded.on('requestfailed', request => { if (request.url().includes('/api/chat')) console.log(`Browser: chat request failed (${request.failure()?.errorText}).`); });
    recorded.on('request', request => {
        const url = new URL(request.url());
        if (/scalemax|generativelanguage|api\.openai/.test(url.hostname)) errors.push('Provider request reached browser.');
        if (url.origin === new URL(base).origin && /^\/(admin|login)/.test(url.pathname)) errors.push('Unexpected private navigation.');
    });
    await recorded.goto(base, { waitUntil: 'domcontentloaded' });
    const navigation = await recorded.evaluate(() => performance.timeOrigin);
    const mark = () => (Date.now() - epoch) / 1000;
    const start = (navigation - epoch) / 1000;
    const scroll = async id => recorded.locator(`#${id}`).evaluate(element => new Promise(resolve => {
        const from = scrollY, to = element.getBoundingClientRect().top + scrollY - 84, start = performance.now();
        const step = now => {
            const p = Math.min(1, (now - start) / 700), eased = 1 - Math.pow(1 - p, 3);
            window.scrollTo({ top: from + (to - from) * eased, behavior: 'instant' });
            if (p < 1) requestAnimationFrame(step); else resolve();
        };
        requestAnimationFrame(step);
    }));
    const voice = JSON.parse(await readFile('assets/voice.words.json', 'utf8'));
    const anchor = word => voice.words.find(item => item.text.replace(/[।,?]/g, '') === word)?.start;
    const openTime = anchor('लेकिन') || 11.93;
    const askTime = anchor('चलो') || 17.3742;
    const replyTime = anchor('देखो') || 21.2341;
    const ctaTime = anchor('अगर') || 23.9044;
    await recorded.addStyleTag({ content: '.cur-dot,.cur-ring{display:none!important}' });
    await recorded.locator('body.ready').waitFor();
    await sleep(1400);
    const showcase = mark();
    await scroll('about');
    await sleep(1500); await scroll('skills');
    await sleep(1500); await scroll('projects');
    await sleep(900);
    const card = recorded.locator('.swiper-slide-active .proj').first();
    await card.scrollIntoViewIfNeeded();
    await card.hover();
    await sleep(900);
    await sleep(1000);
    const opened = mark();
    await recorded.locator('#chat-launch').click();
    await recorded.locator('#portfolio-chat').waitFor({ state: 'visible' });
    await sleep((askTime - openTime) * 1000);
    await recorded.locator('#chat-input').click();
    const typed = mark();
    await recorded.locator('#chat-input').pressSequentially('Best project kaunsa hai?', { delay: 50 });
    const responsePromise = recorded.waitForResponse(response => response.url().includes('/api/chat') && response.request().method() === 'POST', { timeout: 120000 });
    await recorded.locator('#chat-input').press('Enter');
    const sent = mark();
    const response = await responsePromise.catch(async error => {
        await recorded.screenshot({ path: path.join(output, 'capture-error.png') });
        const text = await recorded.locator('#portfolio-chat').innerText();
        console.log(`Chat state: ${text.slice(-700)}`);
        throw error;
    });
    if (!response.ok()) {
        const failure = await response.json().catch(() => ({}));
        throw new Error(`Live chat returned HTTP ${response.status()}: ${failure.message || 'request failed'}`);
    }
    await recorded.waitForFunction(() => !document.querySelector('.chat-typing'), { timeout: 10000 });
    const replied = mark();
    const answer = await recorded.locator('.chat-bubble.bot').last().innerText();
    console.log(`Live reply received in ${(replied - sent).toFixed(1)}s; checking full answer.`);
    if (/temporarily unavailable|connection needs attention|Sorry, I don't have/i.test(answer)) throw new Error(`Live chatbot did not provide a grounded project answer: ${answer}`);
    const publicText = await recorded.locator('body').innerText();
    if (secretPattern.test(publicText) || secretPattern.test(await recorded.content())) throw new Error('Secret-like token detected in public page.');
    if (errors.length) throw new Error(`Browser errors: ${errors.join('; ')}`);
    await recorded.locator('.chat-bubble.bot').last().scrollIntoViewIfNeeded();
    await sleep(3000);
    await recorded.screenshot({ path: path.join(output, 'chat-rehearsal.png') });
    const contact = mark();
    await recorded.locator('#chat-close').click();
    await scroll('contact');
    await sleep((29.5 - ctaTime) * 1000);
    const end = mark();
    const typingDuration = sent - typed;
    const segments = [
        { name: 'intro / hero', start, end: showcase, duration: 4 },
        { name: 'sections / projects', start: showcase, end: opened, duration: openTime - 4 },
        { name: 'chat opens', start: opened, end: typed, duration: askTime - openTime },
        { name: 'question typed at 50ms/character', start: typed, end: sent, duration: typingDuration },
        { name: 'live response wait (edited for reel)', start: sent, end: replied, duration: replyTime - askTime - typingDuration },
        { name: 'complete real answer', start: replied, end: contact, duration: ctaTime - replyTime },
        { name: 'contact / CTA', start: contact, end, duration: 29.5 - ctaTime },
    ];
    if (segments.some(segment => segment.duration <= 0)) throw new Error('Invalid voice/capture timing.');
    const video = recorded.video();
    console.log('Saving the native portrait recording (encoder flush may take a few minutes).');
    await context.close();
    await video.saveAs(path.join(output, 'capture.webm'));
    await writeFile(path.join(output, 'capture.json'), JSON.stringify({ segments, answer, question: 'Best project kaunsa hai?', realResponseSeconds: replied - sent, waitEdited: true, viewport: options.viewport, browserCanvas: { width: 1080, height: 1920 }, mobilePageScale: 3, recordedResolution: '1080x1920', browserErrors: errors, security: 'Anonymous public pages only; no browser provider request; no secret-like tokens in DOM/source.' }, null, 2));
    await browser.close(); browser = null;
    console.log(`Real answer captured (${(replied - sent).toFixed(1)}s response; wait edited to fit narration).`);
}

async function openingShots() {
    console.log('Opening: capture the rendered name and hero after fonts load; omit blank network wait.');
    await ensureSite();
    browser = await chromium.launch({ headless: true, args: ['--disable-gpu'] });
    const context = await browser.newContext({ viewport: { width: 1080, height: 1920 }, isMobile: true, deviceScaleFactor: 1 });
    await context.addInitScript(() => {
        const observer = new MutationObserver(() => {
            const meta = document.querySelector('meta[name="viewport"]');
            if (meta) { meta.content = 'width=360, initial-scale=3, maximum-scale=3'; observer.disconnect(); }
        });
        observer.observe(document, { childList: true, subtree: true });
    });
    const page = await context.newPage();
    await page.goto(base, { waitUntil: 'networkidle', timeout: 90000 });
    await page.locator('body.ready').waitFor();
    await page.evaluate(() => document.fonts.ready);
    await page.addStyleTag({ content: '.cur-dot,.cur-ring{display:none!important}' });
    await sleep(1000);
    await page.screenshot({ path: path.join(output, 'opening-hero.png') });
    await page.evaluate(() => {
        const loader = document.querySelector('#loader');
        loader.hidden = false; loader.classList.remove('done');
        Object.assign(loader.style, { transition: 'none', transform: 'none', opacity: '1' });
        loader.querySelectorAll('.l-name span,.l-tagline').forEach(item => Object.assign(item.style, { animation: 'none', transform: 'none', opacity: '1' }));
        loader.querySelectorAll('.l-bar,.l-count').forEach(item => item.style.visibility = 'hidden');
    });
    await page.screenshot({ path: path.join(output, 'opening-intro.png') });
    await context.close(); await browser.close(); browser = null;
    const timeline = JSON.parse(await readFile(path.join(output, 'capture.json'), 'utf8'));
    timeline.segments = [
        { name: 'animated name reveal', image: 'out/opening-intro.png', duration: 2.2, opening: true },
        { name: 'hero fade / push in', image: 'out/opening-hero.png', duration: 1.8, opening: true },
        ...timeline.segments.filter(segment => !segment.opening && segment.name !== 'intro / hero'),
    ];
    timeline.openingNote = 'Real rendered public UI; subtle video fade/zoom. Initial blank network wait excluded.';
    await writeFile(path.join(output, 'capture.json'), JSON.stringify(timeline, null, 2));
}

try {
    await mkdir(output, { recursive: true });
    if (!process.argv.includes('--render-only') && !process.argv.includes('--opening-only')) {
        await audit();
        await run('python', ['scripts/reel_media.py', 'voice']);
        for (let attempt = 1; attempt <= 3; attempt++) {
            try { await capture(); break; }
            catch (error) {
                if (browser) { await browser.close(); browser = null; }
                if (attempt === 3 || !/Live chat returned HTTP 503|waitForResponse/.test(error.message)) throw error;
                console.log(`Provider was unavailable; retrying real recording (${attempt + 1}/3).`);
                await sleep(3000);
            }
        }
    }
    if (!process.argv.includes('--render-only')) await openingShots();
    console.log('Render: word highlights, Hindi font, voice mix and H.264 export.');
    await run('python', ['scripts/reel_media.py', 'render']);
    console.log('Ready: out/reel.mp4 (format verification in out/report.json).');
} catch (error) {
    console.error(`Reel failed: ${error.message}`);
    process.exitCode = 1;
} finally {
    if (browser) await browser.close();
    if (server && process.platform === 'win32') {
        await new Promise(resolve => {
            const cleanup = spawn('taskkill', ['/PID', String(server.pid), '/T', '/F'], { stdio: 'ignore', windowsHide: true });
            cleanup.on('error', resolve); cleanup.on('exit', resolve);
        });
    } else if (server) server.kill();
}
