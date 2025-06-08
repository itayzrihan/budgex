<?php
/**
 * Frontend Budget Page Access Test
 * 
 * This file tests the complete frontend budget page access flow
 * to identify where the empty container issue occurs.
 */

// Start WordPress context
$wp_root = dirname(dirname(dirname(__FILE__)));
if (file_exists($wp_root . '/wp-load.php')) {
    require_once($wp_root . '/wp-load.php');
} else {
    echo "WordPress not found. Please place this in a WordPress installation.\n";
    exit;
}

echo "<h1>Frontend Budget Page Access Test</h1>\n";

// Test 1: Class availability
echo "<h2>1. Class Availability</h2>\n";
$classes = ['Budgex_Public', 'Budgex_Database', 'Budgex_Budget_Calculator', 'Budgex_Permissions'];
foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✓ $class - Available<br>\n";
    } else {
        echo "✗ $class - Missing<br>\n";
    }
}

// Test 2: Database connectivity
echo "<h2>2. Database Test</h2>\n";
if (class_exists('Budgex_Database')) {
    $database = new Budgex_Database();
    
    // Get first budget for testing
    global $wpdb;
    $budget_table = $wpdb->prefix . 'budgex_budgets';
    $test_budget = $wpdb->get_row("SELECT * FROM $budget_table LIMIT 1");
    
    if ($test_budget) {
        echo "✓ Test budget found: ID {$test_budget->id} - {$test_budget->budget_name}<br>\n";
        $test_budget_id = $test_budget->id;
        
        // Test data fetching
        $outcomes = $database->get_budget_outcomes($test_budget_id);
        echo "✓ Outcomes count: " . count($outcomes) . "<br>\n";
        
        $shared_users = $database->get_budget_shared_users($test_budget_id);
        echo "✓ Shared users count: " . count($shared_users) . "<br>\n";
        
    } else {
        echo "✗ No test budget found in database<br>\n";
        $test_budget_id = null;
    }
} else {
    echo "✗ Database class not available<br>\n";
    $test_budget_id = null;
}

// Test 3: Frontend display method
echo "<h2>3. Frontend Display Method Test</h2>\n";
if (class_exists('Budgex_Public') && $test_budget_id) {
    $public = new Budgex_Public();
    
    try {
        $output = $public->display_single_budget_frontend($test_budget_id);
        
        if ($output && strlen($output) > 100) {
            echo "✓ Frontend method returns content<br>\n";
            echo "✓ Output length: " . strlen($output) . " characters<br>\n";
            
            // Check for key elements
            $checks = [
                'budgex-enhanced-public-budget-page' => 'Main container',
                'enhanced-budget-header' => 'Header section',
                'enhanced-summary-dashboard' => 'Summary dashboard',
                'budget-tabs-container' => 'Tabs container',
                'tab-button' => 'Tab buttons',
                'overview-tab' => 'Overview tab'
            ];
            
            foreach ($checks as $search => $description) {
                if (strpos($output, $search) !== false) {
                    echo "✓ $description found<br>\n";
                } else {
                    echo "✗ $description missing<br>\n";
                }
            }
            
            // Check for error messages
            if (strpos($output, 'budgex-error') !== false) {
                echo "⚠ Error message found in output<br>\n";
            }
            
            // Show snippet of output
            echo "<h3>Output Preview (first 1000 chars):</h3>\n";
            echo "<pre>" . htmlspecialchars(substr($output, 0, 1000)) . "...</pre>\n";
            
        } else {
            echo "✗ Frontend method returns empty or minimal content<br>\n";
            if ($output) {
                echo "Full output: " . htmlspecialchars($output) . "<br>\n";
            }
        }
        
    } catch (Exception $e) {
        echo "✗ Frontend method error: " . $e->getMessage() . "<br>\n";
    }
} else {
    echo "✗ Cannot test frontend display (missing class or budget)<br>\n";
}

// Test 4: Asset enqueue test
echo "<h2>4. Asset Enqueue Test</h2>\n";
if (class_exists('Budgex_Public')) {
    $public = new Budgex_Public();
    
    // Simulate budget page conditions
    $_SERVER['REQUEST_URI'] = '/budgex/budget/1/';
    
    // Test style enqueuing
    $public->enqueue_styles();
    echo "✓ Styles enqueuing method called<br>\n";
    
    // Test script enqueuing
    $public->enqueue_scripts();
    echo "✓ Scripts enqueuing method called<br>\n";
    
    // Check if assets are registered
    global $wp_styles, $wp_scripts;
    
    if (wp_style_is('budgex-enhanced-budget', 'registered')) {
        echo "✓ Enhanced CSS is registered<br>\n";
    } else {
        echo "✗ Enhanced CSS not registered<br>\n";
    }
    
    if (wp_script_is('budgex-enhanced-budget', 'registered')) {
        echo "✓ Enhanced JS is registered<br>\n";
    } else {
        echo "✗ Enhanced JS not registered<br>\n";
    }
}

// Test 5: Template file test
echo "<h2>5. Template File Test</h2>\n";
$template_path = plugin_dir_path(__FILE__) . 'public/partials/budgex-public-enhanced-budget-page.php';
if (file_exists($template_path)) {
    echo "✓ Enhanced template exists<br>\n";
    echo "✓ File size: " . filesize($template_path) . " bytes<br>\n";
    
    // Check for critical template elements
    $template_content = file_get_contents($template_path);
    $critical_elements = [
        'budgex-enhanced-public-budget-page' => 'Main container class',
        'enhanced-budget-header' => 'Header section',
        'tab-button' => 'Tab navigation',
        'window.budgexData' => 'JavaScript data'
    ];
    
    foreach ($critical_elements as $element => $description) {
        if (strpos($template_content, $element) !== false) {
            echo "✓ $description found in template<br>\n";
        } else {
            echo "✗ $description missing from template<br>\n";
        }
    }
    
} else {
    echo "✗ Enhanced template not found at: $template_path<br>\n";
}

echo "<h2>Test Complete</h2>\n";
echo "Check the results above to identify any issues with the enhanced budget page rendering.<br>\n";
