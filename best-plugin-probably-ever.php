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

require dirname(__FILE__) . '/includes/admin/options.php';

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

/**
 * ensure css for plugin's page in admin menu
 */
add_action('admin_head', 'best_plugin_ever_admin_css');
function best_plugin_ever_admin_css() {
    wp_enqueue_style('best-plugin-probably-ever-admin', plugins_url('assets/css/best-plugin-probably-ever-admin.css', __FILE__), '1.0.0', 'all');
    wp_enqueue_script('best-plugin-probably-ever-admin-js', plugins_url('assets/js/best-plugin-probably-ever-admin.js', __FILE__), array(), '', true);
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
 * Modal Email form
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
    global $wpdb;
    var_dump(get_option('sender_email'));
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
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '587';
        $mail->isHTML();
        $mail->Username = get_option('sender_email');
        $mail->Password = get_option('sender_password');
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

        //add data to the db
        $table_name = $wpdb->prefix . 'best_plugin_probably_ever';

        $wpdb->insert(
            $table_name,
            array(
                'time'          => current_time('mysql'),
                'email'         => $to,
                'product_id'    => $product_id
            )
        );

        echo json_encode(['message' => 'Message sent to ' . $to ]);
    } catch (Exception $e) {
        echo $mail->ErrorInfo;
        throw new Error();
    }
    wp_die();
}
add_action('wp_ajax_best_plugin_probably_ever_ajax_request', 'best_plugin_probably_ever_ajax_request');
add_action('wp_ajax_nopriv_best_plugin_probably_ever_ajax_request', 'best_plugin_probably_ever_ajax_request');



/**
 * create plugin's table in database
 */
global $best_plugin_ever_db_version;
$best_plugin_ever_db_version = '1.0';

register_activation_hook( __FILE__, 'best_plugin_ever_table_install' );

function best_plugin_ever_table_install() {
    global $wpdb, $best_plugin_ever_db_version;

    $installed_version = get_option('best_plugin_ever_db_version');

    if ($installed_version !== $best_plugin_ever_db_version) {

        $table_name = $wpdb->prefix . 'best_plugin_probably_ever';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            email text NOT NULL,
            product_id mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('best_plugin_ever_db_version', $best_plugin_ever_db_version);
    }
}

function best_plugin_ever_db_update_check() {
    global $best_plugin_ever_db_version;
    if (get_site_option('best_plugin_ever_db_version') !== $best_plugin_ever_db_version) {
        best_plugin_ever_table_install();
    }    
}

add_action('plugins_loaded', 'best_plugin_ever_db_update_check');