<?php
/**
 * Debug Empty Page Issue
 * Check why the budget page is showing empty content
 */

// Include WordPress if available
if (file_exists('../../../../wp-config.php')) {
    require_once '../../../../wp-config.php';
}

echo "<h1>🔍 Debug Empty Page Issue</h1>\n";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>\n";

// Test 1: Check if we can simulate the request
echo "<h2>1. Simulating Budget Page Request</h2>\n";

// Simulate query vars that would be set for /budgex/budget/7/
$_GET['budgex_page'] = 'budget';
$_GET['budget_id'] = '7';

// Include plugin files
require_once 'budgex.php';
require_once 'includes/class-budgex.php';

try {
    $budgex = new Budgex();
    echo "✅ Budgex class instantiated successfully<br>\n";
} catch (Exception $e) {
    echo "❌ Error creating Budgex class: " . $e->getMessage() . "<br>\n";
}

// Test 2: Check display_budgex_app method
echo "<h2>2. Testing display_budgex_app Method</h2>\n";
try {
    ob_start();
    $content = $budgex->display_budgex_app();
    $buffer = ob_get_clean();
    
    if (!empty($content)) {
        echo "✅ Method returned content (" . strlen($content) . " characters)<br>\n";
        echo "<strong>Content preview:</strong><br>\n";
        echo "<div style='background: #f9f9f9; padding: 10px; max-height: 200px; overflow-y: auto;'>\n";
        echo htmlentities(substr($content, 0, 500)) . "...\n";
        echo "</div>\n";
    } else {
        echo "❌ Method returned empty content<br>\n";
        if (!empty($buffer)) {
            echo "⚠️ But there was output buffer content:<br>\n";
            echo "<div style='background: #fff3cd; padding: 10px;'>" . htmlentities($buffer) . "</div>\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error calling display_budgex_app: " . $e->getMessage() . "<br>\n";
}

// Test 3: Check if user authentication is the issue
echo "<h2>3. Authentication Check</h2>\n";
if (function_exists('is_user_logged_in')) {
    if (is_user_logged_in()) {
        echo "✅ User is logged in<br>\n";
        $user_id = get_current_user_id();
        echo "User ID: $user_id<br>\n";
    } else {
        echo "❌ User is NOT logged in - this could cause empty content<br>\n";
    }
} else {
    echo "⚠️ WordPress authentication functions not available<br>\n";
}

// Test 4: Check public class methods
echo "<h2>4. Public Class Method Check</h2>\n";
try {
    require_once 'public/class-budgex-public.php';
    $public = new Budgex_Public();
    echo "✅ Budgex_Public class instantiated<br>\n";
    
    if (method_exists($public, 'display_single_budget_frontend')) {
        echo "✅ display_single_budget_frontend method exists<br>\n";
        
        // Try to call it directly
        try {
            ob_start();
            $result = $public->display_single_budget_frontend(7);
            $buffer = ob_get_clean();
            
            if (!empty($result)) {
                echo "✅ Method returned content (" . strlen($result) . " characters)<br>\n";
            } else {
                echo "❌ Method returned empty result<br>\n";
                if (!empty($buffer)) {
                    echo "Buffer content: " . htmlentities(substr($buffer, 0, 200)) . "<br>\n";
                }
            }
        } catch (Exception $e) {
            echo "❌ Error calling method: " . $e->getMessage() . "<br>\n";
        }
    } else {
        echo "❌ display_single_budget_frontend method missing<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Error with public class: " . $e->getMessage() . "<br>\n";
}

// Test 5: Check template file existence
echo "<h2>5. Template File Check</h2>\n";
$template_path = 'public/partials/budgex-public-enhanced-budget-page.php';
if (file_exists($template_path)) {
    echo "✅ Enhanced template exists (" . number_format(filesize($template_path)) . " bytes)<br>\n";
    
    // Check if template has content
    $template_content = file_get_contents($template_path);
    if (strlen($template_content) > 1000) {
        echo "✅ Template has substantial content<br>\n";
    } else {
        echo "⚠️ Template seems small (" . strlen($template_content) . " chars)<br>\n";
    }
} else {
    echo "❌ Enhanced template missing at: $template_path<br>\n";
}

// Test 6: Check database connection
echo "<h2>6. Database Connection Check</h2>\n";
try {
    require_once 'includes/class-database.php';
    $database = new Budgex_Database();
    echo "✅ Database class instantiated<br>\n";
    
    // Try to get a budget
    if (method_exists($database, 'get_budget')) {
        try {
            $budget = $database->get_budget(7);
            if ($budget) {
                echo "✅ Budget #7 found in database<br>\n";
                echo "Budget name: " . htmlentities($budget->name ?? 'N/A') . "<br>\n";
            } else {
                echo "❌ Budget #7 not found in database<br>\n";
            }
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Database class error: " . $e->getMessage() . "<br>\n";
}

echo "</div>\n";

// Generate quick fix
echo "<div style='background: #e8f5e8; padding: 20px; margin: 20px 0; border-radius: 5px;'>\n";
echo "<h2>🔧 Quick Fix for Empty Page</h2>\n";
echo "<p>Based on the analysis, try this emergency fix in your functions.php:</p>\n";
echo "<textarea style='width: 100%; height: 200px; font-family: monospace;'>\n";
echo "// EMERGENCY FIX FOR EMPTY BUDGET PAGE\n";
echo "add_action('template_redirect', function() {\n";
echo "    if (get_query_var('budgex_page') === 'budget') {\n";
echo "        \$budget_id = get_query_var('budget_id');\n";
echo "        if (\$budget_id && is_user_logged_in()) {\n";
echo "            // Force load the enhanced budget page\n";
echo "            include_once WP_PLUGIN_DIR . '/budgex/public/partials/budgex-public-enhanced-budget-page.php';\n";
echo "            exit;\n";
echo "        }\n";
echo "    }\n";
echo "});\n";
echo "</textarea>\n";
echo "</div>\n";

echo "<script>console.log('Empty page debug complete');</script>\n";
?>
