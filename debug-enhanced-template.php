<?php
/**
 * Debug: Test Enhanced Budget Page Template
 * 
 * This file tests if the enhanced budget page template loads properly
 * and identifies any issues with the template rendering.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

echo "<h1>Enhanced Budget Page Template Debug</h1>\n";

// Check if the enhanced template file exists
$template_path = plugin_dir_path(__FILE__) . 'public/partials/budgex-public-enhanced-budget-page.php';
echo "<h2>Template File Check</h2>\n";
if (file_exists($template_path)) {
    echo "✓ Enhanced template file exists at: {$template_path}<br>\n";
    echo "✓ File size: " . filesize($template_path) . " bytes<br>\n";
} else {
    echo "✗ Enhanced template file NOT found at: {$template_path}<br>\n";
}

// Check CSS file
$css_path = plugin_dir_path(__FILE__) . 'public/css/budgex-enhanced-budget.css';
echo "<h2>CSS File Check</h2>\n";
if (file_exists($css_path)) {
    echo "✓ Enhanced CSS file exists at: {$css_path}<br>\n";
    echo "✓ File size: " . filesize($css_path) . " bytes<br>\n";
} else {
    echo "✗ Enhanced CSS file NOT found at: {$css_path}<br>\n";
}

// Check JS file  
$js_path = plugin_dir_path(__FILE__) . 'public/js/budgex-enhanced-budget.js';
echo "<h2>JavaScript File Check</h2>\n";
if (file_exists($js_path)) {
    echo "✓ Enhanced JS file exists at: {$js_path}<br>\n";
    echo "✓ File size: " . filesize($js_path) . " bytes<br>\n";
} else {
    echo "✗ Enhanced JS file NOT found at: {$js_path}<br>\n";
}

// Check if main classes exist
echo "<h2>Class Availability Check</h2>\n";
if (class_exists('Budgex_Public')) {
    echo "✓ Budgex_Public class exists<br>\n";
} else {
    echo "✗ Budgex_Public class NOT found<br>\n";
}

if (class_exists('Budgex_Database')) {
    echo "✓ Budgex_Database class exists<br>\n";
} else {
    echo "✗ Budgex_Database class NOT found<br>\n";
}

if (class_exists('Budgex_Budget_Calculator')) {
    echo "✓ Budgex_Budget_Calculator class exists<br>\n";
} else {
    echo "✗ Budgex_Budget_Calculator class NOT found<br>\n";
}

// Check current URL and query vars
echo "<h2>URL and Query Variables</h2>\n";
echo "Current REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>\n";
echo "budgex_page query var: " . get_query_var('budgex_page', 'not set') . "<br>\n";
echo "budget_id query var: " . get_query_var('budget_id', 'not set') . "<br>\n";

// Check if enhanced assets are enqueued
echo "<h2>Asset Enqueue Status</h2>\n";
global $wp_styles, $wp_scripts;

if (wp_style_is('budgex-enhanced-budget', 'enqueued')) {
    echo "✓ Enhanced CSS is enqueued<br>\n";
} else {
    echo "✗ Enhanced CSS is NOT enqueued<br>\n";
}

if (wp_script_is('budgex-enhanced-budget', 'enqueued')) {
    echo "✓ Enhanced JS is enqueued<br>\n";
} else {
    echo "✗ Enhanced JS is NOT enqueued<br>\n";
}

// Test template rendering with sample data
echo "<h2>Template Rendering Test</h2>\n";
echo "Testing template rendering with sample data...<br>\n";

// Mock data for template test
$budget = (object) [
    'id' => 1,
    'budget_name' => 'Test Budget',
    'monthly_budget' => 5000,
    'currency' => 'ILS',
    'start_date' => '2024-01-01'
];

$calculation = [
    'total_available' => 5000,
    'total_spent' => 2000,
    'remaining' => 3000,
    'budget_details' => [
        'monthly_budget' => 5000,
        'additional_budget' => 0
    ]
];

$outcomes = [
    (object) [
        'outcome_name' => 'Test Expense',
        'amount' => 500,
        'outcome_date' => '2024-01-15'
    ]
];

$user_role = 'owner';
$shared_users = [];
$pending_invitations = [];
$future_expenses = [];
$recurring_expenses = [];
$monthly_breakdown = [];
$budget_adjustments = [];

try {
    ob_start();
    include $template_path;
    $template_output = ob_get_clean();
    
    if (strlen($template_output) > 0) {
        echo "✓ Template rendered successfully<br>\n";
        echo "✓ Output length: " . strlen($template_output) . " characters<br>\n";
        
        // Check for key elements in the output
        if (strpos($template_output, 'budgex-enhanced-public-budget-page') !== false) {
            echo "✓ Main container class found in output<br>\n";
        } else {
            echo "✗ Main container class NOT found in output<br>\n";
        }
        
        if (strpos($template_output, 'Test Budget') !== false) {
            echo "✓ Budget name rendered correctly<br>\n";
        } else {
            echo "✗ Budget name NOT found in output<br>\n";
        }
        
        // Show first 500 characters of output
        echo "<h3>Template Output Preview:</h3>\n";
        echo "<pre>" . htmlspecialchars(substr($template_output, 0, 500)) . "...</pre>\n";
        
    } else {
        echo "✗ Template rendered but output is empty<br>\n";
    }
    
} catch (Exception $e) {
    echo "✗ Template rendering failed: " . $e->getMessage() . "<br>\n";
}

echo "<h2>Debug Complete</h2>\n";
echo "If you see issues above, they indicate why the enhanced budget page might appear empty.<br>\n";
