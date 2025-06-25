jQuery(document).ready(function($) {
    // Remove conflicting elements
    $('#tts-play-button, #tts-post-content').remove();
    
    console.log('WP TTS Script Loaded at', new Date().toLocaleString('en-US', { timeZone: 'Asia/Karachi' }));
    console.log('wpTTS Object:', wpTTS);
    
    // Track speech state
    let isSpeaking = false;
    
    // Use event delegation for dynamic elements
    $(document).on('click', '.wp-tts-icon', function(e) {
        e.preventDefault();
        console.log('TTS Icon Clicked');
        const $icon = $(this);
        const postId = $icon.data('post-id');
        const postType = $icon.data('post-type');
        console.log('Post ID:', postId, 'Post Type:', postType);
        
        if (isSpeaking) {
            // Pause speech
            window.speechSynthesis.cancel();
            isSpeaking = false;
            $icon.find('.dashicons').removeClass('dashicons-controls-pause').addClass('dashicons-controls-volumeon');
            console.log('TTS Paused');
            return;
        }
        
        $.ajax({
            url: wpTTS.ajax_url,
            type: 'POST',
            data: {
                action: 'wp_tts_generate_audio',
                nonce: wpTTS.nonce,
                post_id: postId,
                post_type: postType
            },
            beforeSend: function() {
                console.log('Sending AJAX request...');
                // Cancel any existing speech
                window.speechSynthesis.cancel();
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                if (response.success) {
                    const utterance = new SpeechSynthesisUtterance(response.data.text);
                    utterance.onstart = function() {
                        isSpeaking = true;
                        $icon.find('.dashicons').removeClass('dashicons-controls-volumeon').addClass('dashicons-controls-pause');
                        console.log('TTS Playback Started');
                    };
                    utterance.onend = function() {
                        isSpeaking = false;
                        $icon.find('.dashicons').removeClass('dashicons-controls-pause').addClass('dashicons-controls-volumeon');
                        console.log('TTS Playback Finished');
                    };
                    window.speechSynthesis.speak(utterance);
                } else {
                    console.error('TTS Error:', response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
});