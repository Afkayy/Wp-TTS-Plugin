# WP TTS Plugin

A WordPress plugin that adds text-to-speech (TTS) functionality to your posts with a clickable Dashicons speaker icon for audio playback. It uses a Flask-based Python server with Google Text-to-Speech (gTTS) to generate MP3 audio from post content.

---

## Features

- Adds a speaker icon to each post for TTS playback.
- AJAX-powered: fetches post content and sends it to the TTS server.
- Uses browser SpeechSynthesis API for instant playback, or can be extended to play generated MP3s.
- Flask server generates MP3 audio using gTTS.
- Customizable and extendable.

---

## Folder Structure

```
wp-tts-plugin/
├── css/
│   └── wp-tts.css
├── js/
│   └── wp-tts.js
├── server.py
└── wp-tts-plugin.php
```

---

## Requirements

- WordPress site (for the plugin)
- Python 3.x
- Flask (`pip install flask`)
- gTTS (`pip install gtts`)
- (Optional) pydub and ffmpeg for audio post-processing

---

## Installation

### 1. WordPress Plugin

1. Copy the `wp-tts-plugin` folder into your WordPress `wp-content/plugins/` directory.
2. Activate the plugin from the WordPress admin dashboard.

### 2. Python TTS Server

1. Install dependencies:
    ```bash
    pip install flask gtts
    ```
    (Optional for volume adjustment)
    ```bash
    pip install pydub
    sudo apt-get install ffmpeg
    ```
2. Start the server:
    ```bash
    python server.py
    ```
    By default, it runs on `http://localhost:5002`.

---

## Usage

- Visit any post on your WordPress site.
- Click the speaker icon to listen to the post content.

---

## Customization

- To change the TTS voice, language, or speed, edit `server.py` (see the `gTTS` options).
- To adjust the icon or styles, edit `css/wp-tts.css`.
- To change client-side behavior, edit `js/wp-tts.js`.

---

## Troubleshooting

- Ensure the Python server is running and accessible from your WordPress site.
- Check browser console and `tts-server.log` for errors.
- Make sure required Python packages are installed.

---

## License

MIT License

---

## Credits

- [gTTS](https://pypi.org/project/gTTS/)
- [Flask](https://flask.palletsprojects.com/)
- WordPress & Dashicons

---
