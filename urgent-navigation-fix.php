<?php
/**
 * URGENT FIX: Flush Rewrite Rules and Register Query Variables
 * Run this file once to fix the empty page issue
 */

// Include WordPress
if (file_exists('../../../../wp-config.php')) {
    require_once '../../../../wp-config.php';
} elseif (file_exists('../../wp-config.php')) {
    require_once '../../wp-config.php';
} else {
    die('WordPress not found. Run this file from your WordPress directory.');
}

echo "<h1>🔧 Urgent Navigation Fix</h1>\n";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>\n";

// Step 1: Register query variables manually
echo "<h2>Step 1: Registering Query Variables</h2>\n";
global $wp;
$wp->add_query_var('budgex_page');
$wp->add_query_var('budget_id');
echo "✅ Query variables registered<br>\n";

// Step 2: Add rewrite rules manually
echo "<h2>Step 2: Adding Rewrite Rules</h2>\n";
add_rewrite_rule(
    '^budgex/?$',
    'index.php?budgex_page=dashboard',
    'top'
);
echo "✅ Dashboard rule added<br>\n";

add_rewrite_rule(
    '^budgex/budget/([0-9]+)/?$',
    'index.php?budgex_page=budget&budget_id=$matches[1]',
    'top'
);
echo "✅ Budget page rule added<br>\n";

add_rewrite_rule(
    '^budgex/([^/]+)/?$',
    'index.php?budgex_page=$matches[1]',
    'top'
);
echo "✅ General page rule added<br>\n";

// Step 3: Flush rewrite rules
echo "<h2>Step 3: Flushing Rewrite Rules</h2>\n";
flush_rewrite_rules();
echo "✅ Rewrite rules flushed<br>\n";

// Step 4: Test the rules
echo "<h2>Step 4: Testing Rules</h2>\n";
$rewrite_rules = get_option('rewrite_rules');
$budgex_rules = array_filter($rewrite_rules, function($key) {
    return strpos($key, 'budgex') !== false;
}, ARRAY_FILTER_USE_KEY);

if (!empty($budgex_rules)) {
    echo "✅ Budgex rules now active:<br>\n";
    foreach ($budgex_rules as $pattern => $rewrite) {
        echo "&nbsp;&nbsp;- <code>$pattern</code> → <code>$rewrite</code><br>\n";
    }
} else {
    echo "❌ Budgex rules still not found<br>\n";
}

// Step 5: Create a permanent fix
echo "<h2>Step 5: Permanent Fix Instructions</h2>\n";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; border-left: 4px solid #4caf50;'>\n";
echo "<h3>✅ Immediate Actions Needed:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Go to WordPress Admin → Settings → Permalinks</strong></li>\n";
echo "<li><strong>Click 'Save Changes' button</strong> (this will flush rewrite rules)</li>\n";
echo "<li><strong>Test the URL:</strong> <a href='/budgex/budget/7/' target='_blank'>https://mytx.one/budgex/budget/7/</a></li>\n";
echo "</ol>\n";
echo "</div>\n";

// Step 6: Alternative direct fix
echo "<h2>Step 6: Alternative Direct Fix</h2>\n";
echo "<p>If the above doesn't work, add this to your theme's functions.php temporarily:</p>\n";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace; background: #f9f9f9; padding: 10px;'>\n";
echo "// TEMPORARY FIX - Remove after permalinks flush\n";
echo "add_action('init', function() {\n";
echo "    global \$wp;\n";
echo "    \$wp->add_query_var('budgex_page');\n";
echo "    \$wp->add_query_var('budget_id');\n";
echo "    \n";
echo "    add_rewrite_rule('^budgex/?\$', 'index.php?budgex_page=dashboard', 'top');\n";
echo "    add_rewrite_rule('^budgex/budget/([0-9]+)/?\$', 'index.php?budgex_page=budget&budget_id=\$matches[1]', 'top');\n";
echo "    \n";
echo "    if (get_option('manual_flush_needed')) {\n";
echo "        flush_rewrite_rules();\n";
echo "        delete_option('manual_flush_needed');\n";
echo "    }\n";
echo "});\n";
echo "\n";
echo "// Run this once to trigger flush\n";
echo "update_option('manual_flush_needed', true);\n";
echo "</textarea>\n";

// Step 7: Test current state
echo "<h2>Step 7: Current State Test</h2>\n";
$test_budget_id = 7;
$test_url = home_url("/budgex/budget/$test_budget_id/");
echo "Test URL: <a href='$test_url' target='_blank'>$test_url</a><br>\n";

// Simulate the query
$_GET['budgex_page'] = 'budget';
$_GET['budget_id'] = $test_budget_id;

// Check if template would load
if (file_exists(BUDGEX_DIR . 'public/templates/budgex-app.php')) {
    echo "✅ Template file exists and would load<br>\n";
} else {
    echo "❌ Template file missing<br>\n";
}

echo "</div>\n";

echo "<div style='background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<h2>🎯 Summary</h2>\n";
echo "<p><strong>Root Cause:</strong> WordPress wasn't recognizing the custom query variables (budgex_page, budget_id) which caused empty pages.</p>\n";
echo "<p><strong>Fix Applied:</strong> Registered query variables and flushed rewrite rules.</p>\n";
echo "<p><strong>Next Step:</strong> Go to WordPress Admin → Settings → Permalinks and click Save Changes.</p>\n";
echo "</div>\n";

echo "<script>\n";
echo "console.log('Navigation fix applied. Please flush permalinks in WordPress admin.');\n";
echo "alert('Fix applied! Please go to WordPress Admin → Settings → Permalinks and click Save Changes.');\n";
echo "</script>\n";
?>
