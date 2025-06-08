<?php
/**
 * Debug URL Routing Issue
 * 
 * This script helps diagnose why the advanced management navigation
 * is redirecting to login instead of the budget page.
 */

// Simple debugging script to test URL routing
if (isset($_GET['test_url'])) {
    $test_url = $_GET['test_url'];
    echo "<h2>Testing URL: " . esc_html($test_url) . "</h2>\n";
    
    // Parse the URL to see what WordPress sees
    $parsed = parse_url($test_url);
    echo "<h3>Parsed URL Components:</h3>\n";
    echo "<pre>" . print_r($parsed, true) . "</pre>\n";
    
    // Check if this URL would match our rewrite rules
    $path = ltrim($parsed['path'] ?? '', '/');
    echo "<h3>Path: " . esc_html($path) . "</h3>\n";
    
    // Test our rewrite patterns
    $patterns = [
        '^budgex/?$' => 'budgex_page=dashboard',
        '^budgex/budget/([0-9]+)/?$' => 'budgex_page=budget&budget_id=$matches[1]',
        '^budgex/([^/]+)/?$' => 'budgex_page=$matches[1]'
    ];
    
    echo "<h3>Rewrite Rule Testing:</h3>\n";
    foreach ($patterns as $pattern => $rewrite) {
        $pattern_test = str_replace(['^', '$'], ['', ''], $pattern);
        if (preg_match('/' . $pattern . '/', $path, $matches)) {
            echo "✅ MATCHES: " . $pattern . " → " . $rewrite . "\n";
            if (isset($matches[1])) {
                echo "   Budget ID captured: " . $matches[1] . "\n";
            }
        } else {
            echo "❌ No match: " . $pattern . "\n";
        }
    }
    
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Budgex URL Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-url { margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Budgex URL Routing Debug Tool</h1>
    
    <p>Test various URLs to see how they would be processed by WordPress rewrite rules:</p>
    
    <div class="test-url">
        <strong>Dashboard URL:</strong>
        <a href="?test_url=https://mytx.one/budgex/">https://mytx.one/budgex/</a>
    </div>
    
    <div class="test-url">
        <strong>Budget Page URL:</strong>
        <a href="?test_url=https://mytx.one/budgex/budget/123/">https://mytx.one/budgex/budget/123/</a>
    </div>
    
    <div class="test-url">
        <strong>Budget Management URL (the problematic one):</strong>
        <a href="?test_url=https://mytx.one/budgex/budget/123/#advanced-management-panel">https://mytx.one/budgex/budget/123/#advanced-management-panel</a>
    </div>
    
    <h2>Current WordPress Rewrite Rules</h2>
    <?php
    if (function_exists('get_option')) {
        $rewrite_rules = get_option('rewrite_rules');
        if ($rewrite_rules) {
            echo "<pre>";
            foreach ($rewrite_rules as $rule => $rewrite) {
                if (strpos($rule, 'budgex') !== false) {
                    echo "Rule: " . $rule . " → " . $rewrite . "\n";
                }
            }
            echo "</pre>";
        } else {
            echo "<p class='error'>No rewrite rules found. This could be the issue!</p>";
        }
    } else {
        echo "<p>WordPress functions not available. Please run this from within WordPress.</p>";
    }
    ?>
    
    <h2>Quick Fixes to Try</h2>
    <ol>
        <li><strong>Flush Rewrite Rules:</strong> Go to WordPress Admin → Settings → Permalinks and click "Save Changes"</li>
        <li><strong>Check Plugin Activation:</strong> Make sure Budgex plugin is properly activated</li>
        <li><strong>Verify User Login:</strong> Ensure you're actually logged in to WordPress</li>
        <li><strong>Check User Permissions:</strong> Verify you have access to the specific budget</li>
    </ol>
    
    <h2>Manual Test URLs</h2>
    <p>Try these URLs manually in your browser:</p>
    <ul>
        <li><code>https://mytx.one/budgex/</code> - Should show dashboard</li>
        <li><code>https://mytx.one/budgex/budget/1/</code> - Should show budget #1 (replace with real ID)</li>
    </ul>
</body>
</html>
