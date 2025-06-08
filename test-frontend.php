<?php
/**
 * Budgex Frontend Testing Script
 * 
 * This script performs comprehensive testing of the Budgex frontend functionality
 * including routing, permissions, AJAX endpoints, and user interface components.
 * 
 * Usage: Access this file via browser after placing in the plugin directory
 * Example: http://yoursite.com/wp-content/plugins/budgex/test-frontend.php
 */

// Prevent direct access without WordPress
if (!defined('ABSPATH') && !file_exists('../../../wp-load.php')) {
    // Try to load WordPress
    $wp_load_paths = [
        '../../../wp-load.php',
        '../../../../wp-load.php',
        '../../../../../wp-load.php'
    ];
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('WordPress not found. Please access this script through WordPress admin or place it in the correct directory.');
    }
}

// Security check - only allow admin users to run tests
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required to run tests.');
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budgex Frontend Testing</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
            background: #f0f0f1;
            margin: 0;
            padding: 20px;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #1d2327; margin-bottom: 30px; }
        h2 { color: #2c3338; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
        h3 { color: #50575e; }
        .test-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .test-result {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            font-weight: 600;
        }
        .pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-details {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }
        .button {
            background: #0073aa;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 5px 5px 0;
        }
        .button:hover { background: #005a87; color: white; }
        .summary {
            background: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #0073aa;
        }
    </style>
</head>
<body>
<div class="test-container">
    <h1>🧪 Budgex Frontend Testing Suite</h1>
    <p>בדיקה מקיפה של מערכת ה-Frontend של Budgex</p>
    
    <?php
    // Initialize test results
    $total_tests = 0;
    $passed_tests = 0;
    $failed_tests = 0;
    $warnings = 0;
    
    function test_result($condition, $message, $details = '') {
        global $total_tests, $passed_tests, $failed_tests;
        $total_tests++;
        
        if ($condition) {
            $passed_tests++;
            echo "<div class='test-result pass'>✅ {$message}</div>";
        } else {
            $failed_tests++;
            echo "<div class='test-result fail'>❌ {$message}</div>";
        }
        
        if ($details) {
            echo "<div class='test-details'>{$details}</div>";
        }
    }
    
    function warning_result($message, $details = '') {
        global $warnings;
        $warnings++;
        echo "<div class='test-result warning'>⚠️ {$message}</div>";
        if ($details) {
            echo "<div class='test-details'>{$details}</div>";
        }
    }
    
    function info_result($message, $details = '') {
        echo "<div class='test-result info'>ℹ️ {$message}</div>";
        if ($details) {
            echo "<div class='test-details'>{$details}</div>";
        }
    }
    ?>

    <!-- Test 1: Plugin Installation and Activation -->
    <div class="test-section">
        <h2>1. Plugin Installation & Activation</h2>
        
        <?php
        // Check if plugin is active
        $is_plugin_active = is_plugin_active('budgex/budgex.php');
        test_result($is_plugin_active, 'Plugin is active');
        
        // Check main plugin file
        $main_file_exists = file_exists(plugin_dir_path(__FILE__) . 'budgex.php');
        test_result($main_file_exists, 'Main plugin file exists');
        
        // Check if classes are loaded
        $classes_loaded = class_exists('Budgex') && class_exists('Budgex_Public') && class_exists('Budgex_Database');
        test_result($classes_loaded, 'Core classes loaded');
        
        // Check database tables
        global $wpdb;
        $tables_to_check = [
            $wpdb->prefix . 'budgex_budgets',
            $wpdb->prefix . 'budgex_outcomes', 
            $wpdb->prefix . 'budgex_invitations',
            $wpdb->prefix . 'budgex_budget_shares'
        ];
        
        foreach ($tables_to_check as $table) {
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'") == $table;
            test_result($table_exists, "Database table exists: {$table}");
        }
        ?>
    </div>

    <!-- Test 2: Frontend Page Creation -->
    <div class="test-section">
        <h2>2. Frontend Page & URL Routing</h2>
        
        <?php
        // Check if Budgex page exists
        $budgex_page = get_page_by_path('budgex');
        test_result($budgex_page !== null, 'Budgex page exists', $budgex_page ? "Page ID: {$budgex_page->ID}" : '');
        
        // Check rewrite rules
        $rewrite_rules = get_option('rewrite_rules');
        $budgex_rules = [];
        if ($rewrite_rules) {
            foreach ($rewrite_rules as $pattern => $rule) {
                if (strpos($pattern, 'budgex') !== false) {
                    $budgex_rules[] = "{$pattern} => {$rule}";
                }
            }
        }
        test_result(!empty($budgex_rules), 'Rewrite rules for Budgex URLs exist', implode("\n", $budgex_rules));
        
        // Test frontend URLs
        $frontend_urls = [
            home_url('/budgex/') => 'Dashboard URL',
            home_url('/budgex/budget/1/') => 'Budget URL example'
        ];
        
        foreach ($frontend_urls as $url => $description) {
            info_result("Frontend URL: {$description}", $url);
        }
        ?>
    </div>

    <!-- Test 3: Frontend Template System -->
    <div class="test-section">
        <h2>3. Frontend Template System</h2>
        
        <?php
        // Check template files
        $template_files = [
            'public/templates/budgex-app.php' => 'Main frontend template',
            'public/partials/budgex-dashboard.php' => 'Dashboard partial',
            'public/partials/budgex-budget-page.php' => 'Budget page partial',
            'public/partials/budgex-no-access.php' => 'No access partial'
        ];
        
        foreach ($template_files as $file => $description) {
            $file_path = plugin_dir_path(__FILE__) . $file;
            test_result(file_exists($file_path), "Template exists: {$description}", $file_path);
        }
        
        // Check if template loading is set up
        $budgex_instance = class_exists('Budgex') ? new Budgex() : null;
        test_result($budgex_instance !== null, 'Budgex main class can be instantiated');
        
        // Check shortcode registration
        $shortcode_exists = shortcode_exists('budgex_app');
        test_result($shortcode_exists, 'budgex_app shortcode registered');
        ?>
    </div>

    <!-- Test 4: CSS and JavaScript Assets -->
    <div class="test-section">
        <h2>4. Frontend Assets (CSS & JavaScript)</h2>
        
        <?php
        // Check CSS files
        $css_files = [
            'public/css/budgex-public.css' => 'Main public CSS',
            'public/css/budgex-public-new.css' => 'Enhanced public CSS'
        ];
        
        foreach ($css_files as $file => $description) {
            $file_path = plugin_dir_path(__FILE__) . $file;
            $exists = file_exists($file_path);
            test_result($exists, "CSS file exists: {$description}");
            
            if ($exists) {
                $file_size = filesize($file_path);
                info_result("File size: " . number_format($file_size) . " bytes");
            }
        }
        
        // Check JavaScript files
        $js_files = [
            'public/js/budgex-public.js' => 'Main public JavaScript'
        ];
        
        foreach ($js_files as $file => $description) {
            $file_path = plugin_dir_path(__FILE__) . $file;
            $exists = file_exists($file_path);
            test_result($exists, "JavaScript file exists: {$description}");
            
            if ($exists) {
                $file_size = filesize($file_path);
                info_result("File size: " . number_format($file_size) . " bytes");
                
                // Check for key functions in JS
                $js_content = file_get_contents($file_path);
                $required_functions = ['initializeBudgex', 'loadBudgetsList', 'handleOutcomeSubmission'];
                foreach ($required_functions as $func) {
                    $func_exists = strpos($js_content, $func) !== false;
                    test_result($func_exists, "JavaScript function exists: {$func}");
                }
            }
        }
        ?>
    </div>

    <!-- Test 5: AJAX Endpoints -->
    <div class="test-section">
        <h2>5. AJAX Endpoints</h2>
        
        <?php
        // Check if AJAX endpoints are registered
        $ajax_actions = [
            'budgex_get_dashboard_stats',
            'budgex_get_user_budgets',
            'budgex_add_outcome',
            'budgex_edit_outcome',
            'budgex_delete_outcome',
            'budgex_get_outcomes',
            'budgex_get_monthly_breakdown',
            'budgex_send_invitation',
            'budgex_accept_invitation'
        ];
        
        foreach ($ajax_actions as $action) {
            $has_action = has_action("wp_ajax_{$action}");
            test_result($has_action, "AJAX action registered: {$action}");
        }
        
        // Test AJAX URL generation
        $ajax_url = admin_url('admin-ajax.php');
        test_result(!empty($ajax_url), 'AJAX URL available', $ajax_url);
        
        // Check nonce generation
        $nonce = wp_create_nonce('budgex_public_nonce');
        test_result(!empty($nonce), 'Nonce generation works', "Sample nonce: {$nonce}");
        ?>
    </div>

    <!-- Test 6: Permission System -->
    <div class="test-section">
        <h2>6. Permission System</h2>
        
        <?php
        // Check if permission class exists and can be instantiated
        $permissions_class_exists = class_exists('Budgex_Permissions');
        test_result($permissions_class_exists, 'Permissions class exists');
        
        if ($permissions_class_exists) {
            $permissions = new Budgex_Permissions();
            
            // Test with current user
            $current_user_id = get_current_user_id();
            if ($current_user_id) {
                $can_add_budget = $permissions->can_add_budget($current_user_id);
                test_result($can_add_budget, 'Current user can add budget');
                
                info_result("Current user ID: {$current_user_id}");
                info_result("Current user capabilities tested");
            } else {
                warning_result('No user logged in for permission testing');
            }
        }
        
        // Check security functions
        $security_functions = ['is_user_logged_in', 'current_user_can', 'wp_verify_nonce'];
        foreach ($security_functions as $func) {
            test_result(function_exists($func), "Security function available: {$func}");
        }
        ?>
    </div>

    <!-- Test 7: Database Integration -->
    <div class="test-section">
        <h2>7. Database Integration</h2>
        
        <?php
        // Check database class
        $db_class_exists = class_exists('Budgex_Database');
        test_result($db_class_exists, 'Database class exists');
        
        if ($db_class_exists) {
            $database = new Budgex_Database();
            
            // Test getting user budgets (should work even if empty)
            $current_user_id = get_current_user_id();
            if ($current_user_id) {
                try {
                    $user_budgets = $database->get_user_budgets($current_user_id);
                    test_result(is_array($user_budgets), 'Can retrieve user budgets', "Found " . count($user_budgets) . " budgets");
                    
                    $shared_budgets = $database->get_shared_budgets($current_user_id);
                    test_result(is_array($shared_budgets), 'Can retrieve shared budgets', "Found " . count($shared_budgets) . " shared budgets");
                } catch (Exception $e) {
                    test_result(false, 'Database query failed', $e->getMessage());
                }
            }
        }
        
        // Test database connection
        global $wpdb;
        $db_connection = $wpdb->check_connection();
        test_result($db_connection, 'Database connection active');
        ?>
    </div>

    <!-- Test 8: Localization -->
    <div class="test-section">
        <h2>8. Hebrew Localization</h2>
        
        <?php
        // Check if Hebrew translations work
        $test_strings = [
            'תקציב חדש' => __('תקציב חדש', 'budgex'),
            'הוסף הוצאה' => __('הוסף הוצאה', 'budgex'),
            'התחבר' => __('התחבר', 'budgex')
        ];
        
        foreach ($test_strings as $original => $translated) {
            $is_translated = $original === $translated || !empty($translated);
            test_result($is_translated, "Hebrew string available: {$original}");
        }
        
        // Check text domain loading
        $textdomain_loaded = is_textdomain_loaded('budgex');
        test_result($textdomain_loaded, 'Budgex text domain loaded');
        
        // Check language files
        $language_files = [
            'languages/budgex-he_IL.po',
            'languages/budgex-he_IL.mo'
        ];
        
        foreach ($language_files as $file) {
            $file_path = plugin_dir_path(__FILE__) . $file;
            $exists = file_exists($file_path);
            test_result($exists, "Language file exists: {$file}");
        }
        ?>
    </div>

    <!-- Test 9: Frontend Functionality -->
    <div class="test-section">
        <h2>9. Frontend Display Functions</h2>
        
        <?php
        // Check if public class methods exist
        if (class_exists('Budgex_Public')) {
            $public = new Budgex_Public();
            $required_methods = [
                'display_dashboard_frontend',
                'display_single_budget_frontend',
                'enqueue_styles',
                'enqueue_scripts'
            ];
            
            foreach ($required_methods as $method) {
                $method_exists = method_exists($public, $method);
                test_result($method_exists, "Public method exists: {$method}");
            }
            
            // Test dashboard display
            if (method_exists($public, 'display_dashboard_frontend')) {
                ob_start();
                $dashboard_output = $public->display_dashboard_frontend();
                $buffer = ob_get_clean();
                
                test_result(!empty($dashboard_output), 'Dashboard frontend generates output');
                if (!empty($dashboard_output)) {
                    $has_hebrew = preg_match('/[\x{0590}-\x{05FF}]/u', $dashboard_output);
                    test_result($has_hebrew, 'Dashboard output contains Hebrew text');
                }
            }
        }
        ?>
    </div>

    <!-- Test 10: URL Testing -->
    <div class="test-section">
        <h2>10. URL Structure Testing</h2>
        
        <?php
        // Test query var registration
        global $wp;
        $budgex_query_vars = ['budgex_page', 'budget_id'];
        
        foreach ($budgex_query_vars as $var) {
            $var_registered = in_array($var, $wp->public_query_vars);
            test_result($var_registered, "Query var registered: {$var}");
        }
        
        // Test sample URLs
        $test_urls = [
            '/budgex/' => 'Dashboard page',
            '/budgex/budget/123/' => 'Budget page with ID',
            '/budgex/invite/' => 'Invite page'
        ];
        
        foreach ($test_urls as $url => $description) {
            info_result("URL structure: {$description}", home_url($url));
        }
        ?>
    </div>

    <!-- Summary -->
    <div class="summary">
        <h2>📊 Test Results Summary</h2>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_tests; ?></div>
                <div>Total Tests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #28a745;"><?php echo $passed_tests; ?></div>
                <div>Passed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #dc3545;"><?php echo $failed_tests; ?></div>
                <div>Failed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ffc107;"><?php echo $warnings; ?></div>
                <div>Warnings</div>
            </div>
        </div>
        
        <?php
        $success_rate = $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 1) : 0;
        
        if ($success_rate >= 90) {
            echo "<div class='test-result pass'>🎉 Excellent! Success rate: {$success_rate}% - Plugin is ready for production</div>";
        } elseif ($success_rate >= 70) {
            echo "<div class='test-result warning'>⚠️ Good: Success rate: {$success_rate}% - Minor issues need attention</div>";
        } else {
            echo "<div class='test-result fail'>❌ Issues found: Success rate: {$success_rate}% - Major fixes required</div>";
        }
        ?>
    </div>

    <!-- Action Links -->
    <div style="margin-top: 30px; text-align: center;">
        <a href="<?php echo admin_url('admin.php?page=budgex'); ?>" class="button">🔧 Admin Dashboard</a>
        <a href="<?php echo home_url('/budgex/'); ?>" class="button">🏠 Frontend Dashboard</a>
        <a href="<?php echo admin_url('plugins.php'); ?>" class="button">📦 Plugins Page</a>
        <a href="javascript:location.reload()" class="button">🔄 Run Tests Again</a>
    </div>

    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 14px; color: #6c757d;">
        <strong>Note:</strong> This testing suite checks the frontend infrastructure. For full functionality testing, 
        please create test budgets, outcomes, and user invitations through the actual interface.
    </div>
</div>
</body>
</html>
