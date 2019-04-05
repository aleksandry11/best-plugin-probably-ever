<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 *  Plugin Name: Best Plugin Probably Ever
 *  Description: Provides sending product to email
 *  Author: Alexander
 *  Version: 0.1
 */

if ( ! defined('ABSPATH') ) exit;

require dirname(__FILE__) . '/assets/libs/PHPMailer/src/PHPMailer.php';
require dirname(__FILE__) . '/assets/libs/PHPMailer/src/SMTP.php';
require dirname(__FILE__) . '/assets/libs/PHPMailer/src/Exception.php';

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
        wp_enqueue_script('best_plugin_ever_ajax_url', plugins_url('assets/js/best-plugin-probably-ever.js', __FILE__), array('jquery'), '', true);
        
        /**
         * ajax url
         */
        
        wp_localize_script( 'best_plugin_ever_ajax_url', 'best_plugin_ever_ajax_url', 
            array(
                'url' => admin_url('admin-ajax.php')
            )
        );  

        return $content;
    }
    add_shortcode('best_plugin', 'best_plugin_shortcode');
}

add_action('init', 'best_plugin_shortcodes_init');


/**
 * Email form
 */
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
                        Share with: <span class="notice">Incorrect email</span>
                        <input type="email" name="email" placeholder="E-mail" required="required"/>
                        <input type="hidden" name="product-id" value="${id}">
                    </label>
                    <button id="best-plugin-share-submit" type="submit">Send</button>
                </form>
            </div>
        </div>
HTML;
    endif;
}




/**
 * ajax handler
 */
function best_plugin_probably_ever_ajax_request() {
    status_header(500);
    $product_id = $_REQUEST['data']['id'];
    $product = wc_get_product($product_id);
    $to = $_REQUEST['data']['email'];
    $product_image = get_the_post_thumbnail_url($product_id, 'full');
    $product_name = $product->get_name();
    $product_link = get_permalink($product_id);

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = 'charen.test.email@gmail.com';
        $mail->Password = '4auOT7thgrOb';
        $mail->setFrom('no-reply@gmail.com', 'no-reply@charen.com');
        $mail->Subject = 'Hello';
        $mail->Body = <<<HTML
        <p>Proudct's name: ${product_name}</p>
        <div>
            <img src="${product_image}" alt="${product_name}">
        </div>
        <a href="${product_link}">Buy now!</a>
HTML;
        $mail->addAddress($to);
        $mail->send();
        status_header(200);
        echo json_encode(['message' => 'Message sent to ' . $to ]);
    } catch (Exception $e) {
        echo $mail->ErrorInfo;
        throw new Error();
    }
    wp_die();
}
add_action('wp_ajax_best_plugin_probably_ever_ajax_request', 'best_plugin_probably_ever_ajax_request');
add_action('wp_ajax_nopriv_best_plugin_probably_ever_ajax_request', 'best_plugin_probably_ever_ajax_request');