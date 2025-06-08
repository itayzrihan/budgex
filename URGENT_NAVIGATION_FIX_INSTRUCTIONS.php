<?php
/**
 * Complete Navigation Fix
 * Fixes both empty page and link navigation issues
 */

// This file should be copied to your WordPress root directory and run once

echo "<h1>🔧 Complete Navigation Fix for Budgex</h1>\n";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>\n";

echo "<h2>Issue Analysis</h2>\n";
echo "<p><strong>Problem 1:</strong> Empty page at /budgex/budget/7/ - WordPress doesn't recognize custom query variables</p>\n";
echo "<p><strong>Problem 2:</strong> Navigation links don't work - May be CSS/JS conflicts or missing event handling</p>\n";

echo "<h2>🎯 Solution Implementation</h2>\n";

echo "<h3>Step 1: Add to your theme's functions.php (URGENT)</h3>\n";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>\n";
echo "<p>Copy this code to your theme's functions.php file:</p>\n";
echo "<textarea style='width: 100%; height: 300px; font-family: monospace;'>\n";
echo "// BUDGEX NAVIGATION FIX - Add this to functions.php\n";
echo "add_action('init', 'budgex_fix_navigation', 1);\n";
echo "function budgex_fix_navigation() {\n";
echo "    // Register query variables\n";
echo "    global \$wp;\n";
echo "    \$wp->add_query_var('budgex_page');\n";
echo "    \$wp->add_query_var('budget_id');\n";
echo "    \n";
echo "    // Add rewrite rules\n";
echo "    add_rewrite_rule('^budgex/?\$', 'index.php?budgex_page=dashboard', 'top');\n";
echo "    add_rewrite_rule('^budgex/budget/([0-9]+)/?\$', 'index.php?budgex_page=budget&budget_id=\$matches[1]', 'top');\n";
echo "    add_rewrite_rule('^budgex/([^/]+)/?\$', 'index.php?budgex_page=\$matches[1]', 'top');\n";
echo "    \n";
echo "    // Flush rules if needed\n";
echo "    if (get_option('budgex_needs_flush')) {\n";
echo "        flush_rewrite_rules();\n";
echo "        delete_option('budgex_needs_flush');\n";
echo "    }\n";
echo "}\n";
echo "\n";
echo "// Trigger flush once\n";
echo "add_action('admin_init', function() {\n";
echo "    if (!get_option('budgex_flush_done')) {\n";
echo "        update_option('budgex_needs_flush', true);\n";
echo "        update_option('budgex_flush_done', true);\n";
echo "    }\n";
echo "});\n";
echo "</textarea>\n";
echo "</div>\n";

echo "<h3>Step 2: Fix Dashboard Button CSS/JS Conflicts</h3>\n";
echo "<p>The button might be blocked by CSS or JavaScript. Here's an enhanced button:</p>\n";

$enhanced_button_code = '
<!-- Replace the existing "ניהול מתקדם" button with this enhanced version -->
<a href="<?php echo esc_url(home_url(\'/budgex/budget/\' . $budget->id . \'/\')); ?>" 
   class="button secondary budgex-advanced-btn" 
   style="display: inline-block !important; 
          pointer-events: auto !important;
          position: relative !important;
          z-index: 999 !important;"
   onclick="console.log(\'Navigating to budget\', this.href); return true;">
    <?php _e(\'ניהול מתקדם\', \'budgex\'); ?>
</a>

<script>
// Ensure button clicks work
jQuery(document).ready(function($) {
    $(\'.budgex-advanced-btn\').on(\'click\', function(e) {
        e.stopPropagation();
        var url = $(this).attr(\'href\');
        console.log(\'Navigating to:\', url);
        window.location.href = url;
    });
});
</script>';

echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>" . htmlentities($enhanced_button_code) . "</textarea>\n";

echo "<h3>Step 3: Manual WordPress Actions</h3>\n";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>\n";
echo "<ol>\n";
echo "<li><strong>Go to WordPress Admin → Settings → Permalinks</strong></li>\n";
echo "<li><strong>Click 'Save Changes'</strong> (this flushes rewrite rules)</li>\n";
echo "<li><strong>Deactivate and Reactivate Budgex plugin</strong></li>\n";
echo "<li><strong>Test URL manually:</strong> Visit https://mytx.one/budgex/budget/7/</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<h3>Step 4: Emergency Template Fix</h3>\n";
echo "<p>If the page is still empty, add this to your theme's functions.php:</p>\n";
echo "<textarea style='width: 100%; height: 150px; font-family: monospace;'>\n";
echo "// EMERGENCY TEMPLATE FIX\n";
echo "add_action('template_redirect', function() {\n";
echo "    if (get_query_var('budgex_page') === 'budget') {\n";
echo "        \$budget_id = get_query_var('budget_id');\n";
echo "        if (\$budget_id) {\n";
echo "            include_once WP_PLUGIN_DIR . '/budgex/public/templates/budgex-app.php';\n";
echo "            exit;\n";
echo "        }\n";
echo "    }\n";
echo "});\n";
echo "</textarea>\n";

echo "<h3>Step 5: Debug Information</h3>\n";
echo "<p>Add this debug code to see what's happening:</p>\n";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>\n";
echo "// DEBUG - Add to functions.php temporarily\n";
echo "add_action('template_redirect', function() {\n";
echo "    \$budgex_page = get_query_var('budgex_page');\n";
echo "    \$budget_id = get_query_var('budget_id');\n";
echo "    \n";
echo "    if (\$budgex_page || \$budget_id) {\n";
echo "        error_log('Budgex Debug: Page=' . \$budgex_page . ', Budget ID=' . \$budget_id);\n";
echo "        error_log('Request URI: ' . \$_SERVER['REQUEST_URI']);\n";
echo "        error_log('User logged in: ' . (is_user_logged_in() ? 'Yes' : 'No'));\n";
echo "    }\n";
echo "});\n";
echo "</textarea>\n";

echo "<h2>🚀 Testing Instructions</h2>\n";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px;'>\n";
echo "<h3>After applying the fixes:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Test Dashboard:</strong> Go to https://mytx.one/budgex/</li>\n";
echo "<li><strong>Test Direct Budget URL:</strong> Go to https://mytx.one/budgex/budget/7/</li>\n";
echo "<li><strong>Test Button Navigation:</strong> Click 'ניהול מתקדם' on dashboard</li>\n";
echo "<li><strong>Check Browser Console:</strong> Look for JavaScript errors</li>\n";
echo "<li><strong>Check WordPress Debug Log:</strong> Look for Budgex debug messages</li>\n";
echo "</ol>\n";
echo "</div>\n";

echo "<h2>🔍 Common Issues & Solutions</h2>\n";
echo "<table style='width: 100%; border-collapse: collapse;'>\n";
echo "<tr style='background: #f5f5f5;'><th style='padding: 10px; border: 1px solid #ddd;'>Issue</th><th style='padding: 10px; border: 1px solid #ddd;'>Solution</th></tr>\n";
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Empty page at /budgex/budget/7/</td><td style='padding: 10px; border: 1px solid #ddd;'>Apply Step 1 (functions.php fix) + flush permalinks</td></tr>\n";
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Button doesn't navigate</td><td style='padding: 10px; border: 1px solid #ddd;'>Apply Step 2 (enhanced button) + check CSS conflicts</td></tr>\n";
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>404 error</td><td style='padding: 10px; border: 1px solid #ddd;'>Flush permalinks in WordPress admin</td></tr>\n";
echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Still not working</td><td style='padding: 10px; border: 1px solid #ddd;'>Apply Step 4 (emergency template fix)</td></tr>\n";
echo "</table>\n";

echo "</div>\n";

echo "<div style='background: #ffebee; padding: 20px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #f44336;'>\n";
echo "<h2>⚠️ URGENT ACTION REQUIRED</h2>\n";
echo "<p><strong>Priority 1:</strong> Add the code from Step 1 to your theme's functions.php file immediately.</p>\n";
echo "<p><strong>Priority 2:</strong> Go to Settings → Permalinks and click Save Changes.</p>\n";
echo "<p><strong>Priority 3:</strong> Test the URLs manually before testing button navigation.</p>\n";
echo "</div>\n";
?>
