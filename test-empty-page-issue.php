<?php
/**
 * Test Empty Page Issue
 * Quick diagnostic to see what's happening with budget page display
 */

// Include WordPress if available
if (file_exists('../../../../wp-config.php')) {
    require_once '../../../../wp-config.php';
}

echo "<h1>🔍 Empty Page Diagnostic</h1>\n";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>\n";

// Test 1: Check if classes exist
echo "<h2>1. Class Availability Check</h2>\n";

try {
    require_once 'budgex.php';
    
    $budgex = new Budgex();
    $public = new Budgex_Public();
    
    echo "✅ Classes loaded successfully<br>\n";
    echo "✅ Budgex class instantiated<br>\n";
    echo "✅ Budgex_Public class instantiated<br>\n";
} catch (Exception $e) {
    echo "❌ Error loading classes: " . $e->getMessage() . "<br>\n";
    exit;
}

// Test 2: Simulate query variables
echo "<h2>2. Simulating Budget Page Request</h2>\n";

// Simulate the URL /budgex/budget/7/
set_query_var('budgex_page', 'budget');
set_query_var('budget_id', '7');

echo "✅ Query vars set: budgex_page=budget, budget_id=7<br>\n";

// Test 3: Test the main display method
echo "<h2>3. Testing Main Display Method</h2>\n";

try {
    $output = $budgex->display_budgex_app();
    
    if (empty($output)) {
        echo "❌ Display method returned empty content<br>\n";
        echo "Content length: " . strlen($output) . " characters<br>\n";
    } else {
        echo "✅ Display method returned content<br>\n";
        echo "Content length: " . strlen($output) . " characters<br>\n";
        echo "First 200 characters:<br>\n";
        echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0; border: 1px solid #ddd;'>";
        echo htmlspecialchars(substr($output, 0, 200)) . "...";
        echo "</div>\n";
    }
} catch (Exception $e) {
    echo "❌ Error in display method: " . $e->getMessage() . "<br>\n";
}

// Test 4: Test the budget display method directly
echo "<h2>4. Testing Budget Display Method Directly</h2>\n";

try {
    // Simulate being logged in for testing
    if (!is_user_logged_in() && function_exists('wp_set_current_user')) {
        wp_set_current_user(1); // Assuming user ID 1 exists
        echo "✅ Simulated user login (ID: 1)<br>\n";
    }
    
    $budget_output = $public->display_single_budget_frontend(7);
    
    if (empty($budget_output)) {
        echo "❌ Budget display method returned empty content<br>\n";
    } else {
        echo "✅ Budget display method returned content<br>\n";
        echo "Content length: " . strlen($budget_output) . " characters<br>\n";
        echo "First 300 characters:<br>\n";
        echo "<div style='background: #f0f8ff; padding: 10px; margin: 10px 0; border: 1px solid #4CAF50;'>";
        echo htmlspecialchars(substr($budget_output, 0, 300)) . "...";
        echo "</div>\n";
    }
} catch (Exception $e) {
    echo "❌ Error in budget display method: " . $e->getMessage() . "<br>\n";
}

// Test 5: Check template existence
echo "<h2>5. Template File Check</h2>\n";

$template_path = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';
if (file_exists($template_path)) {
    echo "✅ Enhanced template exists<br>\n";
    echo "Path: $template_path<br>\n";
    echo "File size: " . number_format(filesize($template_path)) . " bytes<br>\n";
} else {
    echo "❌ Enhanced template NOT found at: $template_path<br>\n";
}

// Test 6: Direct template test
echo "<h2>6. Direct Template Test</h2>\n";

if (file_exists($template_path)) {
    // Set up minimal variables
    $budget = (object) [
        'id' => 7,
        'budget_name' => 'Test Budget',
        'monthly_budget' => 5000,
        'currency' => 'ILS',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $calculation = [
        'total_available' => 5000,
        'total_spent' => 1500,
        'remaining' => 3500,
        'budget_details' => [
            'monthly_budget' => 5000,
            'additional_budget' => 0
        ]
    ];
    
    $outcomes = [];
    $user_role = 'owner';
    
    try {
        ob_start();
        include $template_path;
        $template_output = ob_get_clean();
        
        if (empty($template_output)) {
            echo "❌ Template produced no output<br>\n";
        } else {
            echo "✅ Template produced output<br>\n";
            echo "Content length: " . strlen($template_output) . " characters<br>\n";
            echo "Contains HTML? " . (strpos($template_output, '<div') !== false ? 'Yes' : 'No') . "<br>\n";
        }
    } catch (Exception $e) {
        echo "❌ Template error: " . $e->getMessage() . "<br>\n";
    }
} else {
    echo "❌ Cannot test template - file not found<br>\n";
}

echo "</div>\n";

echo "<script>console.log('Empty page diagnostic complete');</script>\n";
?>
