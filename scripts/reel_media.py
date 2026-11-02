"""Hindi narration, word-synced ASS captions and a deterministic FFmpeg export."""

import asyncio
import hashlib
import json
import re
import subprocess
import sys
from pathlib import Path

import edge_tts
import imageio_ffmpeg

ROOT = Path(__file__).resolve().parents[1]
ASSETS = ROOT / "assets"
OUT = ROOT / "out"
FFMPEG = imageio_ffmpeg.get_ffmpeg_exe()
TARGET = 29.5
SCRIPT = "क्या आपने कभी ऐसा पोर्टफोलियो देखा है, जो खुद इंटरव्यू देता है? यह है मेरा नया पोर्टफोलियो। इसमें मॉडर्न डिज़ाइन है, स्मूद एनिमेशन हैं, और मेरे सारे प्रोजेक्ट्स एक ही जगह मिलते हैं। लेकिन असली जादू है यह एआई चैटबॉट। यह मेरे अपने डेटा से जवाब देता है। चलो इससे पूछते हैं, तुम्हारा सबसे अच्छा प्रोजेक्ट कौन सा है? देखो, कितनी जल्दी और सही जवाब मिला। अगर आपको भी ऐसा पोर्टफोलियो चाहिए, तो कमेंट में प्रॉम्प्ट लिखो। लिंक बायो में है।"


def run(args):
    result = subprocess.run([str(arg) for arg in args], cwd=ROOT, capture_output=True)
    if result.returncode:
        raise RuntimeError(result.stderr.decode("utf-8", errors="replace")[-5000:])
    return result


def audio_duration(path):
    pcm = run([FFMPEG, "-v", "error", "-i", path, "-vn", "-ac", "1", "-ar", "24000", "-f", "s16le", "-"])
    return len(pcm.stdout) / 48000


async def synthesize(rate):
    words = []
    path = ASSETS / "voice.raw.mp3"
    speech = edge_tts.Communicate(SCRIPT, "hi-IN-MadhurNeural", rate=f"{rate:+d}%", boundary="WordBoundary", connect_timeout=15, receive_timeout=45)
    with path.open("wb") as audio:
        async for event in speech.stream():
            if event["type"] == "audio":
                audio.write(event["data"])
            elif event["type"] == "WordBoundary":
                words.append({"text": event["text"], "start": event["offset"] / 10_000_000, "duration": event["duration"] / 10_000_000})
    if not words:
        raise RuntimeError("The voice service did not return word boundaries.")
    return path, words, audio_duration(path)


async def voice():
    ASSETS.mkdir(exist_ok=True)
    OUT.mkdir(exist_ok=True)
    fingerprint = hashlib.sha256(SCRIPT.encode()).hexdigest()
    metadata_path = ASSETS / "voice.words.json"
    if metadata_path.exists() and (ASSETS / "voice.mp3").exists() and "--force" not in sys.argv:
        metadata = json.loads(metadata_path.read_text(encoding="utf-8"))
        if metadata.get("script_hash") == fingerprint:
            print(json.dumps({key: value for key, value in metadata.items() if key != "words"}))
            return
    chosen_rate = 0
    path, words, duration = await synthesize(chosen_rate)
    if duration > 30 or duration < 28:
        chosen_rate = 10 if duration > 30 else -10
        path, words, duration = await synthesize(chosen_rate)
    tempo = duration / TARGET
    if not 0.5 <= tempo <= 2:
        raise RuntimeError("Narration cannot fit 30 seconds at an intelligible tempo.")
    run([FFMPEG, "-y", "-v", "error", "-i", path, "-af", f"atempo={tempo:.8f},apad,atrim=duration={TARGET}", "-ar", "48000", "-codec:a", "libmp3lame", "-b:a", "192k", ASSETS / "voice.mp3"])
    for word in words:
        word["start"] = round(word["start"] / tempo, 4)
        word["duration"] = round(word["duration"] / tempo, 4)
        word["end"] = round(word["start"] + word["duration"], 4)
    metadata = {"voice": "hi-IN-MadhurNeural", "rate": f"{chosen_rate:+d}%", "raw_duration": duration, "tempo_fit": tempo, "duration": audio_duration(ASSETS / "voice.mp3"), "script_hash": fingerprint, "words": words}
    metadata_path.write_text(json.dumps(metadata, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps({key: value for key, value in metadata.items() if key != "words"}))


def stamp(seconds):
    total = round(max(0, seconds) * 100)
    return f"{total // 360000}:{total // 6000 % 60:02}:{total // 100 % 60:02}.{total % 100:02}"


def escape(text):
    return text.replace("\\", "").replace("{", "").replace("}", "").replace("\n", " ")


def captions():
    data = json.loads((ASSETS / "voice.words.json").read_text(encoding="utf-8"))
    header = """[Script Info]
ScriptType: v4.00+
PlayResX: 1080
PlayResY: 1920
WrapStyle: 2
ScaledBorderAndShadow: yes

[V4+ Styles]
Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding
Style: Caption,Mukta,76,&H00FFFFFF,&H00FFFFFF,&H00101010,&H80000000,-1,0,0,0,100,100,0,0,1,5,2,5,80,80,0,1
Style: Brand,Mukta,36,&H00FFFFFF,&H00FFFFFF,&H00101010,&H80000000,-1,0,0,0,100,100,0,0,1,2,0,8,80,80,290,1

[Events]
Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text
"""
    lines = [header]
    words = data["words"]
    groups = [words[index:index + 3] for index in range(0, len(words), 3)]
    for group in groups:
        for index, word in enumerate(group):
            end = group[index + 1]["start"] if index + 1 < len(group) else word["end"] + 0.035
            text = " ".join((r"{\c&H004FEAFF&}" + escape(item["text"]) + r"{\c&H00FFFFFF&}") if item is word else escape(item["text"]) for item in group)
            pop = r"\fscx88\fscy88\t(0,90,\fscx100\fscy100)" if index == 0 else ""
            # Entire caption box stays between y=250 and y=1520.
            caption_y = 340 if 11.9 <= word["start"] < 23.91 else 1050 if word["start"] >= 23.91 else 1370
            position = rf"{{\an5\pos(540,{caption_y})" + pop + "}"
            lines.append(f"Dialogue: 2,{stamp(word['start'])},{stamp(min(TARGET, end))},Caption,,0,0,0,,{position}{text}\n")
    lines.append("Dialogue: 1,0:00:00.00,0:00:11.90,Brand,,0,0,0,,ANMOL SAINI  /  AI PORTFOLIO\n")
    lines.append(f"Dialogue: 1,0:00:23.91,{stamp(TARGET)},Brand,,0,0,0,,ANMOL SAINI  /  AI PORTFOLIO\n")
    lines.append("Dialogue: 1,0:00:18.90,0:00:21.20,Brand,,0,0,0,,{\\an5\\pos(540,1460)\\fs32}Live reply · wait shortened\n")
    lines.append(f"Dialogue: 3,0:00:25.00,{stamp(TARGET)},Brand,,0,0,0,,{{\\an8\\pos(540,370)\\fs64\\fad(180,0)}}LINK IN BIO\n")
    (OUT / "captions.ass").write_text("".join(lines), encoding="utf-8-sig")


def render():
    captions()
    timeline = json.loads((OUT / "capture.json").read_text(encoding="utf-8"))
    segments = timeline["segments"]
    clips = OUT / "clips"
    clips.mkdir(exist_ok=True)
    concat_lines = []
    for index, segment in enumerate(segments):
        clip = clips / f"{index}.mp4"
        if segment.get("image"):
            run([FFMPEG, "-y", "-v", "error", "-loop", "1", "-framerate", "30", "-i", ROOT / segment["image"], "-t", str(segment["duration"]), "-vf", "zoompan=z='min(zoom+0.00025,1.025)':d=1:s=1080x1920:fps=30,fade=t=in:st=0:d=0.25:color=white,format=yuv420p", "-an", "-threads", "2", "-c:v", "libx264", "-preset", "veryfast", "-crf", "16", clip])
            concat_lines.append(f"file 'clips/{index}.mp4'\n")
            print(f"Rendered opening shot {index + 1}", flush=True)
            continue
        length = segment["end"] - segment["start"]
        factor = segment["duration"] / length
        run([FFMPEG, "-y", "-v", "error", "-ss", str(segment["start"]), "-t", str(length), "-i", OUT / "capture.webm", "-vf", f"setpts=(PTS-STARTPTS)*{factor:.8f},fps=30", "-an", "-threads", "2", "-c:v", "libx264", "-preset", "veryfast", "-crf", "16", clip])
        concat_lines.append(f"file 'clips/{index}.mp4'\n")
        print(f"Retimed scene {index + 1}/{len(segments)}", flush=True)
    (OUT / "clips.txt").write_text("".join(concat_lines), encoding="utf-8")
    filters = ["[0:v]scale=1080:1920:flags=lanczos,setsar=1,tpad=stop_mode=clone:stop_duration=1,trim=duration=29.5,subtitles=filename=out/captions.ass:fontsdir=assets/fonts,format=yuv420p[v]"]
    args = [FFMPEG, "-y", "-hide_banner", "-filter_complex_threads", "1", "-f", "concat", "-safe", "0", "-i", OUT / "clips.txt", "-i", ASSETS / "voice.mp3"]
    music = ASSETS / "music.mp3"
    if music.exists():
        args += ["-stream_loop", "-1", "-i", music]
        filters += ["[1:a]loudnorm=I=-16:TP=-1.5:LRA=11[vo]", "[2:a]volume=0.035[music]", "[vo][music]amix=inputs=2:duration=first:normalize=0,alimiter=limit=0.95[a]"]
    else:
        filters.append("[1:a]loudnorm=I=-16:TP=-1.5:LRA=11[a]")
    args += ["-filter_complex", ";".join(filters), "-map", "[v]", "-map", "[a]", "-t", str(TARGET), "-r", "30", "-c:v", "libx264", "-threads", "2", "-preset", "fast", "-crf", "19", "-pix_fmt", "yuv420p", "-c:a", "aac", "-b:a", "192k", "-ar", "48000", "-ac", "2", "-movflags", "+faststart", OUT / "reel.mp4"]
    run(args)
    info = subprocess.run([FFMPEG, "-hide_banner", "-i", str(OUT / "reel.mp4")], capture_output=True).stderr.decode(errors="replace")
    duration = re.search(r"Duration: (\d+):(\d+):([\d.]+)", info)
    seconds = int(duration[1]) * 3600 + int(duration[2]) * 60 + float(duration[3]) if duration else 0
    if not 28 <= seconds <= 30 or "1080x1920" not in info or "yuv420p" not in info or "h264" not in info or "aac" not in info:
        raise RuntimeError("Final video does not meet the delivery format: " + info[-1500:])
    report = {"file": "out/reel.mp4", "duration": seconds, "resolution": "1080x1920", "fps": 30, "video": "H.264", "audio": "AAC", "pixel_format": "yuv420p", "music": music.exists(), "captions": "Mukta / word-synced / 3 words / active word yellow", "safe_zone": {"top": 250, "bottom": 400}, "voice": json.loads((ASSETS / "voice.words.json").read_text(encoding="utf-8")), "capture": timeline}
    (OUT / "report.json").write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps({key: value for key, value in report.items() if key not in ["voice", "capture"]}))


if __name__ == "__main__":
    try:
        if sys.argv[1] == "voice":
            asyncio.run(voice())
        elif sys.argv[1] == "render":
            render()
        elif sys.argv[1] == "ffmpeg":
            print(FFMPEG)
        elif sys.argv[1] == "frame":
            run([FFMPEG, "-y", "-ss", sys.argv[3], "-i", sys.argv[2], "-frames:v", "1", sys.argv[4]])
        elif sys.argv[1] == "review":
            for second in [1, 3.5, 9.5, 14, 20.8, 22.7, 27.5]:
                run([FFMPEG, "-y", "-v", "error", "-ss", str(second), "-i", OUT / "reel.mp4", "-frames:v", "1", OUT / f"review-{second}.png"])
            frames, seconds = imageio_ffmpeg.count_frames_and_secs(str(OUT / "reel.mp4"))
            print(json.dumps({"decoded_frames": frames, "decoded_seconds": seconds, "expected_frames": 885}))
        elif sys.argv[1] == "inspect":
            frames = imageio_ffmpeg.read_frames(str(OUT / "capture.webm"))
            print(json.dumps(next(frames)))
            frames.close()
        elif sys.argv[1] == "review-source":
            for second in [6, 8, 10, 12]:
                run([FFMPEG, "-y", "-v", "error", "-ss", str(second), "-i", OUT / "capture.webm", "-frames:v", "1", OUT / f"source-{second}.png"])
        else:
            raise ValueError("Use voice, render or ffmpeg.")
    except Exception as error:
        print(f"Reel media failed: {error}", file=sys.stderr)
        sys.exit(1)
