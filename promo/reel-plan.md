# Promo Reel — Portfolio Website + Admin Panel

Draft video: `promo/portfolio-reel.mp4` (1080×1920, 30 fps, 54 s, Hindi voice-over included, no music).
Real screens used: `promo/screens/*.png` (captured from the running project).

## 1. What the project actually is

A personal portfolio website (Laravel + MySQL) with a private admin panel. Everything below was
checked in the code and in the running app. There are **no** payments, reports, multiple user roles
or search screens — none of those are claimed in the video.

| # | Feature (real) | Where it lives |
|---|---|---|
| 1 | Public portfolio site: hero, about, services, skills, experience, projects slider with filter tabs, process, global reach, education, contact | `/`, `/about`, `/services`, `/skills`, `/experience`, `/projects`, `/education`, `/contact` |
| 2 | Admin login | `/login` |
| 3 | Dashboard: content counts, recently updated, look & feel, pages | `/admin` |
| 4 | Edit every text, profile photo, CV, favicon | `/admin/content` |
| 5 | Add / edit / delete projects, experience, education, services, skills, process steps, countries (with image upload, delete confirmation) | `/admin/items/{type}` |
| 6 | 6 ready-made themes (header, hero, cards, footer, section order) | `/admin/themes` |
| 7 | 12 accent colors + custom color picker, 6 page backgrounds + custom, dark/light | `/admin/appearance` |
| 8 | Contact form saves messages, admin inbox with unread badge, automatic thank-you email | `/contact`, `/admin/messages` |
| 9 | Per-page editor: menu name, browser title, show in menu, which sections a page shows | `/admin/pages` |
| 10 | Account: profile picture, name, email, change password | `/admin/account` |

## 2. Timeline (as rendered in the draft)

| Time | Screen shown | On-screen text | Voice-over |
|---|---|---|---|
| 0.0–5.5 | Home page hero (mobile view) | **A portfolio you fully control** · PORTFOLIO WEBSITE + ADMIN PANEL | "ये देखिए, एक complete portfolio website, जिसका अपना admin panel भी है।" |
| 5.5–10.2 | Home page scrolling: about → services → skills | **Modern & responsive** | "Website एकदम modern है, और mobile पर भी उतनी ही अच्छी दिखती है।" |
| 10.2–15.1 | Admin dashboard, zoom into the content | **Smart dashboard** | "Login करते ही dashboard पर आपका पूरा content एक नज़र में दिख जाता है।" |
| 15.1–20.4 | Page content → Hero tab, zoom into the fields | **Edit every text, photo & CV** | "कोई भी text, अपनी photo या CV, बिना code छुए, सीधे यहीं से बदल लीजिए।" |
| 20.4–26.7 | Projects list, then the project edit form | **Add, edit, delete in seconds** | "Projects, jobs, education, skills, कुछ भी add, edit या delete कीजिए, बस कुछ seconds में।" |
| 26.7–30.9 | Themes page (6 theme cards) | **6 ready-made themes** | "छह ready-made themes हैं, एक click में पूरा look बदल जाता है।" |
| 30.9–35.9 | Appearance page, then 4 quick looks of the same home page (Showcase/indigo, Agency/amber, Blueprint/green, Minimal/rose) | **Any color. Any look.** | "Color और background भी अपनी पसंद का चुनिए, पूरी site तुरंत बदल जाती है।" |
| 35.9–43.5 | Contact form on the site, then the admin inbox | **Never miss a message** | "कोई contact form भरे, तो message सीधे आपके inbox में आता है, और उसे thank you email अपने आप चला जाता है।" |
| 43.5–47.9 | Site pages → edit Home (section checkboxes) | **Control every page & section** | "किस page पर कौन सा section दिखेगा, ये भी आप ही तय करते हैं।" |
| 47.9–54.1 | Home page (Showcase look) + CTA card | **Need a similar website?** · DM for a demo | "ऐसी ही website अपने business के लिए चाहिए? हमें message कीजिए, demo दिखा देंगे।" |

Transitions: 0.4 s cross-fade between scenes; the caption slides up and fades in at the start of each scene.

## 3. If you re-record the screens yourself (recommended for the final version)

Record at 1080p, browser zoom 110–125 %, cursor visible, slow and steady mouse.

1. `/` — hold on the hero 3 s, then scroll smoothly to the projects slider (8–10 s, speed up 2× in the edit).
2. `/login` — type the email and password, click Sign in (3 s, speed up).
3. `/admin` — hold 3 s, hover the three tiles.
4. `/admin/content` → **Hero** tab — change one word in "Status pill", click **Save changes**, wait for the "Done" alert (5 s).
5. `/admin/items/projects` — scroll the list (2 s), click **Edit** on one project, show the form and the screenshot field (4 s). Optional: click **Delete** on a test item to show the confirmation box, then Cancel.
6. `/admin/themes` — click the **Showcase** card, click **Apply theme** (3 s).
7. `/admin/appearance` — click 3–4 accent colors so the page recolors live, pick a background, **Save changes** (5 s).
8. Open `/` in a new tab to show the new look (2 s).
9. `/contact` — fill the form and send (5 s, speed up), show the green "Thank you" note.
10. `/admin/messages` — show the unread badge in the sidebar, open the new message (4 s).
11. `/admin/pages` → **Edit** Home — untick/tick one section (3 s).
12. Back to `/` for the closing shot (2 s).

Before recording: delete the test messages in the inbox and add 2–3 realistic ones, so no "test" names appear.

## 4. Editing notes (CapCut / VN)

- Format 9:16, 1080×1920, 30 fps. Put the screen recording in the lower two thirds, caption on top (as in the draft).
- Screen recordings are 16:9: scale them up and keyframe a slow zoom (100 % → 140 %) towards the part being talked about.
- Speed up typing and scrolling 2–3×; never speed up the moment a result appears (alert, new theme, new message).
- One highlight at most per scene: a rounded rectangle or a short zoom on the button being clicked.
- Text: one bold line per scene, max 4 words, in for the whole scene. No more than two font weights.
- Music: light, modern, 100–110 bpm, no vocals, at about 15–20 % volume under the voice. Lower it further in the last scene.
- Voice-over: the draft uses a Hindi neural voice (hi-IN Madhur) speaking everyday Hinglish; record your own voice over the same lines if you want it more personal.
- Cut on the action (click → result), not in the middle of a scroll.

## 5. Final call to action (last 5–6 s)

Text: **Need a similar website?** / Custom Website & Software Development / **DM for a demo**
Voice: "ऐसी ही website अपने business के लिए चाहिए? हमें message कीजिए, demo दिखा देंगे।"

## 6. Instagram content

**Reel title:** Portfolio Website with a Full Admin Panel

**Thumbnail text:** Your Portfolio. Your Control.

**Caption:**
A portfolio website you can run yourself. 🚀

✅ Edit every text, photo and CV from the admin panel
✅ Add projects, experience, education and skills in seconds
✅ 6 ready-made themes, any color, any background
✅ Contact form with an inbox and automatic thank-you email
✅ Works on mobile, tablet and desktop

Built with Laravel and MySQL. Need a similar website or admin panel for your business, institute or personal brand? DM us for a demo.

**Short caption:**
Portfolio website + full admin panel. Change text, themes and colors without code. Want one like this? DM for a demo. 📩

**Call to action:** DM "DEMO" for a live walkthrough.

**Hashtags:**
#webdevelopment #laravel #portfoliowebsite #adminpanel #websitedesign #customsoftware #webdeveloper #freelancedeveloper #fullstackdeveloper #php #mysql #responsivedesign #dashboarddesign #softwaredevelopment #smallbusinesswebsite #lucknow #indiandeveloper #websitefordemo
