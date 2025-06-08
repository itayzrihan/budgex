<?php
/**
 * Quick Fix for Budgex Navigation Issue
 * 
 * Run this script to apply common fixes for the login redirect issue
 */

// Ensure this runs in WordPress context
if (!function_exists('home_url')) {
    require_once('../../../wp-config.php');
}

echo "<h1>Budgex Navigation Quick Fix</h1>\n";

// Fix 1: Force flush rewrite rules
echo "<h2>Fix 1: Flushing Rewrite Rules</h2>\n";
flush_rewrite_rules(true);
add_option('budgex_flush_rewrite_rules', 1);
echo "✅ Rewrite rules flushed\n";

// Fix 2: Verify and recreate main Budgex page if needed
echo "<h2>Fix 2: Checking Main Budgex Page</h2>\n";
$budgex_page = get_page_by_path('budgex');
if (!$budgex_page) {
    echo "❌ Main Budgex page not found, creating it...\n";
    $page_id = wp_insert_post([
        'post_title' => 'Budgex',
        'post_name' => 'budgex',
        'post_content' => '[budgex_app]',
        'post_status' => 'publish',
        'post_type' => 'page'
    ]);
    
    if ($page_id) {
        echo "✅ Created main Budgex page (ID: $page_id)\n";
    } else {
        echo "❌ Failed to create main Budgex page\n";
    }
} else {
    echo "✅ Main Budgex page exists (ID: {$budgex_page->ID})\n";
}

// Fix 3: Test URL generation
echo "<h2>Fix 3: Testing URL Generation</h2>\n";
$dashboard_url = home_url('/budgex/');
$budget_url_template = home_url('/budgex/budget/');
echo "Dashboard URL: $dashboard_url\n";
echo "Budget URL template: {$budget_url_template}{ID}/\n";

// Fix 4: Update JavaScript configuration in database if needed
echo "<h2>Fix 4: Checking JavaScript Configuration</h2>\n";
if (class_exists('Budgex_Public')) {
    echo "✅ Budgex_Public class available\n";
    echo "JavaScript will receive correct URLs on next page load\n";
} else {
    echo "❌ Budgex_Public class not available\n";
}

echo "<h2>Next Steps</h2>\n";
echo "<ol>\n";
echo "<li><strong>Go to WordPress Admin → Settings → Permalinks</strong></li>\n";
echo "<li><strong>Click 'Save Changes'</strong> (this ensures rewrite rules are properly saved)</li>\n";
echo "<li><strong>Test the navigation again</strong></li>\n";
echo "<li>If still not working, run the diagnostic script: <code>diagnose-navigation-issue.php</code></li>\n";
echo "</ol>\n";

echo "<h2>Test URLs</h2>\n";
echo "<p>Try these URLs directly in your browser:</p>\n";
echo "<ul>\n";
echo "<li><a href='$dashboard_url' target='_blank'>Dashboard: $dashboard_url</a></li>\n";

// If we can get some budget IDs, show test URLs
if (class_exists('Budgex_Database') && is_user_logged_in()) {
    $database = new Budgex_Database();
    $user_id = get_current_user_id();
    $budgets = $database->get_user_budgets($user_id);
    
    if (!empty($budgets)) {
        $test_budget = $budgets[0];
        $budget_url = home_url('/budgex/budget/' . $test_budget->id . '/');
        $management_url = home_url('/budgex/budget/' . $test_budget->id . '/#advanced-management-panel');
        echo "<li><a href='$budget_url' target='_blank'>Budget #{$test_budget->id}: $budget_url</a></li>\n";
        echo "<li><a href='$management_url' target='_blank'>Management Panel: $management_url</a></li>\n";
    }
}

echo "</ul>\n";

echo "<p style='background: #e7f3ff; padding: 15px; border-radius: 5px;'>\n";
echo "<strong>Important:</strong> After running this fix, you MUST go to WordPress Admin → Settings → Permalinks and click 'Save Changes' to complete the rewrite rules update.\n";
echo "</p>\n";
?>
