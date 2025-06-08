<?php
/**
 * Budgex Navigation Issue Diagnostic and Fix
 * 
 * This script will help diagnose and fix the navigation issue
 * where clicking "ניהול מתקדם" redirects to login page
 */

if (!defined('ABSPATH')) {
    // If running outside WordPress, include WordPress
    require_once('../../../wp-config.php');
}

function diagnose_budgex_navigation() {
    echo "<h1>Budgex Navigation Issue Diagnostic</h1>\n";
    
    $issues_found = [];
    $fixes_applied = [];
    
    // Check 1: User login status
    echo "<h2>1. User Authentication Check</h2>\n";
    if (is_user_logged_in()) {
        echo "✅ User is logged in as: " . wp_get_current_user()->display_name . "\n";
        $user_id = get_current_user_id();
    } else {
        echo "❌ User is NOT logged in\n";
        $issues_found[] = "User not logged in";
        echo "<p><strong>Fix:</strong> Please log in to WordPress first.</p>\n";
        return;
    }
    
    // Check 2: Plugin activation
    echo "<h2>2. Plugin Activation Check</h2>\n";
    if (class_exists('Budgex')) {
        echo "✅ Budgex plugin is loaded\n";
    } else {
        echo "❌ Budgex plugin is not loaded\n";
        $issues_found[] = "Plugin not activated";
    }
    
    // Check 3: Database and permissions
    echo "<h2>3. Database and Permissions Check</h2>\n";
    if (class_exists('Budgex_Database')) {
        $database = new Budgex_Database();
        $permissions = new Budgex_Permissions();
        
        // Check if user has any budgets
        $user_budgets = $database->get_user_budgets($user_id);
        $shared_budgets = $database->get_shared_budgets($user_id);
        
        echo "User owns " . count($user_budgets) . " budgets\n";
        echo "User has access to " . count($shared_budgets) . " shared budgets\n";
        
        if (empty($user_budgets) && empty($shared_budgets)) {
            echo "⚠️ User has no budgets - this might be the issue\n";
            $issues_found[] = "No budgets available";
        } else {
            echo "✅ User has budget access\n";
            
            // Test specific budget access
            foreach ($user_budgets as $budget) {
                $can_view = $permissions->can_view_budget($budget->id, $user_id);
                $user_role = $permissions->get_user_role_on_budget($budget->id, $user_id);
                echo "Budget #{$budget->id} ({$budget->name}): Can view = " . ($can_view ? 'Yes' : 'No') . ", Role = $user_role\n";
            }
        }
    } else {
        echo "❌ Budgex database classes not available\n";
        $issues_found[] = "Database classes missing";
    }
    
    // Check 4: Rewrite rules
    echo "<h2>4. WordPress Rewrite Rules Check</h2>\n";
    $rewrite_rules = get_option('rewrite_rules', []);
    $budgex_rules_found = 0;
    
    foreach ($rewrite_rules as $rule => $rewrite) {
        if (strpos($rule, 'budgex') !== false) {
            echo "Found rule: $rule → $rewrite\n";
            $budgex_rules_found++;
        }
    }
    
    if ($budgex_rules_found === 0) {
        echo "❌ No Budgex rewrite rules found\n";
        $issues_found[] = "Rewrite rules missing";
        
        echo "<p><strong>Applying Fix:</strong> Flushing rewrite rules...</p>\n";
        add_option('budgex_flush_rewrite_rules', 1);
        flush_rewrite_rules(true);
        $fixes_applied[] = "Flushed rewrite rules";
        echo "✅ Rewrite rules flushed\n";
    } else {
        echo "✅ Found $budgex_rules_found Budgex rewrite rules\n";
    }
    
    // Check 5: URL construction
    echo "<h2>5. URL Construction Check</h2>\n";
    $dashboard_url = home_url('/budgex/');
    $budget_url = home_url('/budgex/budget/');
    echo "Dashboard URL: $dashboard_url\n";
    echo "Budget URL template: $budget_url{ID}/\n";
    
    // Test a specific budget URL
    if (!empty($user_budgets)) {
        $test_budget = $user_budgets[0];
        $test_url = home_url('/budgex/budget/' . $test_budget->id . '/');
        $test_management_url = home_url('/budgex/budget/' . $test_budget->id . '/#advanced-management-panel');
        echo "Test budget URL: $test_url\n";
        echo "Test management URL: $test_management_url\n";
    }
    
    // Check 6: JavaScript configuration
    echo "<h2>6. JavaScript Configuration Check</h2>\n";
    echo "Checking if JavaScript will receive correct URLs...\n";
    
    // Simulate the wp_localize_script data
    $js_config = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'dashboard_url' => home_url('/budgex/'),
        'budget_url' => home_url('/budgex/budget/'),
    );
    
    echo "JavaScript config:\n";
    foreach ($js_config as $key => $value) {
        echo "  $key: $value\n";
    }
    
    // Summary
    echo "<h2>Summary</h2>\n";
    if (empty($issues_found)) {
        echo "<p style='color: green; font-weight: bold;'>✅ No major issues found!</p>\n";
        echo "<p>The navigation should be working. If you're still experiencing issues, try:</p>\n";
        echo "<ol>\n";
        echo "<li>Clear your browser cache</li>\n";
        echo "<li>Check browser console for JavaScript errors</li>\n";
        echo "<li>Verify you're clicking the correct button</li>\n";
        echo "<li>Try accessing the URL directly in your browser</li>\n";
        echo "</ol>\n";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Issues found:</p>\n";
        echo "<ul>\n";
        foreach ($issues_found as $issue) {
            echo "<li>$issue</li>\n";
        }
        echo "</ul>\n";
        
        if (!empty($fixes_applied)) {
            echo "<p style='color: blue; font-weight: bold;'>🔧 Fixes applied:</p>\n";
            echo "<ul>\n";
            foreach ($fixes_applied as $fix) {
                echo "<li>$fix</li>\n";
            }
            echo "</ul>\n";
        }
    }
    
    // Manual testing instructions
    echo "<h2>Manual Testing Instructions</h2>\n";
    echo "<ol>\n";
    echo "<li>Go to your Budgex dashboard: <a href='$dashboard_url' target='_blank'>$dashboard_url</a></li>\n";
    if (!empty($user_budgets)) {
        $test_budget = $user_budgets[0];
        $management_url = home_url('/budgex/budget/' . $test_budget->id . '/#advanced-management-panel');
        echo "<li>Test direct management URL: <a href='$management_url' target='_blank'>$management_url</a></li>\n";
    }
    echo "<li>Click the 'ניהול מתקדם' button on a budget card</li>\n";
    echo "<li>Check browser console for any JavaScript errors</li>\n";
    echo "</ol>\n";
}

// Run the diagnostic
diagnose_budgex_navigation();
?>
