<?php
/**
 * Test Frontend Integration for Budgex Plugin
 * 
 * This file provides a comprehensive test of the complete frontend access system
 * including routing, templates, permissions, and display functionality.
 */

if (!defined('ABSPATH')) {
    // If this file is accessed directly, load WordPress
    require_once '../../../wp-config.php';
}

// Ensure we have WordPress functions
if (!function_exists('add_action')) {
    die('WordPress not loaded properly');
}

// Set content type
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgex Frontend Integration Test</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f1f1f1;
            direction: rtl;
        }
        .test-container { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-section { 
            margin: 20px 0; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
        }
        .success { 
            background: #d4edda; 
            border-color: #c3e6cb; 
            color: #155724;
        }
        .error { 
            background: #f8d7da; 
            border-color: #f5c6cb; 
            color: #721c24;
        }
        .warning { 
            background: #fff3cd; 
            border-color: #ffeaa7; 
            color: #856404;
        }
        .info { 
            background: #d1ecf1; 
            border-color: #bee5eb; 
            color: #0c5460;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .button {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .button:hover { background: #005a87; }
        .button.success { background: #46b450; }
        .button.secondary { background: #666; }
        pre { 
            background: #f8f8f8; 
            padding: 10px; 
            border-radius: 4px; 
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: 10px;
        }
        .status-ok { background: #46b450; }
        .status-error { background: #dc3232; }
        .status-warning { background: #ffba00; }
    </style>
</head>
<body>

<div class="test-container">
    <h1>🎯 Budgex Frontend Integration Test</h1>
    <p style="text-align: center; color: #666;">
        <strong>תאריך:</strong> <?php echo date('d/m/Y H:i:s'); ?> | 
        <strong>גרסת וורדפרס:</strong> <?php echo get_bloginfo('version'); ?> |
        <strong>משתמש נוכחי:</strong> <?php echo is_user_logged_in() ? wp_get_current_user()->display_name : 'לא מחובר'; ?>
    </p>

    <?php
    // Initialize test results
    $results = [];
    $overall_status = 'success';

    /**
     * Test 1: Plugin Files and Classes
     */
    echo '<div class="test-section">';
    echo '<h2>🔍 Test 1: Plugin Files and Classes</h2>';
    
    $tests = [
        'Budgex Main Class' => class_exists('Budgex'),
        'Budgex Public Class' => class_exists('Budgex_Public'),
        'Budgex Database Class' => class_exists('Budgex_Database'),
        'Budgex Permissions Class' => class_exists('Budgex_Permissions'),
        'Budgex Calculator Class' => class_exists('Budgex_Budget_Calculator'),
        'Public Template File' => file_exists(BUDGEX_DIR . 'public/templates/budgex-app.php'),
        'Dashboard Partial' => file_exists(BUDGEX_DIR . 'public/partials/budgex-dashboard.php'),
        'Budget Page Partial' => file_exists(BUDGEX_DIR . 'public/partials/budgex-budget-page.php'),
        'Public CSS' => file_exists(BUDGEX_DIR . 'public/css/budgex-public.css'),
        'Public JS' => file_exists(BUDGEX_DIR . 'public/js/budgex-public.js'),
    ];
    
    foreach ($tests as $test_name => $result) {
        $status = $result ? 'ok' : 'error';
        $status_text = $result ? '✅ הצלחה' : '❌ שגיאה';
        if (!$result) $overall_status = 'error';
        
        echo "<div style='margin: 5px 0;'>";
        echo "<span class='status-indicator status-{$status}'></span>";
        echo "<strong>{$test_name}:</strong> {$status_text}";
        echo "</div>";
    }
    echo '</div>';

    /**
     * Test 2: WordPress Integration
     */
    echo '<div class="test-section">';
    echo '<h2>🔗 Test 2: WordPress Integration</h2>';
    
    $budgex = null;
    if (class_exists('Budgex')) {
        $budgex = new Budgex();
    }
    
    $wp_tests = [
        'Shortcode Registered' => shortcode_exists('budgex_app'),
        'Rewrite Rules Added' => !empty(get_option('rewrite_rules')),
        'Query Vars Available' => in_array('budgex_page', $GLOBALS['wp']->public_query_vars ?? []),
        'Frontend Page Exists' => !empty(get_page_by_path('budgex')),
    ];
    
    foreach ($wp_tests as $test_name => $result) {
        $status = $result ? 'ok' : 'warning';
        $status_text = $result ? '✅ הצלחה' : '⚠️ דורש בדיקה';
        if (!$result && $overall_status === 'success') $overall_status = 'warning';
        
        echo "<div style='margin: 5px 0;'>";
        echo "<span class='status-indicator status-{$status}'></span>";
        echo "<strong>{$test_name}:</strong> {$status_text}";
        echo "</div>";
    }
    echo '</div>';

    /**
     * Test 3: Public Class Methods
     */
    echo '<div class="test-section">';
    echo '<h2>🎯 Test 3: Public Class Methods</h2>';
    
    if (class_exists('Budgex_Public')) {
        $public = new Budgex_Public();
        $method_tests = [
            'render_dashboard' => method_exists($public, 'render_dashboard'),
            'render_budget_page' => method_exists($public, 'render_budget_page'),
            'display_dashboard_frontend' => method_exists($public, 'display_dashboard_frontend'),
            'display_single_budget_frontend' => method_exists($public, 'display_single_budget_frontend'),
            'enqueue_styles' => method_exists($public, 'enqueue_styles'),
            'enqueue_scripts' => method_exists($public, 'enqueue_scripts'),
        ];
        
        foreach ($method_tests as $method_name => $result) {
            $status = $result ? 'ok' : 'error';
            $status_text = $result ? '✅ קיים' : '❌ חסר';
            if (!$result) $overall_status = 'error';
            
            echo "<div style='margin: 5px 0;'>";
            echo "<span class='status-indicator status-{$status}'></span>";
            echo "<strong>{$method_name}:</strong> {$status_text}";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>❌ מחלקת Budgex_Public לא נמצאה</div>";
        $overall_status = 'error';
    }
    echo '</div>';

    /**
     * Test 4: Frontend URL Structure
     */
    echo '<div class="test-section">';
    echo '<h2>🌐 Test 4: Frontend URL Structure</h2>';
    
    $urls = [
        'Dashboard URL' => home_url('/budgex/'),
        'Sample Budget URL' => home_url('/budgex/budget/1/'),
        'Login URL' => wp_login_url(home_url('/budgex/')),
        'Admin Dashboard' => admin_url('admin.php?page=budgex'),
    ];
    
    foreach ($urls as $url_name => $url) {
        echo "<div style='margin: 5px 0;'>";
        echo "<strong>{$url_name}:</strong> ";
        echo "<a href='{$url}' target='_blank' class='button secondary'>{$url}</a>";
        echo "</div>";
    }
    echo '</div>';

    /**
     * Test 5: Database and Permissions
     */
    echo '<div class="test-section">';
    echo '<h2>💾 Test 5: Database and Permissions</h2>';
    
    if (class_exists('Budgex_Database') && class_exists('Budgex_Permissions')) {
        $database = new Budgex_Database();
        $permissions = new Budgex_Permissions();
        
        $db_tests = [
            'Database Class Initialize' => is_object($database),
            'Permissions Class Initialize' => is_object($permissions),
            'User Logged In' => is_user_logged_in(),
        ];
        
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $budgets = $database->get_user_budgets($user_id);
            $db_tests['User Has Budgets'] = !empty($budgets);
            $db_tests['Budget Count'] = count($budgets) . ' תקציבים';
        }
        
        foreach ($db_tests as $test_name => $result) {
            if (is_bool($result)) {
                $status = $result ? 'ok' : 'warning';
                $status_text = $result ? '✅ הצלחה' : '⚠️ דורש בדיקה';
            } else {
                $status = 'info';
                $status_text = "ℹ️ {$result}";
            }
            
            echo "<div style='margin: 5px 0;'>";
            echo "<span class='status-indicator status-{$status}'></span>";
            echo "<strong>{$test_name}:</strong> {$status_text}";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>❌ מחלקות Database או Permissions לא נמצאו</div>";
        $overall_status = 'error';
    }
    echo '</div>';

    /**
     * Test 6: Frontend Display Test
     */
    echo '<div class="test-section">';
    echo '<h2>🎨 Test 6: Frontend Display Test</h2>';
    
    if (class_exists('Budgex_Public')) {
        $public = new Budgex_Public();
        
        try {
            // Test dashboard display
            if (method_exists($public, 'display_dashboard_frontend')) {
                $dashboard_output = $public->display_dashboard_frontend();
                $dashboard_works = !empty($dashboard_output) && !strpos($dashboard_output, 'Fatal error');
                
                echo "<div style='margin: 10px 0;'>";
                echo "<span class='status-indicator status-" . ($dashboard_works ? 'ok' : 'error') . "'></span>";
                echo "<strong>Dashboard Display:</strong> " . ($dashboard_works ? '✅ עובד' : '❌ שגיאה');
                echo "</div>";
                
                if (!$dashboard_works) $overall_status = 'error';
            }
            
            // Test budget page display with non-existent budget ID
            if (method_exists($public, 'display_single_budget_frontend')) {
                $budget_output = $public->display_single_budget_frontend(999999);
                $budget_handles_error = !empty($budget_output) && !strpos($budget_output, 'Fatal error');
                
                echo "<div style='margin: 10px 0;'>";
                echo "<span class='status-indicator status-" . ($budget_handles_error ? 'ok' : 'error') . "'></span>";
                echo "<strong>Budget Page Error Handling:</strong> " . ($budget_handles_error ? '✅ עובד' : '❌ שגיאה');
                echo "</div>";
                
                if (!$budget_handles_error) $overall_status = 'error';
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ שגיאה בבדיקת התצוגה: " . $e->getMessage() . "</div>";
            $overall_status = 'error';
        }
    }
    echo '</div>';

    /**
     * Overall Status
     */
    $status_class = $overall_status;
    $status_message = [
        'success' => '🎉 כל הבדיקות עברו בהצלחה! המערכת מוכנה לשימוש.',
        'warning' => '⚠️ חלק מהבדיקות דורשות תשומת לב. המערכת עובדת אך ייתכן שדרושות התאמות.',
        'error' => '❌ נמצאו שגיאות שדורשות תיקון לפני השימוש במערכת.'
    ][$overall_status];

    echo "<div class='test-section {$status_class}'>";
    echo "<h2>📊 תוצאה כללית</h2>";
    echo "<p style='font-size: 18px; font-weight: bold;'>{$status_message}</p>";
    echo "</div>";

    /**
     * Next Steps
     */
    echo '<div class="test-section info">';
    echo '<h2>🚀 השלבים הבאים</h2>';
    echo '<div class="grid">';
    
    echo '<div>';
    echo '<h3>בדיקות נדרשות:</h3>';
    echo '<ul>';
    echo '<li><strong>Create Test Budget:</strong> השתמש בפאנל הניהול ליצירת תקציב לדוגמא</li>';
    echo '<li><strong>Test User Permissions:</strong> צור משתמשים נוספים ובדוק את מערכת ההזמנות</li>';
    echo '<li><strong>Mobile Testing:</strong> בדוק עיצוב רספונסיבי במכשירים שונים</li>';
    echo '<li><strong>Performance Testing:</strong> בדוק זמני טעינה וזמני תגובת AJAX</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<div>';
    echo '<h3>פעולות מהירות:</h3>';
    echo '<a href="' . admin_url('admin.php?page=budgex') . '" class="button">Admin Dashboard</a>';
    echo '<a href="' . home_url('/budgex/') . '" class="button" target="_blank">Frontend Dashboard</a>';
    echo '<a href="' . admin_url('admin.php?page=budgex-new') . '" class="button success">Create Test Budget</a>';
    echo '<a href="javascript:location.reload()" class="button secondary">Rerun Tests</a>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
    ?>

</div>

<script>
// AJAX testing functions
function testAjaxEndpoint(action) {
    if (typeof jQuery === 'undefined') {
        alert('jQuery not loaded');
        return;
    }
    
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: action,
            nonce: '<?php echo wp_create_nonce('budgex_public_nonce'); ?>'
        },
        success: function(response) {
            console.log(action + ' response:', response);
            alert(action + ' endpoint is responding');
        },
        error: function(xhr, status, error) {
            console.error(action + ' error:', error);
            alert(action + ' endpoint error: ' + error);
        }
    });
}

// Auto-refresh every 5 minutes for development
setTimeout(function() {
    if (confirm('רענן את הבדיקות?')) {
        location.reload();
    }
}, 300000);
</script>

</body>
</html>
