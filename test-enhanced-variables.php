<?php
/**
 * Simple test to verify enhanced budget template variables
 */

// Activate WordPress context
define('WP_USE_THEMES', false);
require_once('../../../../wp-load.php');

echo "<h1>Enhanced Budget Template Variable Test</h1>\n";

// Test if the enhanced template can be loaded with proper variables
if (class_exists('Budgex_Public')) {
    $public = new Budgex_Public();
    
    echo "<h2>Testing Template Variable Requirements</h2>\n";
    
    // Mock the required variables
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
    
    // Test the template
    $template_path = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';
    
    if (file_exists($template_path)) {
        echo "✓ Template file found<br>\n";
        
        // Capture any PHP errors
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        ob_start();
        try {
            include $template_path;
            $output = ob_get_clean();
            
            if ($output && strlen($output) > 100) {
                echo "✓ Template rendered successfully<br>\n";
                echo "✓ Output length: " . strlen($output) . " characters<br>\n";
                
                // Check for key elements
                if (strpos($output, 'budgex-enhanced-public-budget-page') !== false) {
                    echo "✓ Main container found<br>\n";
                } else {
                    echo "✗ Main container missing<br>\n";
                }
                
                if (strpos($output, 'Test Budget') !== false) {
                    echo "✓ Budget name rendered<br>\n";
                } else {
                    echo "✗ Budget name missing<br>\n";
                }
                
                // Show first part of output
                echo "<h3>Template Output (first 800 chars):</h3>\n";
                echo "<pre>" . htmlspecialchars(substr($output, 0, 800)) . "</pre>\n";
                
            } else {
                echo "✗ Template output is empty or too short<br>\n";
                if ($output) {
                    echo "Output: " . htmlspecialchars($output) . "<br>\n";
                }
            }
            
        } catch (Exception $e) {
            ob_end_clean();
            echo "✗ Template error: " . $e->getMessage() . "<br>\n";
        }
        
    } else {
        echo "✗ Template file not found at: $template_path<br>\n";
    }
    
} else {
    echo "✗ Budgex_Public class not found<br>\n";
}

echo "<h2>PHP Error Check</h2>\n";
$error_log = error_get_last();
if ($error_log) {
    echo "Last PHP error: " . print_r($error_log, true) . "<br>\n";
} else {
    echo "✓ No PHP errors detected<br>\n";
}
