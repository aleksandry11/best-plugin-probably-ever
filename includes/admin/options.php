<?php
add_action('admin_init', 'register_best_plugin_settings');

function register_best_plugin_settings() {
    # add_settings_section( $id, $title, $callback, $page ); 
    add_settings_section('sender_auth', 'Sender Authentication', null, 'best-plugin-probably-ever');

    # add_settings_field( $id, $title, $callback, $page, $section, $args );
    add_settings_field('sender_email', 'E-mail', 'sender_email_cb', 'best-plugin-probably-ever', 'sender_auth');
    add_settings_field('sender_password', 'Password', 'sender_password_cb', 'best-plugin-probably-ever', 'sender_auth');

    # register_setting( $option_group, $option_name, $sanitize_callback );
    register_setting('sender_auth', 'sender_email');
    register_setting('sender_auth', 'sender_password');

}


function sender_email_cb() {
    $value = get_option('sender_email');
    
    echo "<input type='email' name='sender_email' placeholder='This email will be used to send letters' value='$value' >";
}

function sender_password_cb() {
    $value = get_option('sender_password');

    echo "<input type='password' name='sender_password' placeholder='Password' value='$value' />";
}

function best_plugin_init() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'best_plugin_probably_ever';

    echo "<div class='wrap'>";
    echo "<h1>Best Plugin Ever</h1>";

    /**
     * Sender email auth
     */
    ?>

    <form method="POST" action="options.php">
        <?php settings_fields('sender_auth'); ?>
        <?php do_settings_sections('best-plugin-probably-ever'); ?>
        <?php submit_button(); ?>
    </form>

    

    <?php
    /**
     * sent email logs
     */
    
    $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

    //How many items are in the table
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

    //how many items to list per page
    $limit = 10;

    //how many pages will there be
    $pages = ceil($total/$limit);

    //Offset for the query
    $offset = ($pagenum - 1) * $limit;


    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_next' => true,
        'prev_text' => __( '&laquo;' ),
        'next_text' => __( '&raquo;' ),
        'total' => $pages,
        'current' => $pagenum
    ) );

    echo "<div class='pagination-wrap'><div>$page_links</div></div>";

    //prepate pages query
    $sql = $wpdb->prepare("SELECT id, email, product_id, time FROM $table_name LIMIT %d OFFSET %d", array($limit, $offset));

    $results = $wpdb->get_results($sql, ARRAY_A);
    // Do we have any results?
    if (!empty($results)) :?>

        <!-- // Display the results -->

        <div class="table-wrapper">
            <h3>Sent emails log</h3>
            <table width="100%" id="best-plugin-ever-menu-logs">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Sent to:</th>
                        <th>Product's ID:</th>
                        <th>Time:</th>
                    </tr>


                    <?php foreach ($results as $row) :?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['product_id'] ?></td>
                            <td><?= $row['time'] ?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    <?php

    else :
        echo '<p>No results could be displayed.</p>';
    endif;
    
    echo '</div>';
}