"""Render the promo reel (1080x1920, 30 fps) from real screenshots + voice-over wavs.

Each scene = caption on top + a rounded "screen" card that pans/zooms over a real screenshot.
Frames are composed with Pillow and piped to ffmpeg; the voice-over is mixed into one track.
"""
import os
import subprocess
import sys
import wave

import imageio_ffmpeg
import numpy as np
from PIL import Image, ImageDraw, ImageFilter, ImageFont

PROJECT = r"D:\Mylaravel\Portfolie"
SCREENS = os.path.join(PROJECT, "promo", "screens")
VO = os.path.join(os.path.dirname(os.path.abspath(__file__)), "vo")
OUT = os.path.join(PROJECT, "promo", "portfolio-reel.mp4")

W, H, FPS = 1080, 1920, 30
CARD = (40, 500, 1040, 1850)  # where the screenshot card sits on the canvas
CW, CH = CARD[2] - CARD[0], CARD[3] - CARD[1]
RADIUS = 44
ACCENT = (255, 91, 46)
ACCENT2 = (255, 138, 76)
FADE = 0.4   # seconds of cross-fade between scenes
GAP = 0.35   # silence after each voice line

BOLD = r"C:\Windows\Fonts\segoeuib.ttf"
SEMI = r"C:\Windows\Fonts\seguisb.ttf" if os.path.exists(r"C:\Windows\Fonts\seguisb.ttf") else BOLD
REG = r"C:\Windows\Fonts\segoeui.ttf"


def font(path, size):
    return ImageFont.truetype(path, size)


# Scene list. "shots" are (image, start, end, weight); start/end = (x, y, zoom) in source pixels,
# where zoom 1 shows the full image width inside the card.
SCENES = [
    dict(vo="01", kicker="PORTFOLIO WEBSITE + ADMIN PANEL", title="A portfolio you\nfully control",
         shots=[("home", (0, 0, 1.0), (0, 60, 1.07), 1)]),
    dict(vo="02", kicker="THE WEBSITE", title="Modern &\nresponsive",
         shots=[("home", (0, 1050, 1.0), (0, 5200, 1.0), 1)]),
    dict(vo="03", kicker="ADMIN PANEL", title="Smart\ndashboard",
         shots=[("admin-dashboard", (0, 0, 1.0), (330, 96, 1.42), 1)]),
    dict(vo="04", kicker="PAGE CONTENT", title="Edit every text,\nphoto & CV",
         shots=[("admin-content", (0, 0, 1.0), (330, 470, 1.45), 1)]),
    dict(vo="05", kicker="CONTENT MANAGER", title="Add, edit, delete\nin seconds",
         shots=[("admin-projects", (0, 0, 1.0), (330, 190, 1.4), 1.2), ("admin-project-form", (330, 150, 1.4), (330, 520, 1.4), 1)]),
    dict(vo="06", kicker="THEMES", title="6 ready-made\nthemes",
         shots=[("admin-themes", (0, 0, 1.0), (330, 330, 1.42), 1)]),
    dict(vo="07", kicker="COLORS & BACKGROUNDS", title="Any color.\nAny look.",
         shots=[("admin-appearance", (330, 250, 1.4), (330, 520, 1.4), 1.5), ("look-showcase", (0, 0, 1.0), (0, 40, 1.04), 1),
                ("look-agency", (0, 0, 1.0), (0, 40, 1.04), 1), ("look-blueprint", (0, 0, 1.0), (0, 40, 1.04), 1),
                ("look-minimal", (0, 0, 1.0), (0, 40, 1.04), 1)]),
    dict(vo="08", kicker="CONTACT FORM + INBOX", title="Never miss\na message",
         shots=[("contact", (0, 1930, 1.0), (0, 2050, 1.05), 1), ("admin-messages", (0, 0, 1.0), (330, 96, 1.42), 1.3)]),
    dict(vo="09", kicker="PAGE BUILDER", title="Control every\npage & section",
         shots=[("admin-page-edit", (0, 0, 1.0), (330, 300, 1.42), 1)]),
    dict(vo="10", kicker="CUSTOM WEBSITE & SOFTWARE DEVELOPMENT", title="Need a similar\nwebsite?", cta=True,
         shots=[("look-showcase", (0, 0, 1.0), (0, 30, 1.03), 1)]),
]


def ease(t):
    return t * t * (3 - 2 * t)


def make_background():
    """Dark canvas with two soft accent glows and a faint grid, like the site itself."""
    bg = Image.new("RGB", (W, H), (12, 11, 10))
    glow = Image.new("RGB", (W, H), (12, 11, 10))
    d = ImageDraw.Draw(glow)
    d.ellipse((W - 520, -420, W + 420, 520), fill=(120, 44, 22))
    d.ellipse((-460, H - 620, 420, H + 260), fill=(70, 30, 18))
    glow = glow.filter(ImageFilter.GaussianBlur(170))
    bg = Image.blend(bg, glow, 0.9)
    d = ImageDraw.Draw(bg, "RGBA")
    for x in range(0, W, 72):
        d.line([(x, 0), (x, 470)], fill=(255, 255, 255, 9))
    for y in range(0, 470, 72):
        d.line([(0, y), (W, y)], fill=(255, 255, 255, 9))
    return bg


def card_mask():
    m = Image.new("L", (CW, CH), 0)
    ImageDraw.Draw(m).rounded_rectangle((0, 0, CW - 1, CH - 1), RADIUS, fill=255)
    return m


def card_shadow():
    sh = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(sh).rounded_rectangle((CARD[0] + 6, CARD[1] + 26, CARD[2] - 6, CARD[3] + 30), RADIUS, fill=(0, 0, 0, 190))
    return sh.filter(ImageFilter.GaussianBlur(34))


def draw_spaced(d, xy, text, fnt, fill, spacing):
    x, y = xy
    for ch in text:
        d.text((x, y), ch, font=fnt, fill=fill)
        x += d.textlength(ch, font=fnt) + spacing
    return x


def caption_layer(scene, index, total):
    """Transparent layer with kicker, title, progress dots and footer for one scene."""
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    kf = font(SEMI, 30)
    d.rectangle((64, 132, 118, 136), fill=ACCENT)
    draw_spaced(d, (138, 113), scene["kicker"], kf, ACCENT2, 5)
    tf = font(BOLD, 104 if not scene.get("cta") else 112)
    y = 176
    for line in scene["title"].split("\n"):
        d.text((60, y), line, font=tf, fill=(244, 239, 230))
        y += int(tf.size * 1.08)
    if scene.get("cta"):
        # call to action replaces the lower part of the card
        box = (40, 1420, 1040, 1850)
        d.rounded_rectangle(box, RADIUS, fill=(18, 16, 15, 244), outline=(255, 91, 46, 255), width=3)
        d.text((92, 1470), "Portfolio • Business site • Admin panel", font=font(SEMI, 40), fill=(244, 239, 230))
        d.text((92, 1532), "Built and customized for you.", font=font(REG, 40), fill=(170, 163, 152))
        btn = (92, 1640, 988, 1776)
        d.rounded_rectangle(btn, 68, fill=ACCENT)
        label = "DM for a demo"
        bf = font(BOLD, 56)
        tw = d.textlength(label, font=bf)
        d.text(((btn[0] + btn[2] - tw) / 2, btn[1] + 26), label, font=bf, fill=(26, 13, 7))
    # progress dots
    x = 64
    for i in range(total):
        w = 54 if i == index else 16
        d.rounded_rectangle((x, 1884, x + w, 1894), 5, fill=ACCENT if i == index else (90, 84, 76))
        x += w + 10
    return layer


class Renderer:
    def __init__(self):
        self.bg = make_background()
        self.mask = card_mask()
        self.shadow = card_shadow()
        self.images = {}
        self.border = Image.new("RGBA", (W, H), (0, 0, 0, 0))
        ImageDraw.Draw(self.border).rounded_rectangle(CARD, RADIUS, outline=(255, 255, 255, 46), width=2)

    def image(self, name):
        if name not in self.images:
            im = Image.open(os.path.join(SCREENS, name + ".png")).convert("RGB")
            need = int(im.width * CH / CW) + 2
            if im.height < need:
                # short capture: continue the page's own bottom color so the card is always filled
                tall = Image.new("RGB", (im.width, need), im.getpixel((im.width - 5, im.height - 5)))
                tall.paste(im, (0, 0))
                im = tall
            self.images[name] = im
        return self.images[name]

    def screen(self, name, x, y, zoom):
        im = self.image(name)
        cw = im.width / zoom
        ch = cw * CH / CW
        x = max(0, min(x, im.width - cw))
        y = max(0, min(y, im.height - ch))
        return im.resize((CW, CH), Image.LANCZOS, box=(x, y, min(im.width, x + cw), min(im.height, y + ch)))

    def frame(self, scene, cap, t, dur):
        """t = seconds into the scene."""
        shots = scene["shots"]
        total_w = sum(s[3] for s in shots)
        pos, acc = t / dur * total_w, 0.0
        for name, a, b, wgt in shots:
            if pos <= acc + wgt or (name, a, b, wgt) == shots[-1]:
                k = ease(min(1.0, max(0.0, (pos - acc) / wgt)))
                break
            acc += wgt
        x, y, z = (a[i] + (b[i] - a[i]) * k for i in range(3))
        canvas = self.bg.copy()
        canvas.paste(self.shadow, (0, 0), self.shadow)
        canvas.paste(self.screen(name, x, y, z), (CARD[0], CARD[1]), self.mask)
        canvas.paste(self.border, (0, 0), self.border)
        # caption slides up and fades in during the first 0.45 s
        a_in = ease(min(1.0, t / 0.45))
        if a_in >= 1:
            canvas.paste(cap, (0, 0), cap)
        else:
            faded = cap.copy()
            faded.putalpha(faded.getchannel("A").point(lambda v: int(v * a_in)))
            canvas.paste(faded, (0, int((1 - a_in) * 26)), faded)
        return canvas


def read_wav(path):
    with wave.open(path) as w:
        assert w.getnchannels() == 1 and w.getsampwidth() == 2
        return w.getframerate(), np.frombuffer(w.readframes(w.getnframes()), dtype=np.int16)


def main():
    rate = None
    voices = []
    for s in SCENES:
        r, data = read_wav(os.path.join(VO, s["vo"] + ".wav"))
        rate = rate or r
        voices.append(data)
        s["dur"] = len(data) / r + GAP

    # audio: each line starts 0.2 s into its scene
    total = sum(s["dur"] for s in SCENES)
    audio = np.zeros(int(total * rate) + rate, dtype=np.int16)
    t0 = 0.0
    for s, v in zip(SCENES, voices):
        i = int((t0 + 0.2) * rate)
        audio[i:i + len(v)] = v
        t0 += s["dur"]
    wav_path = os.path.join(VO, "voiceover.wav")
    with wave.open(wav_path, "wb") as w:
        w.setnchannels(1); w.setsampwidth(2); w.setframerate(rate); w.writeframes(audio.tobytes())

    ff = imageio_ffmpeg.get_ffmpeg_exe()
    cmd = [ff, "-y", "-loglevel", "error", "-f", "rawvideo", "-pix_fmt", "rgb24", "-s", f"{W}x{H}", "-r", str(FPS), "-i", "-",
           "-i", wav_path, "-c:v", "libx264", "-preset", "medium", "-crf", "19", "-pix_fmt", "yuv420p",
           "-c:a", "aac", "-b:a", "192k", "-movflags", "+faststart", "-shortest", OUT]
    proc = subprocess.Popen(cmd, stdin=subprocess.PIPE)

    r = Renderer()
    caps = [caption_layer(s, i, len(SCENES)) for i, s in enumerate(SCENES)]
    fade_frames = int(FADE * FPS)
    written = 0
    for i, s in enumerate(SCENES):
        n = int(round(s["dur"] * FPS))
        for f in range(n):
            img = r.frame(s, caps[i], f / FPS, s["dur"])
            # cross-fade from the last frame of the previous scene
            if i > 0 and f < fade_frames:
                img = Image.blend(prev_last, img, ease((f + 1) / fade_frames))
            if f == n - 1:
                prev_last = img
            proc.stdin.write(img.tobytes())
            written += 1
        print(f"scene {i + 1}/{len(SCENES)} done ({s['dur']:.1f}s)", flush=True)
    proc.stdin.close()
    proc.wait()
    print(f"frames={written} seconds={written / FPS:.1f} -> {OUT} exit={proc.returncode}")
    if len(sys.argv) > 1:  # also save a still of each scene for review
        for i, s in enumerate(SCENES):
            r.frame(s, caps[i], s["dur"] * 0.7, s["dur"]).resize((270, 480)).save(os.path.join(sys.argv[1], f"still-{i + 1:02d}.png"))


if __name__ == "__main__":
    main()
