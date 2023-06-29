<?php
/*
Plugin Name: Custom WP Data and Ajax
Description: A custom WordPress plugin.
Version: 1.0
Author: Oana Sasaran
*/

register_activation_hook(__FILE__, 'plugin_activate');
register_deactivation_hook(__FILE__, 'plugin_deactivate');

function plugin_activate() {
    //Create the database table and populate it with dummy data
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_db_table';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    //Check if the file has already been included, and if so, not include
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    //Insert dummy data into the database table
    $wpdb->insert(
        $table_name,
        array(
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ',
        )
    );
    $wpdb->insert(
        $table_name,
        array(
            'name' => 'Sed euismod arcu vel libero dignissim, ut pulvinar lectus blandit.',
        )
    );
    $wpdb->insert(
        $table_name,
        array(
            'name' => ' In at pretium augue, a placerat erat.', 
        )
    );
}

function plugin_deactivate() {
    //Remove the database table
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_db_table';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

//Add an admin menu item for the plugin
add_action('admin_menu', 'plugin_menu');

function plugin_menu() {
    add_menu_page(
        'WP Data and Ajax',
        'WP Data and Ajax',
        'manage_options',
        'custom-plugin',
        'plugin_page',
        'dashicons-admin-plugins',
        30
    );
}

//Callback function for the admin page
function plugin_page() {
    //Retrieve data from the custom table
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_db_table';
    $data = $wpdb->get_results("SELECT * FROM $table_name");

    //Display the data in a table on the admin page
    echo '<div class="custom-plugin-container wrap">';
    echo '<h1 class="title">Add data to a database table and display articles in Homepage</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Id</th><th>Name</th></tr></thead>';
    echo '<tbody>';
    foreach ($data as $row) {
        echo '<tr><td>' . $row->id . '</td><td>' . $row->name . '</td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

//Add JavaScript file for the frontend
add_action('wp_enqueue_scripts', 'plugin_enqueue_scripts');

function plugin_enqueue_scripts() {
    wp_enqueue_script('custom-plugin', plugin_dir_url(__FILE__) . 'js/custom-plugin.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-plugin', 'customPluginAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

//Add CSS file for the admin page
add_action('admin_enqueue_scripts', 'plugin_enqueue_admin_styles');

function plugin_enqueue_admin_styles() {
    wp_enqueue_style('custom-plugin', plugin_dir_url(__FILE__) . 'css/custom-plugin.css');
}

//Add CSS file for the frontend
add_action('wp_enqueue_scripts', 'plugin_enqueue_frontend_styles');

function plugin_enqueue_frontend_styles() {
    wp_enqueue_style('custom-plugin-frontend-styles', plugin_dir_url(__FILE__) . 'css/custom-plugin.css');

}

//AJAX handler to retrieve WordPress posts
add_action('wp_ajax_custom_plugin_get_posts', 'plugin_get_posts');
add_action('wp_ajax_nopriv_custom_plugin_get_posts', 'plugin_get_posts');

function plugin_get_posts() {
    $posts = get_posts(array(
        'posts_per_page' => 5,
    ));

    $formatted_posts = array();
    foreach ($posts as $post) {
        $formatted_posts[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
        );
    }

    wp_send_json($formatted_posts);
}
