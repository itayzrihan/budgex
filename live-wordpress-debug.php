<?php
/**
 * Live WordPress Debug - Empty Page Issue
 * This file will help diagnose what's happening in the actual WordPress environment
 */

// Add this to your WordPress functions.php temporarily or create as a plugin
add_action('wp_footer', 'budgex_debug_empty_page');

function budgex_debug_empty_page() {
    // Only run on budgex pages
    if (strpos($_SERVER['REQUEST_URI'], '/budgex/') === false) {
        return;
    }
    
    echo '<div id="budgex-debug-panel" style="
        position: fixed; 
        bottom: 0; 
        left: 0; 
        right: 0; 
        background: #000; 
        color: #fff; 
        padding: 15px; 
        z-index: 99999; 
        font-family: monospace; 
        font-size: 12px;
        max-height: 200px;
        overflow-y: auto;
        border-top: 3px solid #ff0000;
    ">';
    
    echo '<h4 style="margin: 0 0 10px 0; color: #ff6b6b;">🔍 Budgex Debug Panel</h4>';
    
    // 1. Check current URL and query vars
    echo '<p><strong>Current URL:</strong> ' . $_SERVER['REQUEST_URI'] . '</p>';
    echo '<p><strong>Query Vars:</strong> budgex_page=' . get_query_var('budgex_page') . ', budget_id=' . get_query_var('budget_id') . '</p>';
    
    // 2. Check if plugin classes exist
    $classes_check = [
        'Budgex' => class_exists('Budgex'),
        'Budgex_Public' => class_exists('Budgex_Public'),
        'Budgex_Database' => class_exists('Budgex_Database'),
        'Budgex_Budget_Calculator' => class_exists('Budgex_Budget_Calculator'),
        'Budgex_Permissions' => class_exists('Budgex_Permissions')
    ];
    
    echo '<p><strong>Plugin Classes:</strong> ';
    foreach ($classes_check as $class => $exists) {
        echo $class . ':' . ($exists ? '✅' : '❌') . ' ';
    }
    echo '</p>';
    
    // 3. Check if user is logged in
    echo '<p><strong>User Status:</strong> ' . (is_user_logged_in() ? '✅ Logged in (ID: ' . get_current_user_id() . ')' : '❌ Not logged in') . '</p>';
    
    // 4. Check template file paths
    $template_paths = [
        'Main Template' => ABSPATH . 'wp-content/plugins/budgex/public/templates/budgex-app.php',
        'Enhanced Public' => ABSPATH . 'wp-content/plugins/budgex/public/partials/budgex-public-enhanced-budget-page.php',
        'Enhanced Admin' => ABSPATH . 'wp-content/plugins/budgex/public/partials/budgex-enhanced-budget-page.php'
    ];
    
    echo '<p><strong>Template Files:</strong> ';
    foreach ($template_paths as $name => $path) {
        echo $name . ':' . (file_exists($path) ? '✅' : '❌') . ' ';
    }
    echo '</p>';
    
    // 5. Try to create plugin instances and test methods
    if (class_exists('Budgex_Public')) {
        try {
            $public = new Budgex_Public();
            $method_exists = method_exists($public, 'display_single_budget_frontend');
            echo '<p><strong>Public Class:</strong> ✅ Created, display_single_budget_frontend: ' . ($method_exists ? '✅' : '❌') . '</p>';
            
            // Test with budget ID 7 if we're on that page
            $budget_id = get_query_var('budget_id');
            if ($budget_id) {
                ob_start();
                $result = $public->display_single_budget_frontend($budget_id);
                $output = ob_get_clean();
                
                echo '<p><strong>Method Test:</strong> Budget ID ' . $budget_id . ', Result length: ' . strlen($result) . ' chars</p>';
                if (strlen($result) < 100) {
                    echo '<p><strong>Method Output:</strong> "' . htmlspecialchars($result) . '"</p>';
                }
            }
            
        } catch (Exception $e) {
            echo '<p><strong>Public Class Error:</strong> ' . $e->getMessage() . '</p>';
        }
    }
    
    // 6. Check WordPress hooks and filters
    global $wp_filter;
    $budgex_hooks = [];
    foreach ($wp_filter as $hook_name => $hook) {
        if (strpos($hook_name, 'budgex') !== false) {
            $budgex_hooks[] = $hook_name;
        }
    }
    echo '<p><strong>Budgex Hooks:</strong> ' . (empty($budgex_hooks) ? 'None found' : implode(', ', $budgex_hooks)) . '</p>';
    
    // 7. Check for PHP errors in log
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<p><strong>Debug Mode:</strong> ✅ Enabled</p>';
    } else {
        echo '<p><strong>Debug Mode:</strong> ❌ Disabled (Enable WP_DEBUG to see errors)</p>';
    }
    
    echo '<p style="margin-top: 10px;"><button onclick="document.getElementById(\'budgex-debug-panel\').style.display=\'none\';">Close Debug Panel</button></p>';
    echo '</div>';
    
    // Also output to console
    echo '<script>
        console.log("🔍 Budgex Debug Info:", {
            url: "' . $_SERVER['REQUEST_URI'] . '",
            budgex_page: "' . get_query_var('budgex_page') . '",
            budget_id: "' . get_query_var('budget_id') . '",
            user_logged_in: ' . (is_user_logged_in() ? 'true' : 'false') . ',
            classes_loaded: ' . json_encode($classes_check) . '
        });
    </script>';
}

// Also add a direct test function that can be called via URL parameter
add_action('init', 'budgex_direct_test');

function budgex_direct_test() {
    if (isset($_GET['budgex_debug_test']) && current_user_can('manage_options')) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<h1>🔍 Budgex Direct Test</h1>';
        
        // Test the routing directly
        if (class_exists('Budgex')) {
            $budgex = new Budgex();
            
            // Simulate the budget page request
            set_query_var('budgex_page', 'budget');
            set_query_var('budget_id', '7');
            
            echo '<h2>Testing display_budgex_app() method:</h2>';
            try {
                $result = $budgex->display_budgex_app();
                echo '<p>Result length: ' . strlen($result) . ' characters</p>';
                if (strlen($result) > 0) {
                    echo '<div style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">';
                    echo '<h3>First 1000 characters:</h3>';
                    echo '<pre>' . htmlspecialchars(substr($result, 0, 1000)) . '</pre>';
                    echo '</div>';
                } else {
                    echo '<p style="color: red;">❌ Method returned empty result!</p>';
                }
            } catch (Exception $e) {
                echo '<p style="color: red;">❌ Error: ' . $e->getMessage() . '</p>';
            }
        } else {
            echo '<p style="color: red;">❌ Budgex class not found!</p>';
        }
        
        exit;
    }
}
?>
