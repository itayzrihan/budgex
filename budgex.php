<?php
/**
 * Plugin Name: Budgex
 * Description: באג'קס - מערכת ניהול תקציב מתקדמת. מאפשרת לכל משתמש רשום ליצור דפי תקציב פרטיים, להזמין משתמשים אחרים, ולנהל הוצאות בצורה מקצועית.
 * Version: 1.0
 * Author: Budgex Team
 * Text Domain: budgex
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'BUDGEX_VERSION', '1.0' );
define( 'BUDGEX_DIR', plugin_dir_path( __FILE__ ) );
define( 'BUDGEX_URL', plugin_dir_url( __FILE__ ) );

// Safety check for required files
$required_files = array(
    'includes/class-database.php',
    'includes/class-permissions.php', 
    'includes/class-budget-calculator.php',
    'includes/class-budgex.php',
    'admin/class-budgex-admin.php',
    'public/class-budgex-public.php'
);

foreach ($required_files as $file) {
    $file_path = BUDGEX_DIR . $file;
    if (!file_exists($file_path)) {
        add_action('admin_notices', function() use ($file) {
            echo '<div class="notice notice-error"><p>Budgex Error: Required file missing - ' . esc_html($file) . '</p></div>';
        });
        return;
    }
    require_once $file_path;
}

// Activation hook
register_activation_hook( __FILE__, 'budgex_activate' );
register_deactivation_hook( __FILE__, 'budgex_deactivate' );

// Activation function
function budgex_activate() {
    // Create database tables
    $database = new Budgex_Database();
    $database->create_tables();
    
    // Create main Budgex frontend page
    $page_check = get_page_by_path('budgex');
    if (!$page_check) {
        $page = array(
            'post_title' => 'Budgex - ניהול תקציב',
            'post_content' => '[budgex_app]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => 'budgex',
            'post_author' => 1
        );
        wp_insert_post($page);
    }
    
    // Set flag to flush rewrite rules after activation
    update_option('budgex_flush_rewrite_rules', true);
    flush_rewrite_rules();
}

// Deactivation function
function budgex_deactivate() {
    flush_rewrite_rules();
}

// Initialize the plugin
function run_budgex() {
    load_plugin_textdomain( 'budgex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    
    if ( is_admin() ) {
        new Budgex_Admin();
    }
    
    new Budgex_Public();
    new Budgex();
}
add_action( 'plugins_loaded', 'run_budgex' );
?>