<?php
/**
 * Plugin Name: Uptime, SEO and Security monitors - UptimeZone
 * Description: Downtime Happens. Get Notified! Uptime, SEO, and Vulnerability monitors for your website, totally free. Get alerted whenever downtime happens to your website. Receive notifications of any SEO or security issues to reduce potential damage.
 * Author: Chatra
 * Text Domain: uptime-seo-and-security-monitors-uptimezone
 * Version: 1.0.1
 */

// Add multilingual support
//load_plugin_textdomain('uptimezone', false, basename( dirname( __FILE__ ) ) . '/languages' );

//register_activation_hook( __FILE__, 'my_plugin_activate' );
//add_action('plugins_loaded', 'plugin_activate');
register_activation_hook( __FILE__,'plugin_activate' );
function plugin_activate() {
    $var = get_option('uptimezone_is_installation_completed');
    if ($var == false) {
        update_option('uptimezone_is_installation_completed', true);
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.uptimezone.com/wp_install");
            curl_setopt($ch, CURLOPT_USERAGENT, empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            @curl_exec($ch);
            @curl_close($ch);     
        }
    }
}

// Add settings page and register settings with WordPress
add_action('admin_menu', 'uptimezone_setup');

function uptimezone_setup() {
  // add_menu_page( 'uptimezone Plugin Page', 'UptimeZone Plugin', 'manage_options', 'options-uptimezone', 'uptimezone_settings' );
  add_submenu_page('options-general.php', __( 'UptimeZone Plugin', 'uptimezone'), __( 'UptimeZone Monitors', 'uptimezone'), 'manage_options', 'options-uptimezone', 'uptimezone_settings' );

  register_setting( 'uptimezone', 'uptimezone-code' );
}

// Display settings page
function uptimezone_settings() {
  echo "<h1>" . __( 'UptimeZone Setup', 'uptimezone' ) . "</h1><hr>";
  if (get_option('uptimezone-code')) {
    echo "<p>" . __( '<h4>Seems like everything is OK!</h4>
<h4>Don\'t forget to sign in to your <a href="https://uptimezone.com/signin/?utm_source=WP&utm_campaign=WP" target="_blank">UptimeZone dashboard</a> to create monitors <i>or</i> change your existing ones.</h4><hr><br>', 'uptimezone');
  } else {
    echo "" . __( '<p>1. Sign up for a free UptimeZone account at <a href="https://uptimezone.com/signup/?utm_source=WP&utm_campaign=WP" target="_blank">uptimezone.com</a></p><p>2. Copy and paste Public Token from the <i>My Settings</i> section into the form below:</p>
', 'uptimezone' ) . "";
  }

  echo "<form action=\"options.php\" method=\"POST\">";

    // Show success message when code is saved
    // if (isset($_GET['settings-updated'])) {
    //   echo "<p><strong style=\"color: green;\">Settings updated successfully.</strong><br><br>";
    // }

    settings_fields( 'uptimezone' );
    do_settings_sections( 'uptimezone' );
    echo "
<!--<textarea cols=\"80\" rows=\"14\" name=\"uptimezone-code\">" . esc_attr( get_option('uptimezone-code') ) . "</textarea>
-->
Your token: <input name=\"uptimezone-code\" size='50%' value='" . esc_attr( get_option('uptimezone-code') ) . "'>
";
    submit_button();
  echo "</form>";
}


// Add the code to footer
add_action('wp_footer', 'add_uptimezone_code');
function add_uptimezone_code() {
  echo '
  <script>
       (function(d, w, c) {
            w.UptimezoneID = \''.get_option( 'uptimezone-code' ).'\';
            var s = d.createElement(\'script\');
            w[c] = w[c] || function() {
                (w[c].q = w[c].q || []).push(arguments);
            };
            s.async = true;
            s.src = \'https://uptimezone.com/call.js\';
            if (d.head) d.head.appendChild(s);
        })(document, window, \'Uptimezone\');
 </script>
  
  ';
}
