<?php
/**
 * Test Navigation Flow - Advanced Management Panel
 * 
 * This test validates the complete navigation flow from dashboard
 * to budget page with advanced management panel functionality.
 */

if (!defined('ABSPATH')) {
    exit;
}

function test_navigation_flow() {
    echo "<h2>Testing Advanced Management Panel Navigation Flow</h2>\n";
    
    $results = [];
    
    // Test 1: Check if JavaScript file contains required functions
    $js_file = BUDGEX_PLUGIN_PATH . 'public/js/budgex-public.js';
    if (file_exists($js_file)) {
        $js_content = file_get_contents($js_file);
        
        $required_functions = [
            'handleAdvancedManagementToggle',
            'handleAdvancedManagementAnchor'
        ];
        
        foreach ($required_functions as $function) {
            if (strpos($js_content, $function) !== false) {
                $results[] = "✅ JavaScript function '$function' found";
            } else {
                $results[] = "❌ JavaScript function '$function' missing";
            }
        }
        
        // Check for event binding
        if (strpos($js_content, '#toggle-management-panel') !== false) {
            $results[] = "✅ Toggle button event binding found";
        } else {
            $results[] = "❌ Toggle button event binding missing";
        }
        
        // Check for anchor handling
        if (strpos($js_content, '#advanced-management-panel') !== false) {
            $results[] = "✅ Anchor link handling found";
        } else {
            $results[] = "❌ Anchor link handling missing";
        }
    } else {
        $results[] = "❌ JavaScript file not found";
    }
    
    // Test 2: Check if CSS contains active state styles
    $css_file = BUDGEX_PLUGIN_PATH . 'public/css/budgex-public.css';
    if (file_exists($css_file)) {
        $css_content = file_get_contents($css_file);
        
        if (strpos($css_content, '.toggle-all-management.active') !== false) {
            $results[] = "✅ Toggle button active state CSS found";
        } else {
            $results[] = "❌ Toggle button active state CSS missing";
        }
        
        if (strpos($css_content, '.advanced-management-panel') !== false) {
            $results[] = "✅ Panel animation CSS found";
        } else {
            $results[] = "❌ Panel animation CSS missing";
        }
    } else {
        $results[] = "❌ CSS file not found";
    }
    
    // Test 3: Check if budget page template has correct structure
    $template_file = BUDGEX_PLUGIN_PATH . 'public/partials/budgex-budget-page.php';
    if (file_exists($template_file)) {
        $template_content = file_get_contents($template_file);
        
        if (strpos($template_content, 'id="toggle-management-panel"') !== false) {
            $results[] = "✅ Toggle button with correct ID found in template";
        } else {
            $results[] = "❌ Toggle button with correct ID missing in template";
        }
        
        if (strpos($template_content, 'id="advanced-management-panel"') !== false) {
            $results[] = "✅ Advanced management panel with correct ID found";
        } else {
            $results[] = "❌ Advanced management panel with correct ID missing";
        }
        
        if (strpos($template_content, 'style="display: none;"') !== false) {
            $results[] = "✅ Panel is initially hidden";
        } else {
            $results[] = "❌ Panel initial state not properly set";
        }
    } else {
        $results[] = "❌ Budget page template not found";
    }
    
    // Test 4: Check dashboard navigation URLs
    $dashboard_file = BUDGEX_PLUGIN_PATH . 'public/partials/budgex-dashboard.php';
    if (file_exists($dashboard_file)) {
        $dashboard_content = file_get_contents($dashboard_file);
        
        if (strpos($dashboard_content, "budgex_ajax.budget_url + budgetId + '/#advanced-management-panel'") !== false) {
            $results[] = "✅ Manage budget button uses correct URL with anchor";
        } else {
            $results[] = "❌ Manage budget button URL structure incorrect";
        }
        
        if (strpos($dashboard_content, "home_url('/budgex/budget/' . \$budget->id . '/')") !== false) {
            $results[] = "✅ View budget button uses correct URL structure";
        } else {
            $results[] = "❌ View budget button URL structure incorrect";
        }
    } else {
        $results[] = "❌ Dashboard template not found";
    }
    
    // Test 5: Check rewrite rules
    $budgex_class_file = BUDGEX_PLUGIN_PATH . 'includes/class-budgex.php';
    if (file_exists($budgex_class_file)) {
        $budgex_content = file_get_contents($budgex_class_file);
        
        if (strpos($budgex_content, "'^budgex/budget/([0-9]+)/?$'") !== false) {
            $results[] = "✅ Budget page rewrite rule found";
        } else {
            $results[] = "❌ Budget page rewrite rule missing or incorrect";
        }
    } else {
        $results[] = "❌ Main Budgex class file not found";
    }
    
    // Display results
    foreach ($results as $result) {
        echo $result . "\n";
    }
    
    // Summary
    $success_count = count(array_filter($results, function($r) { return strpos($r, '✅') === 0; }));
    $total_count = count($results);
    
    echo "\n<h3>Summary: $success_count/$total_count tests passed</h3>\n";
    
    if ($success_count === $total_count) {
        echo "<p style='color: green; font-weight: bold;'>🎉 All navigation components are properly implemented!</p>\n";
        echo "<p><strong>Navigation Flow:</strong></p>\n";
        echo "<ol>\n";
        echo "<li>User clicks 'ניהול מתקדם' button on dashboard</li>\n";
        echo "<li>JavaScript navigates to: /budgex/budget/{ID}/#advanced-management-panel</li>\n";
        echo "<li>Budget page loads and checks for anchor in URL</li>\n";
        echo "<li>JavaScript automatically shows the advanced management panel</li>\n";
        echo "<li>User can also manually toggle the panel with the toggle button</li>\n";
        echo "</ol>\n";
    } else {
        echo "<p style='color: orange; font-weight: bold;'>⚠️ Some issues found. Please review the failed tests above.</p>\n";
    }
}

// Run the test
test_navigation_flow();
?>
