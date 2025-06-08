<?php
/**
 * Test the new /budgexpage/{id} route
 * This script creates the route and tests access to the enhanced budget page
 */

// Include WordPress
if (!defined('ABSPATH')) {
    require_once('../../../wp-config.php');
}

echo "<h1>🔧 Testing New /budgexpage/{id} Route</h1>";

// First, let's flush rewrite rules to ensure our new route is active
flush_rewrite_rules();
echo "<p>✅ Rewrite rules flushed</p>";

// Test the route detection
echo "<h2>Route Detection Test</h2>";

// Simulate the new route
$_SERVER['REQUEST_URI'] = '/budgexpage/7/';
$GLOBALS['wp']->parse_request();

echo "<p><strong>Current Query Vars:</strong></p>";
echo "<pre>";
print_r($GLOBALS['wp']->query_vars);
echo "</pre>";

// Test specific query vars
echo "<p><strong>budgex_enhanced_page:</strong> " . get_query_var('budgex_enhanced_page') . "</p>";
echo "<p><strong>budget_id:</strong> " . get_query_var('budget_id') . "</p>";

// Test URL generation
echo "<h2>URL Generation Test</h2>";
$test_budget_id = 7;
$new_route_url = home_url("/budgexpage/$test_budget_id/");
echo "<p><strong>New Enhanced Route URL:</strong> <a href='$new_route_url' target='_blank'>$new_route_url</a></p>";

// Test template file existence
echo "<h2>Template File Check</h2>";
$template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
if (file_exists($template_path)) {
    echo "<p>✅ Enhanced direct template exists: $template_path</p>";
} else {
    echo "<p>❌ Enhanced direct template missing: $template_path</p>";
}

// Test enhanced budget page template
$enhanced_template_path = BUDGEX_DIR . 'public/partials/budgex-public-enhanced-budget-page.php';
if (file_exists($enhanced_template_path)) {
    echo "<p>✅ Enhanced budget page template exists: $enhanced_template_path</p>";
} else {
    echo "<p>❌ Enhanced budget page template missing: $enhanced_template_path</p>";
}

// Test classes
echo "<h2>Class Availability Test</h2>";
$required_classes = [
    'Budgex',
    'Budgex_Database',
    'Budgex_Permissions', 
    'Budgex_Budget_Calculator'
];

foreach ($required_classes as $class) {
    if (class_exists($class)) {
        echo "<p>✅ $class - Available</p>";
    } else {
        echo "<p>❌ $class - Missing</p>";
    }
}

// Test current user access
echo "<h2>User Access Test</h2>";
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    echo "<p>✅ User logged in (ID: $user_id)</p>";
    
    // Test budget access
    if (class_exists('Budgex_Permissions')) {
        $permissions = new Budgex_Permissions();
        $test_budget_id = 7;
        
        if ($permissions->can_view_budget($test_budget_id, $user_id)) {
            echo "<p>✅ User has access to budget $test_budget_id</p>";
        } else {
            echo "<p>⚠️ User does not have access to budget $test_budget_id</p>";
        }
    }
} else {
    echo "<p>⚠️ No user logged in</p>";
}

// Create a test link
echo "<h2>Direct Test Links</h2>";
echo "<p><strong>Test the new route:</strong></p>";
echo "<ul>";
for ($i = 1; $i <= 10; $i++) {
    $url = home_url("/budgexpage/$i/");
    echo "<li><a href='$url' target='_blank'>Budget $i - $url</a></li>";
}
echo "</ul>";

// Show rewrite rules
echo "<h2>Current Rewrite Rules</h2>";
echo "<pre>";
$rules = get_option('rewrite_rules');
foreach ($rules as $pattern => $rewrite) {
    if (strpos($pattern, 'budgex') !== false) {
        echo "$pattern => $rewrite\n";
    }
}
echo "</pre>";

echo "<h2>Usage Instructions</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>How to use the new direct route:</strong></p>";
echo "<ol>";
echo "<li>Instead of navigating to <code>/budgex/budget/{id}</code></li>";
echo "<li>Use the new direct route: <code>/budgexpage/{id}</code></li>";
echo "<li>This will load the enhanced budget page directly without routing issues</li>";
echo "<li>Update your navigation links to use the new route</li>";
echo "</ol>";
echo "</div>";

?>
