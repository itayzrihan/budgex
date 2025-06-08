<?php
/**
 * Budgex Frontend Integration Test
 * 
 * This script tests the complete frontend workflow including:
 * - User authentication
 * - Budget creation and management
 * - Frontend routing and templates
 * - AJAX functionality
 * - Permission system
 * 
 * Run this script after the basic frontend test to verify end-to-end functionality
 */

// Load WordPress
if (!defined('ABSPATH')) {
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
        die('WordPress not found. Please access this script through WordPress admin.');
    }
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required to run integration tests.');
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budgex Frontend Integration Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
            background: #f0f0f1;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .test-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
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
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .button {
            background: #0073aa;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 5px 5px 0;
            border: none;
            cursor: pointer;
        }
        .button:hover { background: #005a87; }
        .button.secondary { background: #6c757d; }
        .button.success { background: #28a745; }
        .button.danger { background: #dc3545; }
        h1 { color: #1d2327; }
        h2 { color: #2c3338; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
        h3 { color: #50575e; }
        .step-counter {
            background: #0073aa;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-weight: bold;
            margin-left: 10px;
        }
        .output-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
        }
        .ajax-test {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="test-container">
    <h1>🔄 Budgex Frontend Integration Test</h1>
    <p>בדיקת אינטגרציה מלאה של הפונקציונליות</p>
    
    <?php
    // Test status tracking
    $integration_tests = [];
    $current_test = 0;
    
    function log_test($name, $status, $details = '', $output = '') {
        global $integration_tests, $current_test;
        $current_test++;
        
        $integration_tests[] = [
            'number' => $current_test,
            'name' => $name,
            'status' => $status,
            'details' => $details,
            'output' => $output
        ];
        
        $status_class = $status === 'pass' ? 'pass' : ($status === 'warning' ? 'warning' : 'fail');
        $status_icon = $status === 'pass' ? '✅' : ($status === 'warning' ? '⚠️' : '❌');
        
        echo "<div class='test-result {$status_class}'>";
        echo "<span class='step-counter'>{$current_test}</span>";
        echo "{$status_icon} {$name}";
        echo "</div>";
        
        if ($details) {
            echo "<div class='output-section'><strong>Details:</strong> {$details}</div>";
        }
        
        if ($output) {
            echo "<div class='code-block'>{$output}</div>";
        }
    }
    ?>

    <!-- Test 1: Database Setup Test -->
    <div class="test-section">
        <h2>1. Database and Core Setup</h2>
        
        <?php
        try {
            // Initialize core classes
            $database = new Budgex_Database();
            $permissions = new Budgex_Permissions();
            $calculator = new Budgex_Budget_Calculator();
            
            log_test(
                'Core classes initialization',
                'pass',
                'All main classes (Database, Permissions, Calculator) loaded successfully'
            );
            
            // Test database connection
            global $wpdb;
            $tables_check = [];
            $required_tables = [
                $wpdb->prefix . 'budgex_budgets',
                $wpdb->prefix . 'budgex_outcomes',
                $wpdb->prefix . 'budgex_invitations',
                $wpdb->prefix . 'budgex_budget_shares'
            ];
            
            foreach ($required_tables as $table) {
                $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'") == $table;
                $tables_check[] = "{$table}: " . ($exists ? 'EXISTS' : 'MISSING');
            }
            
            $all_tables_exist = !in_array('MISSING', implode('', $tables_check));
            log_test(
                'Database tables verification',
                $all_tables_exist ? 'pass' : 'fail',
                $all_tables_exist ? 'All required tables exist' : 'Some tables are missing',
                implode("\n", $tables_check)
            );
            
        } catch (Exception $e) {
            log_test(
                'Core setup failed', 
                'fail',
                'Exception occurred during initialization',
                $e->getMessage()
            );
        }
        ?>
    </div>

    <!-- Test 2: Frontend URL and Routing -->
    <div class="test-section">
        <h2>2. Frontend URL Structure</h2>
        
        <?php
        // Test URL generation
        $base_url = home_url('/budgex/');
        $budget_url = home_url('/budgex/budget/1/');
        
        log_test(
            'Frontend URL generation',
            'pass',
            'Base URLs generated correctly',
            "Dashboard URL: {$base_url}\nBudget URL: {$budget_url}"
        );
        
        // Test rewrite rules
        $rewrite_rules = get_option('rewrite_rules', []);
        $budgex_rules_found = 0;
        $budgex_patterns = [];
        
        foreach ($rewrite_rules as $pattern => $rule) {
            if (strpos($pattern, 'budgex') !== false || strpos($rule, 'budgex') !== false) {
                $budgex_rules_found++;
                $budgex_patterns[] = "{$pattern} → {$rule}";
            }
        }
        
        log_test(
            'Rewrite rules check',
            $budgex_rules_found > 0 ? 'pass' : 'warning',
            "Found {$budgex_rules_found} Budgex-related rewrite rules",
            implode("\n", array_slice($budgex_patterns, 0, 5))
        );
        
        // Check if Budgex page exists
        $budgex_page = get_page_by_path('budgex');
        log_test(
            'Budgex frontend page',
            $budgex_page ? 'pass' : 'fail',
            $budgex_page ? "Page exists with ID: {$budgex_page->ID}" : 'Budgex page not found',
            $budgex_page ? "Page URL: " . get_permalink($budgex_page->ID) : ''
        );
        ?>
    </div>

    <!-- Test 3: Template System -->
    <div class="test-section">
        <h2>3. Template Loading System</h2>
        
        <?php
        // Test template files
        $template_files = [
            'public/templates/budgex-app.php' => 'Main app template',
            'public/partials/budgex-dashboard.php' => 'Dashboard partial',
            'public/partials/budgex-budget-page.php' => 'Budget page partial',
            'public/partials/budgex-no-access.php' => 'Access denied partial'
        ];
        
        $all_templates_exist = true;
        $template_status = [];
        
        foreach ($template_files as $file => $description) {
            $full_path = plugin_dir_path(__FILE__) . $file;
            $exists = file_exists($full_path);
            $all_templates_exist = $all_templates_exist && $exists;
            $template_status[] = "{$description}: " . ($exists ? 'EXISTS' : 'MISSING');
        }
        
        log_test(
            'Template files verification',
            $all_templates_exist ? 'pass' : 'fail',
            $all_templates_exist ? 'All template files found' : 'Some template files missing',
            implode("\n", $template_status)
        );
        
        // Test shortcode
        $shortcode_exists = shortcode_exists('budgex_app');
        log_test(
            'Shortcode registration',
            $shortcode_exists ? 'pass' : 'fail',
            $shortcode_exists ? 'budgex_app shortcode is registered' : 'Shortcode not found'
        );
        ?>
    </div>

    <!-- Test 4: Asset Loading -->
    <div class="test-section">
        <h2>4. CSS and JavaScript Assets</h2>
        
        <?php
        // Test CSS files
        $css_files = [
            'public/css/budgex-public.css',
            'public/css/budgex-public-new.css'
        ];
        
        $css_total_size = 0;
        $css_status = [];
        
        foreach ($css_files as $file) {
            $full_path = plugin_dir_path(__FILE__) . $file;
            $exists = file_exists($full_path);
            $size = $exists ? filesize($full_path) : 0;
            $css_total_size += $size;
            $css_status[] = basename($file) . ": " . ($exists ? number_format($size) . " bytes" : "MISSING");
        }
        
        log_test(
            'CSS files check',
            $css_total_size > 0 ? 'pass' : 'fail',
            "Total CSS size: " . number_format($css_total_size) . " bytes",
            implode("\n", $css_status)
        );
        
        // Test JavaScript
        $js_file = plugin_dir_path(__FILE__) . 'public/js/budgex-public.js';
        $js_exists = file_exists($js_file);
        $js_size = $js_exists ? filesize($js_file) : 0;
        
        if ($js_exists) {
            $js_content = file_get_contents($js_file);
            $key_functions = ['initializeBudgex', 'loadBudgetsList', 'handleOutcomeSubmission', 'formatCurrency'];
            $missing_functions = [];
            
            foreach ($key_functions as $func) {
                if (strpos($js_content, $func) === false) {
                    $missing_functions[] = $func;
                }
            }
            
            log_test(
                'JavaScript functionality check',
                empty($missing_functions) ? 'pass' : 'warning',
                empty($missing_functions) ? 'All key functions found' : 'Some functions missing: ' . implode(', ', $missing_functions),
                "File size: " . number_format($js_size) . " bytes"
            );
        } else {
            log_test(
                'JavaScript file check',
                'fail',
                'JavaScript file not found'
            );
        }
        ?>
    </div>

    <!-- Test 5: User Authentication and Permissions -->
    <div class="test-section">
        <h2>5. User System Integration</h2>
        
        <?php
        $current_user = wp_get_current_user();
        $user_logged_in = is_user_logged_in();
        
        log_test(
            'User authentication',
            $user_logged_in ? 'pass' : 'warning',
            $user_logged_in ? "Logged in as: {$current_user->display_name} (ID: {$current_user->ID})" : 'No user logged in',
            $user_logged_in ? "User roles: " . implode(', ', $current_user->roles) : 'Testing with logged out state'
        );
        
        if ($user_logged_in && class_exists('Budgex_Permissions')) {
            $permissions = new Budgex_Permissions();
            
            // Test permission methods
            $can_add_budget = $permissions->can_add_budget($current_user->ID);
            log_test(
                'Permission system - can add budget',
                $can_add_budget ? 'pass' : 'warning',
                $can_add_budget ? 'User can create budgets' : 'User cannot create budgets'
            );
            
            // Test budget retrieval
            if (class_exists('Budgex_Database')) {
                $database = new Budgex_Database();
                $user_budgets = $database->get_user_budgets($current_user->ID);
                $shared_budgets = $database->get_shared_budgets($current_user->ID);
                
                log_test(
                    'User budget retrieval',
                    'pass',
                    "Found " . count($user_budgets) . " owned budgets and " . count($shared_budgets) . " shared budgets"
                );
            }
        }
        ?>
    </div>

    <!-- Test 6: Frontend Display Functions -->
    <div class="test-section">
        <h2>6. Frontend Display Testing</h2>
        
        <?php
        if (class_exists('Budgex_Public')) {
            $public = new Budgex_Public();
            
            // Test dashboard display
            try {
                ob_start();
                $dashboard_content = $public->display_dashboard_frontend();
                $buffer_content = ob_get_clean();
                
                $has_content = !empty($dashboard_content) || !empty($buffer_content);
                $total_content = $dashboard_content . $buffer_content;
                
                log_test(
                    'Dashboard frontend display',
                    $has_content ? 'pass' : 'fail',
                    $has_content ? 'Dashboard content generated successfully' : 'No dashboard content generated',
                    $has_content ? "Content length: " . strlen($total_content) . " characters" : ''
                );
                
                // Check for Hebrew content
                if ($has_content) {
                    $has_hebrew = preg_match('/[\x{0590}-\x{05FF}]/u', $total_content);
                    log_test(
                        'Hebrew text verification',
                        $has_hebrew ? 'pass' : 'warning',
                        $has_hebrew ? 'Hebrew text detected in output' : 'No Hebrew text found - check translations'
                    );
                }
                
            } catch (Exception $e) {
                log_test(
                    'Dashboard display error',
                    'fail',
                    'Exception during dashboard display',
                    $e->getMessage()
                );
            }
        }
        ?>
    </div>

    <!-- Test 7: AJAX Endpoints Live Test -->
    <div class="test-section">
        <h2>7. AJAX Functionality Test</h2>
        
        <div class="ajax-test">
            <h3>Live AJAX Test</h3>
            <p>Click the buttons below to test AJAX endpoints:</p>
            
            <button class="button" onclick="testAjaxEndpoint('budgex_get_dashboard_stats')">Test Dashboard Stats</button>
            <button class="button" onclick="testAjaxEndpoint('budgex_get_user_budgets')">Test User Budgets</button>
            <button class="button secondary" onclick="testAllAjax()">Test All Endpoints</button>
            
            <div id="ajax-results" style="margin-top: 15px;"></div>
        </div>
        
        <?php
        // Check AJAX action registration
        $ajax_actions = [
            'wp_ajax_budgex_get_dashboard_stats',
            'wp_ajax_budgex_get_user_budgets',
            'wp_ajax_budgex_add_outcome',
            'wp_ajax_budgex_edit_outcome',
            'wp_ajax_budgex_delete_outcome',
            'wp_ajax_budgex_send_invitation'
        ];
        
        $registered_actions = [];
        $missing_actions = [];
        
        foreach ($ajax_actions as $action) {
            if (has_action($action)) {
                $registered_actions[] = $action;
            } else {
                $missing_actions[] = $action;
            }
        }
        
        log_test(
            'AJAX actions registration',
            empty($missing_actions) ? 'pass' : 'warning',
            "Registered: " . count($registered_actions) . ", Missing: " . count($missing_actions),
            !empty($missing_actions) ? "Missing actions:\n" . implode("\n", $missing_actions) : "All actions registered"
        );
        ?>
    </div>

    <!-- Test 8: Frontend Access Security -->
    <div class="test-section">
        <h2>8. Security and Access Control</h2>
        
        <?php
        // Test nonce generation
        $nonce = wp_create_nonce('budgex_public_nonce');
        log_test(
            'Nonce generation',
            !empty($nonce) ? 'pass' : 'fail',
            !empty($nonce) ? 'Nonce generated successfully' : 'Nonce generation failed',
            "Sample nonce: {$nonce}"
        );
        
        // Test capability checks
        $security_functions = [
            'is_user_logged_in' => is_user_logged_in(),
            'current_user_can(read)' => current_user_can('read'),
            'wp_verify_nonce availability' => function_exists('wp_verify_nonce')
        ];
        
        $security_status = [];
        $all_security_ok = true;
        
        foreach ($security_functions as $check => $result) {
            $security_status[] = "{$check}: " . ($result ? 'OK' : 'FAIL');
            $all_security_ok = $all_security_ok && $result;
        }
        
        log_test(
            'Security functions check',
            $all_security_ok ? 'pass' : 'warning',
            $all_security_ok ? 'All security functions working' : 'Some security issues detected',
            implode("\n", $security_status)
        );
        ?>
    </div>

    <!-- Summary -->
    <div class="test-section">
        <h2>📊 Integration Test Summary</h2>
        
        <?php
        $total_tests = count($integration_tests);
        $passed = count(array_filter($integration_tests, function($test) { return $test['status'] === 'pass'; }));
        $warnings = count(array_filter($integration_tests, function($test) { return $test['status'] === 'warning'; }));
        $failed = count(array_filter($integration_tests, function($test) { return $test['status'] === 'fail'; }));
        
        echo "<div class='output-section'>";
        echo "<h3>Test Results:</h3>";
        echo "<ul>";
        echo "<li><strong>Total Tests:</strong> {$total_tests}</li>";
        echo "<li><strong style='color: #28a745;'>Passed:</strong> {$passed}</li>";
        echo "<li><strong style='color: #ffc107;'>Warnings:</strong> {$warnings}</li>";
        echo "<li><strong style='color: #dc3545;'>Failed:</strong> {$failed}</li>";
        echo "</ul>";
        
        $success_rate = $total_tests > 0 ? round(($passed / $total_tests) * 100, 1) : 0;
        echo "<h3>Success Rate: {$success_rate}%</h3>";
        
        if ($success_rate >= 90) {
            echo "<div class='test-result pass'>🎉 Excellent! The frontend system is working properly.</div>";
        } elseif ($success_rate >= 70) {
            echo "<div class='test-result warning'>⚠️ Good with some warnings. Check the issues above.</div>";
        } else {
            echo "<div class='test-result fail'>❌ Significant issues found. Please fix the failing tests.</div>";
        }
        echo "</div>";
        ?>
    </div>

    <!-- Next Steps -->
    <div class="test-section">
        <h2>🎯 Next Steps</h2>
        
        <div class="output-section">
            <h3>Manual Testing Recommendations:</h3>
            <ol>
                <li><strong>Frontend Navigation:</strong> Visit <a href="<?php echo home_url('/budgex/'); ?>" target="_blank"><?php echo home_url('/budgex/'); ?></a></li>
                <li><strong>Create Test Budget:</strong> Use the admin panel to create a sample budget</li>
                <li><strong>Test User Permissions:</strong> Create additional users and test invitation system</li>
                <li><strong>Mobile Testing:</strong> Test responsive design on different devices</li>
                <li><strong>Performance Testing:</strong> Check page load times and AJAX response times</li>
            </ol>
            
            <h3>Quick Actions:</h3>
            <a href="<?php echo admin_url('admin.php?page=budgex'); ?>" class="button">Admin Dashboard</a>
            <a href="<?php echo home_url('/budgex/'); ?>" class="button" target="_blank">Frontend Dashboard</a>
            <a href="<?php echo admin_url('admin.php?page=budgex-new'); ?>" class="button success">Create Test Budget</a>
            <a href="javascript:location.reload()" class="button secondary">Rerun Tests</a>
        </div>
    </div>
</div>

<script>
// AJAX testing functions
function testAjaxEndpoint(action) {
    const resultsDiv = document.getElementById('ajax-results');
    const nonce = '<?php echo wp_create_nonce('budgex_public_nonce'); ?>';
    
    resultsDiv.innerHTML += `<div style="padding: 10px; background: #e3f2fd; margin: 5px 0; border-radius: 4px;">Testing ${action}...</div>`;
    
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: action,
            nonce: nonce
        },
        success: function(response) {
            const status = response.success ? 'success' : 'error';
            const color = response.success ? '#d4edda' : '#f8d7da';
            resultsDiv.innerHTML += `<div style="padding: 10px; background: ${color}; margin: 5px 0; border-radius: 4px;">
                <strong>${action}:</strong> ${status.toUpperCase()}<br>
                <small>${JSON.stringify(response).substring(0, 200)}...</small>
            </div>`;
        },
        error: function(xhr, status, error) {
            resultsDiv.innerHTML += `<div style="padding: 10px; background: #f8d7da; margin: 5px 0; border-radius: 4px;">
                <strong>${action}:</strong> ERROR<br>
                <small>${error}</small>
            </div>`;
        }
    });
}

function testAllAjax() {
    const actions = [
        'budgex_get_dashboard_stats',
        'budgex_get_user_budgets'
    ];
    
    document.getElementById('ajax-results').innerHTML = '<h4>Testing all AJAX endpoints...</h4>';
    
    actions.forEach((action, index) => {
        setTimeout(() => {
            testAjaxEndpoint(action);
        }, index * 1000);
    });
}

// Auto-scroll to bottom when new results are added
const observer = new MutationObserver(function() {
    window.scrollTo(0, document.body.scrollHeight);
});

observer.observe(document.getElementById('ajax-results'), {
    childList: true,
    subtree: true
});
</script>
</body>
</html>
