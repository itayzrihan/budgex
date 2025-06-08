<?php
/**
 * Update Budget Navigation to Use New Direct Route
 * This script updates budget navigation links to use /budgexpage/{id} instead of /budgex/budget/{id}
 */

// Include WordPress
if (!defined('ABSPATH')) {
    require_once('../../../wp-config.php');
}

echo "<h1>🔗 Update Budget Navigation Links</h1>";

echo "<h2>Search for Budget Navigation Links</h2>";

// Files to check and update
$files_to_update = [
    BUDGEX_DIR . 'public/partials/budgex-dashboard.php',
    BUDGEX_DIR . 'public/templates/budgex-app.php',
    BUDGEX_DIR . 'public/partials/budgex-budget-page.php',
    BUDGEX_DIR . 'public/partials/budgex-enhanced-budget-page.php',
    BUDGEX_DIR . 'public/partials/budgex-public-enhanced-budget-page.php',
    BUDGEX_DIR . 'public/partials/budgex-admin-enhanced-budget-page.php'
];

// Search patterns for budget links
$old_patterns = [
    '/budgex/budget/',
    'budgex/budget/',
    "'/budgex/budget/'",
    '"/budgex/budget/"',
    'home_url(\'/budgex/budget/\'',
    'window.location.href = \'/budgex/budget/\''
];

$new_replacement = '/budgexpage/';

echo "<p>Searching for budget navigation links in files...</p>";

foreach ($files_to_update as $file_path) {
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        $original_content = $content;
        $changes_made = false;
        
        echo "<h3>📄 " . basename($file_path) . "</h3>";
        
        // Check for each pattern
        foreach ($old_patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                echo "<p>⚠️ Found pattern: <code>$pattern</code></p>";
                
                // Create appropriate replacement
                if (strpos($pattern, 'home_url') !== false) {
                    $replacement = str_replace('/budgex/budget/', '/budgexpage/', $pattern);
                } elseif (strpos($pattern, 'window.location.href') !== false) {
                    $replacement = str_replace('/budgex/budget/', '/budgexpage/', $pattern);
                } else {
                    $replacement = str_replace('/budgex/budget/', '/budgexpage/', $pattern);
                }
                
                echo "<p>🔄 Will replace with: <code>$replacement</code></p>";
                
                // Count occurrences
                $count = substr_count($content, $pattern);
                echo "<p>📊 Found $count occurrence(s)</p>";
            }
        }
        
        // Show example of what navigation should look like
        echo "<div style='background: #e8f5e8; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<p><strong>✅ Recommended navigation pattern:</strong></p>";
        echo "<code>&lt;a href=\"&lt;?php echo home_url('/budgexpage/' . \$budget->id . '/'); ?&gt;\"&gt;View Budget&lt;/a&gt;</code>";
        echo "</div>";
        
    } else {
        echo "<p>❌ File not found: $file_path</p>";
    }
}

// Generate example navigation updates
echo "<h2>📝 Manual Navigation Updates Needed</h2>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>Dashboard Budget Cards</h3>";
echo "<p><strong>Old:</strong></p>";
echo "<pre>&lt;a href=\"&lt;?php echo home_url('/budgex/budget/' . \$budget->id . '/'); ?&gt;\"&gt;</pre>";
echo "<p><strong>New:</strong></p>";
echo "<pre>&lt;a href=\"&lt;?php echo home_url('/budgexpage/' . \$budget->id . '/'); ?&gt;\"&gt;</pre>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>JavaScript Navigation</h3>";
echo "<p><strong>Old:</strong></p>";
echo "<pre>window.location.href = '/budgex/budget/' + budgetId + '/';</pre>";
echo "<p><strong>New:</strong></p>";
echo "<pre>window.location.href = '/budgexpage/' + budgetId + '/';</pre>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>Budget Navigation in Templates</h3>";
echo "<p><strong>Old:</strong></p>";
echo "<pre>&lt;?php echo home_url('/budgex/budget/' . \$budget_id . '/'); ?&gt;</pre>";
echo "<p><strong>New:</strong></p>";
echo "<pre>&lt;?php echo home_url('/budgexpage/' . \$budget_id . '/'); ?&gt;</pre>";
echo "</div>";

// Test the new routing
echo "<h2>🧪 Test New Route</h2>";
$test_budget_ids = [1, 2, 3, 7, 10];

echo "<p>Click these links to test the new enhanced route:</p>";
echo "<ul>";
foreach ($test_budget_ids as $budget_id) {
    $new_url = home_url("/budgexpage/$budget_id/");
    $old_url = home_url("/budgex/budget/$budget_id/");
    
    echo "<li>";
    echo "<strong>Budget $budget_id:</strong><br>";
    echo "New: <a href='$new_url' target='_blank'>$new_url</a><br>";
    echo "Old: <a href='$old_url' target='_blank'>$old_url</a>";
    echo "</li>";
}
echo "</ul>";

// Instructions
echo "<h2>📋 Implementation Steps</h2>";
echo "<ol>";
echo "<li><strong>Flush Rewrite Rules:</strong> The new route has been added, run the test script to flush rules</li>";
echo "<li><strong>Update Navigation Links:</strong> Replace /budgex/budget/ with /budgexpage/ in all navigation</li>";
echo "<li><strong>Test Access:</strong> Verify that /budgexpage/{id} loads the enhanced budget page correctly</li>";
echo "<li><strong>Update Dashboard:</strong> Make sure budget cards link to the new route</li>";
echo "<li><strong>JavaScript Updates:</strong> Update any JS code that navigates to budget pages</li>";
echo "</ol>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
echo "<h3>✅ Benefits of New Route</h3>";
echo "<ul>";
echo "<li>Direct access to enhanced budget page</li>";
echo "<li>Bypasses complex routing in display_budgex_app()</li>";
echo "<li>Simpler URL structure</li>";
echo "<li>Better performance</li>";
echo "<li>Easier debugging</li>";
echo "</ul>";
echo "</div>";

?>
