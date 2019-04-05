<?php
/**
 *  Plugin Name: Best Plugin Probably Ever
 *  Description: Provides sending product to email
 *  Author: Alexander
 *  Version: 0.1
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Add Plugin to wordpress admin menu
 */
function best_plugin_setup_menu() {
    add_menu_page('Best Plugin Probably Ever', 'Best Plugin', 'manage_options', 'best-plugin-probably-ever', 'best_plugin_init');
}
add_action('admin_menu', 'best_plugin_setup_menu');

/**
 * Admin menu plugin page 
 */
function best_plugin_init() {
    echo "<h1>Best Plugin Ever</h1>";
}


/**
 * Creating shortcode 
 */
function best_plugin_shortcodes_init() {
    function best_plugin_shortcode($atts = [], $content = null) {
        $content = '<button id="best-plugin-ever-btn">Share</button>';

        wp_enqueue_style('best-plugin-probably-ever', plugins_url('assets/css/best-plugin-probably-ever.css', __FILE__), '1.0.0', 'all');
        wp_enqueue_script('best-plugin-probably-ever-js', plugins_url('assets/js/best-plugin-probably-ever.js', __FILE__), array('jquery'), '', true);
    
        return $content;
    }
    add_shortcode('best_plugin', 'best_plugin_shortcode');
}

add_action('init', 'best_plugin_shortcodes_init');







add_action( 'wp_footer', 'best_plugin_ever_email_form', 15 );
 
function best_plugin_ever_email_form() {
    global $product;
    if (isset($product)) :
        
        $id = $product->get_id();
        echo <<<HTML
        <div id="best-plugin-share-wrap" class="fixed-full-screen">
            <div id="best-plugin-share-overlay" class="fixed-full-screen"></div>
            <div id="best-plugin-share-content">
                <div id="best-plugin-share-close"></div>      
                <form action="" id="best-plugin-share-form" name="best-plugin-share-form">
                    <label for="email" id="best-plugin-share-label">
                        Share with: ${id}
                        <input type="email" name="email" placeholder="E-mail">
                    </label>
                    <button id="best-plugin-share-submit" type="submit">Send</button>
                </form>
            </div>
        </div>
HTML;
    endif;
}