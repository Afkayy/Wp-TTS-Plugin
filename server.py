from flask import Flask, request, send_file, jsonify
import logging
from gtts import gTTS
import os
import tempfile

app = Flask(__name__)

logging.basicConfig(level=logging.DEBUG, filename='tts-server.log', filemode='a', format='%(asctime)s - %(levelname)s - %(message)s')

@app.route('/api/tts', methods=['POST'])
def tts():
    try:
        logging.info("Received TTS request")
        data = request.get_json()
        if not data or 'text' not in data:
            logging.error("Missing 'text' field in JSON request")
            return jsonify({"error": "Missing 'text' field in JSON request"}), 400

        text = data['text']
        logging.info(f"Text received: {text}")
        if not text or not isinstance(text, str):
            logging.error("Invalid or empty text provided")
            return jsonify({"error": "Text must be a non-empty string"}), 400

        with tempfile.NamedTemporaryFile(suffix='.mp3', delete=False) as temp_file:
            temp_filename = temp_file.name

        logging.info("Generating audio with gTTS")
        try:
            tts = gTTS(text=text, lang='en', slow=True)
            tts.save(temp_filename)
            logging.info(f"Audio saved to {temp_filename}")
        except Exception as e:
            logging.error(f"gTTS error: {str(e)}")
            os.unlink(temp_filename)
            return jsonify({"error": f"gTTS error: {str(e)}"}), 500

        with open(temp_filename, 'rb') as f:
            mp3_data = f.read(3)
            if not mp3_data:
                logging.error("Generated file is empty")
                os.unlink(temp_filename)
                return jsonify({"error": "Generated file is empty"}), 500
            logging.info(f"MP3 starts with: {mp3_data.hex()}")

        response = send_file(
            temp_filename,
            mimetype='audio/mpeg',
            as_attachment=True,
            download_name='output.mp3'
        )
        response.headers['Content-Type'] = 'audio/mpeg'
        logging.info("Sending MP3 response")

        @response.call_on_close
        def cleanup():
            try:
                os.unlink(temp_filename)
                logging.info(f"Deleted {temp_filename}")
            except Exception as e:
                logging.error(f"Error deleting file: {str(e)}")

        return response

    except Exception as e:
        logging.error(f"Server error: {str(e)}", exc_info=True)
        return jsonify({"error": f"Server error: {str(e)}"}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5002, debug=True)