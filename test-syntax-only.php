<?php
/**
 * Simple syntax check for monthly budget increase functionality
 */

// Test that all classes can be loaded without WordPress
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock WordPress functions that our classes use
if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return 1;
    }
}

if (!function_exists('current_time')) {
    function current_time($type = 'timestamp') {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        echo json_encode(['success' => false, 'data' => $data]);
        exit;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(strip_tags($str));
    }
}

if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action = -1, $query_arg = false, $die = true) {
        return true;
    }
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

// Test Budget Calculator class (our main new functionality)
echo "Testing Budget Calculator class...\n";
require_once 'includes/class-budget-calculator.php';

// Test that the class can be instantiated
try {
    $calculator = new Budgex_Budget_Calculator();
    echo "✓ Budget Calculator class loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading Budget Calculator: " . $e->getMessage() . "\n";
}

// Test Database class (contains our new methods)
echo "\nTesting Database class...\n";
require_once 'includes/class-database.php';

try {
    // Create a mock wpdb object
    $mock_wpdb = new stdClass();
    $mock_wpdb->prefix = 'wp_';
    
    // We can't fully test without database, but we can check syntax
    echo "✓ Database class loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading Database class: " . $e->getMessage() . "\n";
}

echo "\nSyntax check complete! Core functionality classes are loadable.\n";

// Test the method signatures exist
echo "\nChecking method signatures...\n";

$reflection = new ReflectionClass('Budgex_Budget_Calculator');
$methods = $reflection->getMethods();

$expected_methods = [
    'calculate_total_budget_with_adjustments',
    'get_monthly_breakdown'
];

foreach ($expected_methods as $method_name) {
    if ($reflection->hasMethod($method_name)) {
        echo "✓ Method {$method_name} exists in Budget Calculator\n";
    } else {
        echo "✗ Method {$method_name} missing in Budget Calculator\n";
    }
}

$db_reflection = new ReflectionClass('Budgex_Database');
$expected_db_methods = [
    'add_budget_adjustment',
    'get_budget_adjustments',
    'get_monthly_budget_for_date'
];

foreach ($expected_db_methods as $method_name) {
    if ($db_reflection->hasMethod($method_name)) {
        echo "✓ Method {$method_name} exists in Database class\n";
    } else {
        echo "✗ Method {$method_name} missing in Database class\n";
    }
}

echo "\nAll tests completed!\n";
?>
