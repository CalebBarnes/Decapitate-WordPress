<?php
/**
 * Plugin Name: Decapitate WordPress
 * Plugin URI: https://github.com/CalebBarnes
 * Description: Decapitates WordPress
 * Version: 0.1
 * Author: Caleb Barnes
 * Author URI: https://github.com/CalebBarnes
 */

// redirect to admin page unless preview
add_action("template_redirect", function() {
    if (!isset($_GET['preview'])) {
        wp_redirect('/wp-admin');
    }
});

