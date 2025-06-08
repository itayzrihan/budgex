<?php
/**
 * Frontend Access Integration Test
 * 
 * This script tests the complete frontend access system for Budgex
 * including URL routing, template loading, and permission checks.
 */

// Determine WordPress root path
$wp_root_path = dirname(dirname(dirname(__FILE__)));
if (file_exists($wp_root_path . '/wp-config.php')) {
    require_once $wp_root_path . '/wp-config.php';
} else {
    die('WordPress configuration file not found');
}

// Ensure WordPress is loaded
if (!function_exists('add_action')) {
    die('WordPress not properly loaded');
}

// Set proper headers
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgex Frontend Access Test</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            direction: rtl;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(45deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        .content {
            padding: 30px;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .test-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #007cba;
        }
        .test-card.success { border-left-color: #28a745; }
        .test-card.error { border-left-color: #dc3545; }
        .test-card.warning { border-left-color: #ffc107; }
        .test-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status.success { background: #28a745; }
        .status.error { background: #dc3545; }
        .status.warning { background: #ffc107; color: #333; }
        .status.info { background: #17a2b8; }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            transition: background 0.3s;
        }
        .button:hover { background: #005a87; }
        .button.success { background: #28a745; }
        .button.secondary { background: #6c757d; }
        .code {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        .results-summary {
            background: #e8f4fd;
            border: 2px solid #007cba;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .url-test {
            background: #f1f3f4;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🚀 Budgex Frontend Access Test</h1>
        <p>בדיקה מקיפה של מערכת הגישה הקדמית</p>
    </div>

    <div class="content">
        <?php
        // Initialize test results
        $total_tests = 0;
        $passed_tests = 0;
        $failed_tests = 0;
        $warnings = 0;

        function run_test($test_name, $test_function, $expected_result = true) {
            global $total_tests, $passed_tests, $failed_tests, $warnings;
            $total_tests++;
            
            try {
                $result = $test_function();
                if ($result === $expected_result) {
                    $passed_tests++;
                    return ['status' => 'success', 'message' => 'הצלחה'];
                } elseif ($result === 'warning') {
                    $warnings++;
                    return ['status' => 'warning', 'message' => 'דורש תשומת לב'];
                } else {
                    $failed_tests++;
                    return ['status' => 'error', 'message' => 'נכשל'];
                }
            } catch (Exception $e) {
                $failed_tests++;
                return ['status' => 'error', 'message' => 'שגיאה: ' . $e->getMessage()];
            }
        }

        // Test 1: Core Classes
        echo '<div class="test-grid">';
        
        echo '<div class="test-card">';
        echo '<h3>🔧 בדיקת מחלקות יסוד</h3>';
        
        $tests = [
            'Budgex Class' => function() { return class_exists('Budgex'); },
            'Budgex_Public Class' => function() { return class_exists('Budgex_Public'); },
            'Budgex_Database Class' => function() { return class_exists('Budgex_Database'); },
            'Budgex_Permissions Class' => function() { return class_exists('Budgex_Permissions'); },
        ];
        
        foreach ($tests as $test_name => $test_func) {
            $result = run_test($test_name, $test_func);
            echo "<div style='margin: 8px 0;'>";
            echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
            echo "<strong>{$test_name}</strong>";
            echo "</div>";
        }
        echo '</div>';

        // Test 2: Frontend Methods
        echo '<div class="test-card">';
        echo '<h3>🎯 בדיקת מתודות קדמיות</h3>';
        
        if (class_exists('Budgex_Public')) {
            $public = new Budgex_Public();
            $frontend_tests = [
                'display_dashboard_frontend' => function() use ($public) { 
                    return method_exists($public, 'display_dashboard_frontend'); 
                },
                'display_single_budget_frontend' => function() use ($public) { 
                    return method_exists($public, 'display_single_budget_frontend'); 
                },
                'render_dashboard' => function() use ($public) { 
                    return method_exists($public, 'render_dashboard'); 
                },
                'render_budget_page' => function() use ($public) { 
                    return method_exists($public, 'render_budget_page'); 
                },
            ];
            
            foreach ($frontend_tests as $test_name => $test_func) {
                $result = run_test($test_name, $test_func);
                echo "<div style='margin: 8px 0;'>";
                echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
                echo "<strong>{$test_name}</strong>";
                echo "</div>";
            }
        } else {
            echo "<div style='margin: 8px 0;'>";
            echo "<span class='status error'>שגיאה</span> ";
            echo "<strong>Budgex_Public Class not found</strong>";
            echo "</div>";
            $failed_tests++;
            $total_tests++;
        }
        echo '</div>';

        // Test 3: WordPress Integration
        echo '<div class="test-card">';
        echo '<h3>🔗 בדיקת אינטגרציה עם וורדפרס</h3>';
        
        $wp_tests = [
            'Shortcode [budgex_app]' => function() { 
                return shortcode_exists('budgex_app'); 
            },
            'Frontend Page Exists' => function() { 
                $page = get_page_by_path('budgex');
                return !empty($page);
            },
            'Rewrite Rules' => function() { 
                global $wp_rewrite;
                return !empty($wp_rewrite->rules) ? true : 'warning';
            },
            'Query Vars' => function() {
                global $wp;
                return in_array('budgex_page', $wp->public_query_vars ?? []) ? true : 'warning';
            },
        ];
        
        foreach ($wp_tests as $test_name => $test_func) {
            $result = run_test($test_name, $test_func);
            echo "<div style='margin: 8px 0;'>";
            echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
            echo "<strong>{$test_name}</strong>";
            echo "</div>";
        }
        echo '</div>';

        // Test 4: File Structure
        echo '<div class="test-card">';
        echo '<h3>📁 בדיקת מבנה קבצים</h3>';
        
        $required_files = [
            'Template File' => BUDGEX_DIR . 'public/templates/budgex-app.php',
            'Dashboard Partial' => BUDGEX_DIR . 'public/partials/budgex-dashboard.php',
            'Budget Page Partial' => BUDGEX_DIR . 'public/partials/budgex-budget-page.php',
            'Public CSS' => BUDGEX_DIR . 'public/css/budgex-public.css',
            'Public JS' => BUDGEX_DIR . 'public/js/budgex-public.js',
        ];
        
        foreach ($required_files as $file_name => $file_path) {
            $exists = file_exists($file_path);
            $result = run_test($file_name, function() use ($exists) { return $exists; });
            echo "<div style='margin: 8px 0;'>";
            echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
            echo "<strong>{$file_name}</strong>";
            echo "</div>";
        }
        echo '</div>';

        echo '</div>'; // End test-grid

        // Test 5: URL Access Test
        echo '<div class="test-card">';
        echo '<h3>🌐 בדיקת URLs לגישה קדמית</h3>';
        
        $urls = [
            'Dashboard' => home_url('/budgex/'),
            'Sample Budget' => home_url('/budgex/budget/1/'),
            'Admin Panel' => admin_url('admin.php?page=budgex'),
        ];
        
        foreach ($urls as $url_name => $url) {
            echo "<div class='url-test'>";
            echo "<strong>{$url_name}:</strong>";
            echo "<div>";
            echo "<code style='background:#f1f1f1;padding:3px 6px;border-radius:3px;'>{$url}</code>";
            echo "<a href='{$url}' target='_blank' class='button' style='margin-right:10px;'>בדוק</a>";
            echo "</div>";
            echo "</div>";
        }
        echo '</div>';

        // Test 6: Functional Test
        echo '<div class="test-card">';
        echo '<h3>⚙️ בדיקה פונקציונלית</h3>';
        
        if (class_exists('Budgex_Public')) {
            $public = new Budgex_Public();
            
            // Test dashboard display
            if (method_exists($public, 'display_dashboard_frontend')) {
                try {
                    $dashboard_output = $public->display_dashboard_frontend();
                    $dashboard_works = !empty($dashboard_output) && !strpos($dashboard_output, 'Fatal error');
                    $result = run_test('Dashboard Display', function() use ($dashboard_works) { return $dashboard_works; });
                    echo "<div style='margin: 8px 0;'>";
                    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
                    echo "<strong>Dashboard Display</strong>";
                    echo "</div>";
                } catch (Exception $e) {
                    echo "<div style='margin: 8px 0;'>";
                    echo "<span class='status error'>שגיאה</span> ";
                    echo "<strong>Dashboard Display: " . $e->getMessage() . "</strong>";
                    echo "</div>";
                    $failed_tests++;
                    $total_tests++;
                }
            }
            
            // Test budget page display
            if (method_exists($public, 'display_single_budget_frontend')) {
                try {
                    $budget_output = $public->display_single_budget_frontend(999999);
                    $budget_handles_error = !empty($budget_output) && !strpos($budget_output, 'Fatal error');
                    $result = run_test('Budget Page Error Handling', function() use ($budget_handles_error) { return $budget_handles_error; });
                    echo "<div style='margin: 8px 0;'>";
                    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
                    echo "<strong>Budget Page Error Handling</strong>";
                    echo "</div>";
                } catch (Exception $e) {
                    echo "<div style='margin: 8px 0;'>";
                    echo "<span class='status error'>שגיאה</span> ";
                    echo "<strong>Budget Page: " . $e->getMessage() . "</strong>";
                    echo "</div>";
                    $failed_tests++;
                    $total_tests++;
                }
            }
        }
        echo '</div>';

        // Results Summary
        $success_rate = $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 1) : 0;
        $overall_status = 'success';
        if ($failed_tests > 0) $overall_status = 'error';
        elseif ($warnings > 0) $overall_status = 'warning';

        echo "<div class='results-summary'>";
        echo "<h2>📊 תוצאות סופיות</h2>";
        echo "<div style='font-size: 1.2em; margin: 15px 0;'>";
        echo "<strong>אחוז הצלחה: {$success_rate}%</strong><br>";
        echo "בדיקות שעברו: <span style='color:#28a745;'>{$passed_tests}</span> | ";
        echo "אזהרות: <span style='color:#ffc107;'>{$warnings}</span> | ";
        echo "נכשלו: <span style='color:#dc3545;'>{$failed_tests}</span> | ";
        echo "סה\"כ: {$total_tests}";
        echo "</div>";

        if ($overall_status === 'success') {
            echo "<div style='color:#28a745; font-size:1.5em; margin:20px 0;'>✅ המערכת מוכנה לשימוש!</div>";
        } elseif ($overall_status === 'warning') {
            echo "<div style='color:#ffc107; font-size:1.5em; margin:20px 0;'>⚠️ המערכת עובדת אך דורשת תשומת לב</div>";
        } else {
            echo "<div style='color:#dc3545; font-size:1.5em; margin:20px 0;'>❌ נדרשים תיקונים לפני השימוש</div>";
        }
        echo "</div>";

        // Quick Actions
        echo "<div style='text-align:center; margin:30px 0;'>";
        echo "<h3>פעולות מהירות</h3>";
        echo "<a href='" . home_url('/budgex/') . "' class='button success' target='_blank'>Frontend Dashboard</a>";
        echo "<a href='" . admin_url('admin.php?page=budgex') . "' class='button'>Admin Panel</a>";
        echo "<a href='" . admin_url('admin.php?page=budgex-new') . "' class='button secondary'>Create Test Budget</a>";
        echo "<a href='javascript:location.reload()' class='button secondary'>Rerun Test</a>";
        echo "</div>";

        // Debug Information
        if (defined('WP_DEBUG') && WP_DEBUG) {
            echo "<div class='test-card'>";
            echo "<h3>🐛 Debug Information</h3>";
            echo "<div class='code'>";
            echo "WordPress Version: " . get_bloginfo('version') . "<br>";
            echo "Plugin Path: " . BUDGEX_DIR . "<br>";
            echo "Plugin URL: " . BUDGEX_URL . "<br>";
            echo "Current User: " . (is_user_logged_in() ? wp_get_current_user()->display_name : 'Not logged in') . "<br>";
            echo "Site URL: " . site_url() . "<br>";
            echo "Home URL: " . home_url() . "<br>";
            echo "</div>";
            echo "</div>";
        }
        ?>

    </div>
</div>

<script>
// Auto-test AJAX endpoints if jQuery is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined') {
        console.log('jQuery loaded, AJAX endpoints available for testing');
        
        // Test basic AJAX endpoint
        setTimeout(function() {
            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'budgex_get_dashboard_stats',
                nonce: '<?php echo wp_create_nonce('budgex_public_nonce'); ?>'
            }).done(function(response) {
                console.log('AJAX test successful:', response);
            }).fail(function(xhr, status, error) {
                console.log('AJAX test failed:', error);
            });
        }, 1000);
    }
});

// Auto-refresh for development
setTimeout(function() {
    if (confirm('רענן את הבדיקות?')) {
        location.reload();
    }
}, 180000); // 3 minutes
</script>

</body>
</html>
