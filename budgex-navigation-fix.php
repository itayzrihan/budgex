<?php
/**
 * Budgex Navigation Fix - WordPress Compatible
 * 
 * Place this file in your active theme directory and access it via:
 * https://mytx.one/wp-content/themes/YOUR_THEME/budgex-navigation-fix.php
 * 
 * Or add it to your theme's functions.php:
 * include_once 'budgex-navigation-fix.php';
 */

// Ensure WordPress is loaded
if (!function_exists('home_url')) {
    // Try to load WordPress
    $wp_paths = [
        __DIR__ . '/../../../../wp-config.php',
        __DIR__ . '/../../../wp-config.php',
        __DIR__ . '/../../wp-config.php',
        __DIR__ . '/../wp-config.php'
    ];
    
    foreach ($wp_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
}

// If still not loaded, try a different approach
if (!function_exists('home_url')) {
    echo "WordPress not loaded. Please run this script through WordPress admin or place in theme directory.";
    exit;
}

// Add to WordPress init if being included
if (!defined('BUDGEX_NAV_FIX_APPLIED')) {
    define('BUDGEX_NAV_FIX_APPLIED', true);
    add_action('init', 'budgex_apply_navigation_fix');
}

function budgex_apply_navigation_fix() {
    // Force flush rewrite rules
    flush_rewrite_rules(true);
    
    // Set option to flush again on next Budgex init
    update_option('budgex_flush_rewrite_rules', 1);
    
    // Log the fix application
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Budgex Navigation Fix Applied: Rewrite rules flushed');
    }
}

// If accessed directly, show status
if (!headers_sent()) {
    echo "<h1>Budgex Navigation Fix Applied</h1>";
    echo "<p>Rewrite rules have been flushed.</p>";
    echo "<p>Please go to <strong>Settings → Permalinks</strong> in WordPress admin and click 'Save Changes'.</p>";
    echo "<p>Then test your navigation again.</p>";
    
    // Show current URLs for testing
    echo "<h2>Test URLs:</h2>";
    echo "<ul>";
    echo "<li><a href='" . home_url('/budgex/') . "' target='_blank'>Dashboard: " . home_url('/budgex/') . "</a></li>";
    
    if (class_exists('Budgex_Database') && is_user_logged_in()) {
        $database = new Budgex_Database();
        $user_id = get_current_user_id();
        $budgets = $database->get_user_budgets($user_id);
        
        if (!empty($budgets)) {
            $test_budget = $budgets[0];
            $budget_url = home_url('/budgex/budget/' . $test_budget->id . '/');
            $management_url = home_url('/budgex/budget/' . $test_budget->id . '/#advanced-management-panel');
            echo "<li><a href='$budget_url' target='_blank'>Test Budget: $budget_url</a></li>";
            echo "<li><a href='$management_url' target='_blank'>Test Management: $management_url</a></li>";
        }
    }
    echo "</ul>";
}
?>
