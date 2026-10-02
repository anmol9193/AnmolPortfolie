"""Generate the Hindi (conversational) voice-over lines with a neural voice and save them as wav."""
import asyncio
import os
import subprocess
import wave

import edge_tts
import imageio_ffmpeg

VO = os.path.join(os.path.dirname(os.path.abspath(__file__)), "vo")
VOICE = "hi-IN-MadhurNeural"
RATE = "+12%"

LINES = {
    "01": "ये देखिए, एक complete portfolio website, जिसका अपना admin panel भी है।",
    "02": "Website एकदम modern है, और mobile पर भी उतनी ही अच्छी दिखती है।",
    "03": "Login करते ही dashboard पर आपका पूरा content एक नज़र में दिख जाता है।",
    "04": "कोई भी text, अपनी photo या CV, बिना code छुए, सीधे यहीं से बदल लीजिए।",
    "05": "Projects, jobs, education, skills, कुछ भी add, edit या delete कीजिए, बस कुछ seconds में।",
    "06": "छह ready-made themes हैं, एक click में पूरा look बदल जाता है।",
    "07": "Color और background भी अपनी पसंद का चुनिए, पूरी site तुरंत बदल जाती है।",
    "08": "कोई contact form भरे, तो message सीधे आपके inbox में आता है, और उसे thank you email अपने आप चला जाता है।",
    "09": "किस page पर कौन सा section दिखेगा, ये भी आप ही तय करते हैं।",
    "10": "ऐसी ही website अपने business के लिए चाहिए? हमें message कीजिए, demo दिखा देंगे।",
}


async def main():
    ff = imageio_ffmpeg.get_ffmpeg_exe()
    total = 0.0
    for key, text in LINES.items():
        mp3 = os.path.join(VO, key + ".mp3")
        wav = os.path.join(VO, key + ".wav")
        await edge_tts.Communicate(text, VOICE, rate=RATE).save(mp3)
        # mono 16-bit 44.1 kHz, with the silence at both ends trimmed
        subprocess.run([ff, "-y", "-loglevel", "error", "-i", mp3, "-af",
                        "silenceremove=start_periods=1:start_threshold=-45dB,areverse,silenceremove=start_periods=1:start_threshold=-45dB,areverse,loudnorm=I=-16:TP=-1.5",
                        "-ac", "1", "-ar", "44100", "-sample_fmt", "s16", wav], check=True)
        os.remove(mp3)
        with wave.open(wav) as w:
            dur = w.getnframes() / w.getframerate()
        total += dur
        print(key, round(dur, 2))
    with open(os.path.join(VO, "lines.txt"), "w", encoding="utf-8") as f:
        f.write("\n".join(f"{k}|{v}" for k, v in LINES.items()) + "\n")
    print("total", round(total, 2))


asyncio.run(main())
