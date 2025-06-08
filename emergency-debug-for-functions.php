<?php
/**
 * Emergency Debug Tool for Budgex Empty Page Issue
 * 
 * DEPLOYMENT INSTRUCTIONS:
 * 1. Copy this entire code
 * 2. Go to WordPress Admin > Appearance > Theme Editor
 * 3. Open functions.php of your active theme
 * 4. Paste this code at the END of the file (before the closing ?>)
 * 5. Save the file
 * 6. Visit the problematic page: https://mytx.one/budgex/budget/7/
 * 7. Look for the black debug panel at the bottom of the page
 * 8. For direct testing, visit: https://mytx.one/?budgex_debug_test=1 (admin only)
 */

// Emergency Debug Panel - Shows on all budgex pages
add_action('wp_footer', function() {
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
        max-height: 300px;
        overflow-y: auto;
        border-top: 3px solid #ff0000;
    ">';
    
    echo '<h4 style="margin: 0 0 10px 0; color: #ff6b6b;">🔍 Budgex Debug Panel - ' . date('Y-m-d H:i:s') . '</h4>';
    
    // 1. URL and Query Analysis
    echo '<div style="margin-bottom: 10px;">';
    echo '<strong>URL:</strong> ' . $_SERVER['REQUEST_URI'] . '<br>';
    echo '<strong>Query Vars:</strong> budgex_page=' . get_query_var('budgex_page') . ', budget_id=' . get_query_var('budget_id') . '<br>';
    echo '<strong>HTTP Method:</strong> ' . $_SERVER['REQUEST_METHOD'] . '<br>';
    echo '</div>';
    
    // 2. Plugin Class Existence
    $classes = [
        'Budgex' => class_exists('Budgex'),
        'Budgex_Public' => class_exists('Budgex_Public'),
        'Budgex_Database' => class_exists('Budgex_Database')
    ];
    
    echo '<div style="margin-bottom: 10px;">';
    echo '<strong>Plugin Classes:</strong> ';
    foreach ($classes as $class => $exists) {
        $icon = $exists ? '✅' : '❌';
        echo "{$class}:{$icon} ";
    }
    echo '</div>';
    
    // 3. User Status
    echo '<div style="margin-bottom: 10px;">';
    if (is_user_logged_in()) {
        echo '<strong>User:</strong> ✅ Logged in (ID: ' . get_current_user_id() . ')';
    } else {
        echo '<strong>User:</strong> ❌ Not logged in';
    }
    echo '</div>';
    
    // 4. Template File Check
    $plugin_path = WP_PLUGIN_DIR . '/budgex/';
    $templates = [
        'Main App' => $plugin_path . 'public/templates/budgex-app.php',
        'Enhanced Public' => $plugin_path . 'public/partials/budgex-public-enhanced-budget-page.php',
        'Enhanced Admin' => $plugin_path . 'public/partials/budgex-enhanced-budget-page.php'
    ];
    
    echo '<div style="margin-bottom: 10px;">';
    echo '<strong>Templates:</strong> ';
    foreach ($templates as $name => $path) {
        $exists = file_exists($path);
        $icon = $exists ? '✅' : '❌';
        echo "{$name}:{$icon} ";
    }
    echo '</div>';
    
    // 5. Active Plugin Check
    $active_plugins = get_option('active_plugins', []);
    $budgex_active = false;
    foreach ($active_plugins as $plugin) {
        if (strpos($plugin, 'budgex') !== false) {
            $budgex_active = true;
            echo '<div style="margin-bottom: 10px;">';
            echo '<strong>Plugin Status:</strong> ✅ Active (' . $plugin . ')';
            echo '</div>';
            break;
        }
    }
    
    if (!$budgex_active) {
        echo '<div style="margin-bottom: 10px; color: #ff6b6b;">';
        echo '<strong>Plugin Status:</strong> ❌ Not found in active plugins!';
        echo '</div>';
    }
    
    // 6. Test Method Execution
    if (class_exists('Budgex_Public')) {
        echo '<div style="margin-bottom: 10px;">';
        try {
            $public = new Budgex_Public();
            $budget_id = get_query_var('budget_id') ?: '7';
            
            if (method_exists($public, 'display_single_budget_frontend')) {
                ob_start();
                $result = $public->display_single_budget_frontend($budget_id);
                $captured_output = ob_get_clean();
                
                echo '<strong>Method Test:</strong> ';
                if (strlen($result) > 0) {
                    echo '✅ Success (' . strlen($result) . ' chars)';
                } else {
                    echo '❌ Empty result';
                }
                
                if ($captured_output) {
                    echo ' | Captured: ' . strlen($captured_output) . ' chars';
                }
            } else {
                echo '<strong>Method Test:</strong> ❌ display_single_budget_frontend not found';
            }
        } catch (Exception $e) {
            echo '<strong>Method Test:</strong> ❌ Error: ' . $e->getMessage();
        }
        echo '</div>';
    }
    
    // 7. WordPress Errors
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<div style="margin-bottom: 10px;">';
        echo '<strong>Debug Mode:</strong> ✅ Enabled';
        echo '</div>';
    } else {
        echo '<div style="margin-bottom: 10px; color: #ffa500;">';
        echo '<strong>Debug Mode:</strong> ⚠️ Disabled - Enable WP_DEBUG in wp-config.php';
        echo '</div>';
    }
    
    echo '<button onclick="document.getElementById(\'budgex-debug-panel\').style.display=\'none\';" style="background: #ff6b6b; color: white; border: none; padding: 5px 10px; cursor: pointer;">Close</button>';
    echo '</div>';
});

// Direct Test Function (Admin Only)
add_action('init', function() {
    if (isset($_GET['budgex_debug_test']) && current_user_can('manage_options')) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><title>Budgex Debug Test</title></head><body>';
        echo '<h1>🔍 Budgex Direct Debug Test</h1>';
        echo '<p>Time: ' . date('Y-m-d H:i:s') . '</p>';
        
        // Check if main class exists and test routing
        if (class_exists('Budgex')) {
            echo '<h2>✅ Budgex class found</h2>';
            
            try {
                $budgex = new Budgex();
                echo '<p>✅ Budgex instance created</p>';
                
                // Set up test environment
                set_query_var('budgex_page', 'budget');
                set_query_var('budget_id', '7');
                
                if (method_exists($budgex, 'display_budgex_app')) {
                    echo '<h3>Testing display_budgex_app():</h3>';
                    
                    ob_start();
                    $result = $budgex->display_budgex_app();
                    $captured = ob_get_clean();
                    
                    echo '<p><strong>Returned:</strong> ' . strlen($result) . ' characters</p>';
                    echo '<p><strong>Captured:</strong> ' . strlen($captured) . ' characters</p>';
                    
                    if ($result) {
                        echo '<h4>First 500 characters of result:</h4>';
                        echo '<pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd;">';
                        echo htmlspecialchars(substr($result, 0, 500));
                        echo '</pre>';
                    }
                    
                    if ($captured) {
                        echo '<h4>Captured output:</h4>';
                        echo '<pre style="background: #fff3cd; padding: 10px; border: 1px solid #ffc107;">';
                        echo htmlspecialchars($captured);
                        echo '</pre>';
                    }
                } else {
                    echo '<p>❌ display_budgex_app method not found</p>';
                }
                
            } catch (Exception $e) {
                echo '<p style="color: red;">❌ Error: ' . $e->getMessage() . '</p>';
                echo '<p><strong>Stack trace:</strong></p>';
                echo '<pre style="background: #f8d7da; padding: 10px; border: 1px solid #dc3545;">';
                echo $e->getTraceAsString();
                echo '</pre>';
            }
        } else {
            echo '<h2>❌ Budgex class not found</h2>';
            echo '<p>Active plugins:</p>';
            echo '<ul>';
            foreach (get_option('active_plugins', []) as $plugin) {
                echo '<li>' . $plugin . '</li>';
            }
            echo '</ul>';
        }
        
        echo '</body></html>';
        exit;
    }
});
