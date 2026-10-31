# Portfolio chatbot

Laravel 13 / PHP 8.3, Blade and ScaleMax's OpenAI-compatible Chat Completions API. No new dependency is required. The widget matches the portfolio theme and supports mobile, keyboard input, reduced motion, loading and auto-scroll.

## Local setup

1. Run `composer install`. On a fresh checkout copy `.env.example` to `.env`; preserve an existing `.env`.
2. Run `php artisan key:generate --no-interaction`, configure the database, then `php artisan migrate --no-interaction`.
3. Set these backend environment variables:

```dotenv
AI_API_KEY=your_key_here
AI_BASE_URL=https://api.scalemax.pro/token/v1
AI_MODEL=gemini-3.8-flash
APP_URL=http://localhost:8000
CHAT_ALLOWED_ORIGIN=http://localhost:8000
```

Use the exact browser scheme, host and port for the allowed origin, without a trailing slash. Run `php artisan config:clear --no-interaction`, then `php artisan serve --no-interaction`. The widget is included in Blade and needs no Vite build. Other frontend asset changes use `npm install` and `npm run build`.

## Admin key setup

Sign in and open **AI Chatbot**. Paste your ScaleMax key, save, then use **Test connection**. You can replace or remove the key on this page. An admin key takes priority over the environment key immediately, without clearing configuration caches. It is encrypted in the existing settings table using Laravel's APP_KEY, and never repopulated into the form. Keep APP_KEY stable and backed up. Removing the admin key restores the environment fallback.

Connection testing checks the authenticated model catalog and confirms that AI_MODEL is available to the key. A successful catalog check does not guarantee sufficient balance for inference. Actual chat errors remain safely summarized.

## Provider configuration

This key uses ScaleMax, not Google's direct Gemini endpoint. ScaleMax requires `Authorization: Bearer` and the OpenAI-compatible `messages`/`choices` format even when the selected model is Gemini.

- Token-pack key: `AI_BASE_URL=https://api.scalemax.pro/token/v1`
- API-credit key: `AI_BASE_URL=https://api.scalemax.pro/v1`

Use only a model from the key's authenticated `/models` catalog. The configured token route and `gemini-3.8-flash` were confirmed accessible for the saved key. The client only permits the two official ScaleMax base URLs and disables redirects so credentials cannot be forwarded to another host. A `wrong_billing_surface` response means the key needs the other billing route.

Official API documentation: https://docs.scalemax.pro/docs/chat-completions
Setup and billing routes: https://docs.scalemax.pro/docs/quick-start

## Knowledge and conversation

The assistant handles greetings and short small talk naturally. It keeps the last four exchanges in the visitor's server session and uses them to understand follow-ups. Each answer shows one clickable follow-up question from the public knowledge base. Shown questions are not repeated within the session; no question is shown once the available pool is exhausted.

For factual answers, it retrieves up to 12 relevant public records using English/Hindi/Hinglish aliases and text matches. Profile/contact text uses the existing public `content.*` fields and the site's default content. Projects, skills, experience, education, services, process and countries use public text fields from `portfolie`. Update these in the existing admin. Login/account content, visitor messages, private settings and API credentials are excluded.

The model receives only relevant public context and limited conversation history, a strict context-only factual prompt, temperature 0.1 and no external tools. Past answers are explicitly not factual evidence. Unsupported queries are refused; factual responses must reference retrieved context IDs. These safeguards reduce hallucinations but do not prove every generated claim correct. Review live answers before deployment.

## Security and hosting

The API key stays on the PHP backend: `env('AI_API_KEY')` in configuration, not Node's `process.env` or any `VITE_*` variable. `.env*` is ignored except the placeholder example. Admin-entered keys are encrypted in the database. Neither keys nor provider headers are logged by the chatbot.

`POST /api/chat` retains CSRF/session protection, a 1,000-character input limit, 10 requests/minute/IP, restricted CORS and an exact origin check. Outbound connections have 12-second connection and 25-second total timeouts, HTTPS certificate verification and no redirects. Provider error bodies never reach visitors. Configure external APM/HTTP tracing to redact Authorization headers.

On your hosting platform, set AI_API_KEY if using an environment key, AI_BASE_URL, AI_MODEL, APP_URL and CHAT_ALLOWED_ORIGIN. Set APP_DEBUG=false, enable HTTPS, use persistent database/cache/session storage and the public document root. Configure trusted proxies for the host so rate limits see visitor IPs. Run `php artisan config:cache --no-interaction` after environment changes. Keys saved through admin need no cache clear.

If Windows/PHP reports cURL error 60, configure a trusted CA bundle without disabling certificate verification. The local file is `storage/app/private/gemini-ca.pem` (the filename is retained from the earlier integration), downloaded from https://curl.se/ca/cacert.pem. Set AI_CA_BUNDLE to another trusted PEM path if needed. Without this file, normal PHP certificate trust is used. The local bundle is ignored by Git; configure the hosting CA store or supply your own current bundle.

## Admin accounts and email OTP

Run `php artisan migrate --no-interaction` when deploying this update. The migration adds admin roles, active status and hashed OTP fields, and promotes the existing `anmolsaini9193@gmail.com` account to super admin. It does not change that account's password.

Admin login always requires two steps: **email + password**, then **email OTP verification**. A correct password sends a six-digit code but does not authenticate the user. The dashboard remains blocked until the code is verified. There is no email-only or password-only login. Codes expire after five minutes, are stored only as hashes and are consumed on successful verification. Five incorrect codes invalidate a challenge. Resending requires the same password-checked session and replaces the previous code; requests are limited to one per minute and five per hour per email, plus per-IP limits. Password proof expires after ten minutes and is invalidated if the password changes. OTP input is never flashed back into the session. There is no public registration or remember-me bypass.

Only the super admin sees **Admins** in the sidebar and can add, activate, deactivate or remove other admins. A password is required when creating an admin. Regular admins can access only **Dashboard, Themes, Appearance, Site pages and Messages**, plus logout. Chatbot settings, portfolio content/collections, account settings and admin management are hidden and their routes are restricted to the super admin. The normal dashboard also omits content editing shortcuts and system information. The super-admin account cannot be deactivated or removed through this page. Deactivated admins cannot sign in. Admin requests require a session with completed email verification, so old password-only sessions are signed out and must complete the new login flow.

OTP uses the existing `MAIL_*` SMTP configuration. Configure a real mail transport on hosting; the OTP flow refuses direct log/array transports outside tests. The current Gmail SMTP connection was successfully authenticated, but inbox delivery must be checked by signing in from the login screen. No test email was sent during implementation. Email delivery failure keeps the user logged out; there is no fallback that skips verification.

Run `php artisan test --compact tests/Feature/LoginOtpTest.php tests/Feature/AdminAccessTest.php` for OTP and admin permissions.

### Chat verification

Run `php artisan test --compact tests/Feature/ChatTest.php tests/Feature/ChatSettingsTest.php`. Tests fake the provider and cover conversational history, factual context/source checks, refusals, input/origin/rate limits, failure handling, encrypted admin keys, connection checks, unsupported endpoints and secret exclusion.

## Instagram reel

Install Node dependencies with `npm install`, the headless browser with `npx playwright install chromium --only-shell`, and media tools with `python -m pip install -r scripts/reel-requirements.txt`. Python 3.12+ is recommended. Run the Laravel site locally with its normal backend configuration and a working chatbot. The recorder visits only public pages in a fresh anonymous browser; it never reads environment files or opens admin screens.

Run `npm run build`, then `npm run reel`. The recorder uses a separate temporary Artisan server on port 8002 by default, keeping recording traffic separate from your normal development server. `REEL_URL` optionally selects another running site. Output is `out/reel.mp4`; `out/report.json`, `out/audit.json`, `out/discovery.json` and `out/capture.json` document the checks, timings and real public answer. Generated recordings and voice files are ignored by Git. Optional `assets/music.mp3` is mixed quietly; supply music you have permission to use.

The exact Hindi script uses Madhur at +10%. It lasts about 39 seconds naturally, so a pitch-preserving tempo adjustment fits it into 29.5 seconds; word timestamps are adjusted by the same factor. The real provider's waiting interval is edited to fit the narration. This is an edited showcase, not a response-speed benchmark. No answers are mocked. Review narration pace, Hindi captions, project answer and contact details before posting. Set your real Instagram bio link manually.

To change only caption styling or export settings without calling the chatbot again, run `npm run reel -- --render-only`. To regenerate narration, run `python scripts/reel_media.py voice --force`, then record again. The deliverable is portrait 1080×1920, 30fps, H.264/AAC with a Devanagari font and three-word highlighted captions. Source voice/timestamp files live in `assets/voice.mp3` and `assets/voice.words.json`.

Recording uses a native 1080×1920 browser canvas with a 360×640 mobile layout at 3× page scale. Saving the recording can take several minutes on a machine with limited memory; progress is printed before encoder flushing. Captions move above the chat reply and away from contact details. The backend retains a 25-second provider timeout; the browser allows 90 seconds including local server queuing. WordBoundary timestamps use seconds and are rescaled to match the edited voice track.

The opening uses two real rendered UI captures with a short fade and subtle camera push, excluding blank network/font loading. The sections and chatbot use the live browser recording. `npm run reel -- --opening-only` refreshes those two opening shots and exports again without sending another chatbot question. `python scripts/reel_media.py review` exports review frames and verifies decoded frame count.

For live acceptance, ask `hii`, `How are you`, a question about saved skills/projects, and a follow-up. Ask an unrelated question and verify refusal. Inspect browser Network: chat must call only your own `/api/chat` endpoint; no provider request or Authorization key should be present. Search page source, scripts and responses for the key: there must be no match. Automated HTML/JSON checks do not constitute a browser DevTools inspection.
