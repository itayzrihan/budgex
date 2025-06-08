<?php
/**
 * Test script to validate budget calculation fixes
 * This script tests the budget calculation logic without requiring a full WordPress environment
 */

// Mock WordPress functions for testing
if (!function_exists('wp_die')) {
    function wp_die($message) {
        die($message);
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = '') {
        return htmlspecialchars($text);
    }
}

if (!function_exists('current_time')) {
    function current_time($type) {
        return ($type === 'mysql') ? date('Y-m-d H:i:s') : time();
    }
}

// Include the necessary files
require_once 'includes/class-budget-calculator.php';

// Test the budget calculation logic
echo "=== Testing Budget Calculation Logic ===\n\n";

// Test 1: Calculate total budget with adjustments for a budget started in the past
echo "Test 1: Budget started 3 months ago\n";
$start_date = date('Y-m-d', strtotime('-3 months'));
$additional_budget = 500.00;
$budget_id = 1;

try {
    $result = Budgex_Budget_Calculator::calculate_total_budget_with_adjustments($budget_id, $start_date, $additional_budget);
    echo "✓ Method call successful\n";
    echo "  Start Date: $start_date\n";
    echo "  Additional Budget: $additional_budget\n";
    echo "  Result type: " . gettype($result) . "\n";
    if (is_array($result)) {
        echo "  Result keys: " . implode(', ', array_keys($result)) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Calculate total budget for a current budget
echo "Test 2: Budget started this month\n";
$start_date = date('Y-m-01'); // First day of current month
$additional_budget = 200.00;

try {
    $result = Budgex_Budget_Calculator::calculate_total_budget_with_adjustments($budget_id, $start_date, $additional_budget);
    echo "✓ Method call successful\n";
    echo "  Start Date: $start_date\n";
    echo "  Additional Budget: $additional_budget\n";
    echo "  Result type: " . gettype($result) . "\n";
    if (is_array($result)) {
        echo "  Result keys: " . implode(', ', array_keys($result)) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Test remaining budget calculation method
echo "Test 3: Calculate remaining budget\n";
$mock_budget = [
    'id' => 1,
    'monthly_budget' => 1000.00,
    'start_date' => $start_date,
    'additional_budget' => 300.00
];

try {
    $result = Budgex_Budget_Calculator::calculate_remaining_budget($mock_budget);
    echo "✓ Remaining budget calculation successful\n";
    echo "  Result type: " . gettype($result) . "\n";
    if (is_array($result)) {
        echo "  Result keys: " . implode(', ', array_keys($result)) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
