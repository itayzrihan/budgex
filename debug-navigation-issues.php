<?php
/**
 * Debug Navigation Issues
 * Check URL routing, query vars, and template loading
 */

// Include WordPress
if (file_exists('../../../../wp-config.php')) {
    require_once '../../../../wp-config.php';
    require_once ABSPATH . 'wp-includes/wp-db.php';
}

// Include plugin files
require_once 'budgex.php';

echo "<h1>🔍 Navigation Debug Report</h1>\n";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>\n";

// Test 1: Check rewrite rules
echo "<h2>1. WordPress Rewrite Rules Check</h2>\n";
$rewrite_rules = get_option('rewrite_rules');
if ($rewrite_rules) {
    $budgex_rules = array_filter($rewrite_rules, function($key) {
        return strpos($key, 'budgex') !== false;
    }, ARRAY_FILTER_USE_KEY);
    
    if (!empty($budgex_rules)) {
        echo "✅ Budgex rewrite rules found:<br>\n";
        foreach ($budgex_rules as $pattern => $rewrite) {
            echo "&nbsp;&nbsp;- <code>$pattern</code> → <code>$rewrite</code><br>\n";
        }
    } else {
        echo "❌ No Budgex rewrite rules found!<br>\n";
        echo "<strong>This is likely the cause of the empty page issue.</strong><br>\n";
    }
} else {
    echo "❌ No rewrite rules found at all!<br>\n";
}

// Test 2: Check query vars
echo "<h2>2. Query Variables Registration</h2>\n";
global $wp_query;
$allowed_query_vars = $wp_query->query_vars;

$required_vars = ['budgex_page', 'budget_id'];
foreach ($required_vars as $var) {
    if (in_array($var, $allowed_query_vars)) {
        echo "✅ Query var '$var' is registered<br>\n";
    } else {
        echo "❌ Query var '$var' is NOT registered<br>\n";
    }
}

// Test 3: Simulate URL parsing
echo "<h2>3. URL Parsing Simulation</h2>\n";
$test_url = 'https://mytx.one/budgex/budget/7/';
echo "Testing URL: <code>$test_url</code><br>\n";

// Parse using WordPress URL parsing
$parsed = parse_url($test_url);
$path = $parsed['path'];
echo "Path: <code>$path</code><br>\n";

// Test if path matches our pattern
if (preg_match('#^/budgex/budget/(\d+)/?$#', $path, $matches)) {
    echo "✅ URL pattern matches! Budget ID: {$matches[1]}<br>\n";
} else {
    echo "❌ URL pattern does NOT match!<br>\n";
}

// Test 4: Check if plugin hooks are registered
echo "<h2>4. Plugin Hooks Registration</h2>\n";
global $wp_filter;

$hooks_to_check = [
    'init' => 'add_rewrite_rules',
    'template_include' => 'load_budgex_template',
    'query_vars' => 'add_query_vars'
];

foreach ($hooks_to_check as $hook => $method) {
    if (isset($wp_filter[$hook])) {
        $found = false;
        foreach ($wp_filter[$hook]->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                if (is_array($callback['function']) && 
                    method_exists($callback['function'][0], $method)) {
                    echo "✅ Hook '$hook' has '$method' registered<br>\n";
                    $found = true;
                    break 2;
                }
            }
        }
        if (!$found) {
            echo "❌ Hook '$hook' missing '$method' method<br>\n";
        }
    } else {
        echo "❌ Hook '$hook' not found<br>\n";
    }
}

// Test 5: Check template files
echo "<h2>5. Template Files Check</h2>\n";
$templates = [
    'budgex-app.php' => 'public/templates/budgex-app.php',
    'enhanced-budget-page.php' => 'public/partials/budgex-public-enhanced-budget-page.php',
    'dashboard.php' => 'public/partials/budgex-dashboard.php'
];

foreach ($templates as $name => $path) {
    if (file_exists($path)) {
        echo "✅ Template $name exists (" . number_format(filesize($path)) . " bytes)<br>\n";
    } else {
        echo "❌ Template $name missing at $path<br>\n";
    }
}

// Test 6: Database connection
echo "<h2>6. Database Connection</h2>\n";
if (class_exists('wpdb')) {
    global $wpdb;
    $test_query = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}budgex_budgets");
    if ($test_query !== null) {
        echo "✅ Database connection working. Found $test_query budgets<br>\n";
    } else {
        echo "❌ Database query failed or no budgets table<br>\n";
    }
} else {
    echo "❌ WordPress database not available<br>\n";
}

// Test 7: Suggested fixes
echo "<h2>7. 🔧 Suggested Fixes</h2>\n";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>\n";
echo "<h3>For Empty Page Issue:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Flush Rewrite Rules:</strong> Go to Settings → Permalinks and click Save</li>\n";
echo "<li><strong>Check Plugin Activation:</strong> Ensure Budgex plugin is fully activated</li>\n";
echo "<li><strong>Verify Template Loading:</strong> Check if budgex-app.php template is being called</li>\n";
echo "</ol>\n";

echo "<h3>For Link Navigation Issue:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Check JavaScript Errors:</strong> Open browser console when clicking links</li>\n";
echo "<li><strong>Verify URL Generation:</strong> Ensure home_url() generates correct URLs</li>\n";
echo "<li><strong>Test Direct URLs:</strong> Try accessing /budgex/ first, then navigate to budget</li>\n";
echo "</ol>\n";
echo "</div>\n";

// Test 8: Generate flush rules script
echo "<h2>8. 🚀 Quick Fix Script</h2>\n";
echo "<p>Run this to flush rewrite rules:</p>\n";
echo "<textarea style='width: 100%; height: 150px; font-family: monospace;'>\n";
echo "<?php\n";
echo "// Add to functions.php temporarily or run as separate script\n";
echo "add_action('init', function() {\n";
echo "    // Re-register rewrite rules\n";
echo "    add_rewrite_rule('^budgex/?$', 'index.php?budgex_page=dashboard', 'top');\n";
echo "    add_rewrite_rule('^budgex/budget/([0-9]+)/?$', 'index.php?budgex_page=budget&budget_id=\$matches[1]', 'top');\n";
echo "    \n";
echo "    // Flush rules\n";
echo "    flush_rewrite_rules();\n";
echo "    \n";
echo "    // Add query vars\n";
echo "    global \$wp;\n";
echo "    \$wp->add_query_var('budgex_page');\n";
echo "    \$wp->add_query_var('budget_id');\n";
echo "});\n";
echo "</textarea>\n";

echo "</div>\n";

echo "<script>console.log('Navigation Debug Complete');</script>\n";
?>
