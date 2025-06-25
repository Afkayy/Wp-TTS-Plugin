<?php
/*
Plugin Name: WP TTS Plugin
Description: Adds text-to-speech functionality to posts with a clickable Dashicons speaker icon for audio playback.
Version: 1.4
Author: Grok
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function wp_tts_enqueue_assets() {
    wp_enqueue_style('dashicons');
    wp_enqueue_style('wp-tts-style', plugin_dir_url(__FILE__) . 'css/wp-tts.css', [], '1.4');
    wp_enqueue_script('wp-tts-script', plugin_dir_url(__FILE__) . 'js/wp-tts.js', ['jquery'], '1.4', true);
    wp_localize_script('wp-tts-script', 'wpTTS', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_tts_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'wp_tts_enqueue_assets');

// Add Dashicons speaker icon to post content
function wp_tts_add_speaker_icon($content) {
    if (is_singular()) {
        global $post;
        $post_id = $post->ID;
        $post_type = $post->post_type;
        error_log("TTS Icon Added: Post ID $post_id, Type $post_type");
        $icon = '<div class="wp-tts-icon" data-post-id="' . esc_attr($post_id) . '" data-post-type="' . esc_attr($post_type) . '">
                    <span class="dashicons dashicons-controls-volumeon"></span>
                 </div>';
        $content = '<div class="wp-tts-container" style="position: relative; padding: 20px;">' . $icon . $content . '</div>';
    }
    return $content;
}
add_filter('the_content', 'wp_tts_add_speaker_icon', 20);

// Handle AJAX request for TTS
function wp_tts_generate_audio() {
    check_ajax_referer('wp_tts_nonce', 'nonce');
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';
    if (!$post_id || !$post_type) {
        wp_send_json_error(['message' => 'Invalid post ID or type']);
    }
    $post = get_post($post_id);
    if (!$post || $post->post_type !== $post_type) {
        wp_send_json_error(['message' => 'Post not found']);
    }
    $text = strip_tags($post->post_content);
    if (empty($text)) {
        wp_send_json_error(['message' => 'No content to convert']);
    }
    wp_send_json_success(['text' => $text]);
}
add_action('wp_ajax_wp_tts_generate_audio', 'wp_tts_generate_audio');
add_action('wp_ajax_nopriv_wp_tts_generate_audio', 'wp_tts_generate_audio');
?>