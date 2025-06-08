<?php
/**
 * Test Budget Data Object Fix
 * 
 * This script tests that budget data is now properly returned as objects
 * instead of arrays, which should fix the debug text issue in navigation buttons.
 */

// Include WordPress environment
$wp_load_path = dirname(__DIR__) . '/wp-load.php';
if (file_exists($wp_load_path)) {
    require_once $wp_load_path;
} else {
    echo "❌ WordPress not found. Please ensure this is in the correct directory.\n";
    exit;
}

// Include Budgex classes
require_once plugin_dir_path(__FILE__) . 'includes/class-database.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-budget-calculator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-permissions.php';

echo "<h1>🧪 Budget Data Object Fix Test</h1>\n";

try {
    // Test database class
    $database = new Budgex_Database();
    
    // Get current user
    $current_user_id = get_current_user_id();
    if (!$current_user_id) {
        echo "<p>❌ No user logged in. Please log in to test.</p>\n";
        exit;
    }
    
    echo "<h2>👤 Testing for User ID: $current_user_id</h2>\n";
    
    // Test get_user_budgets - should return objects now
    echo "<h3>📊 Testing get_user_budgets()</h3>\n";
    $budgets = $database->get_user_budgets($current_user_id);
    
    if (empty($budgets)) {
        echo "<p>⚠️ No budgets found for this user. Creating a test budget...</p>\n";
        
        // Create a test budget
        $test_budget_id = $database->add_budget(
            $current_user_id,
            'Test Budget - Object Fix',
            1000.00,
            'ILS',
            date('Y-m-d')
        );
        
        if ($test_budget_id) {
            echo "<p>✅ Test budget created with ID: $test_budget_id</p>\n";
            $budgets = $database->get_user_budgets($current_user_id);
        } else {
            echo "<p>❌ Failed to create test budget</p>\n";
            exit;
        }
    }
    
    echo "<p>📈 Found " . count($budgets) . " budget(s)</p>\n";
    
    // Test first budget
    if (!empty($budgets)) {
        $budget = $budgets[0];
        
        echo "<h4>🔍 Testing Budget Object Properties</h4>\n";
        echo "<ul>\n";
        
        // Test object access (should work now)
        if (isset($budget->id)) {
            echo "<li>✅ budget->id: " . $budget->id . "</li>\n";
        } else {
            echo "<li>❌ budget->id: NOT ACCESSIBLE</li>\n";
        }
        
        if (isset($budget->budget_name)) {
            echo "<li>✅ budget->budget_name: " . htmlspecialchars($budget->budget_name) . "</li>\n";
        } else {
            echo "<li>❌ budget->budget_name: NOT ACCESSIBLE</li>\n";
        }
        
        if (isset($budget->monthly_budget)) {
            echo "<li>✅ budget->monthly_budget: " . $budget->monthly_budget . "</li>\n";
        } else {
            echo "<li>❌ budget->monthly_budget: NOT ACCESSIBLE</li>\n";
        }
        
        if (isset($budget->currency)) {
            echo "<li>✅ budget->currency: " . $budget->currency . "</li>\n";
        } else {
            echo "<li>❌ budget->currency: NOT ACCESSIBLE</li>\n";
        }
        
        echo "</ul>\n";
        
        // Test that this works in HTML generation context
        echo "<h4>🏷️ Testing HTML Data Attribute Generation</h4>\n";
        
        $test_button_html = '<button type="button" class="button secondary manage-budget-btn" data-budget-id="' . 
                           esc_attr($budget->id) . '" data-budget-name="' . 
                           esc_attr($budget->budget_name) . '">' .
                           'Manage Budget</button>';
        
        echo "<p>Generated HTML:</p>\n";
        echo "<pre>" . htmlspecialchars($test_button_html) . "</pre>\n";
        
        echo "<p>Rendered button:</p>\n";
        echo $test_button_html . "\n";
        
        // Test get_budget function
        echo "<h4>🔎 Testing get_budget() function</h4>\n";
        $single_budget = $database->get_budget($budget->id);
        
        if ($single_budget && isset($single_budget->id)) {
            echo "<p>✅ get_budget() returns object with ID: " . $single_budget->id . "</p>\n";
        } else {
            echo "<p>❌ get_budget() failed to return proper object</p>\n";
        }
        
        // Test budget calculator
        echo "<h4>🧮 Testing Budget Calculator</h4>\n";
        $calculator = new Budgex_Budget_Calculator();
        $calculation = $calculator->calculate_remaining_budget($budget->id);
        
        if (is_array($calculation) && isset($calculation['total_available'])) {
            echo "<p>✅ Budget calculation successful:</p>\n";
            echo "<ul>\n";
            echo "<li>Total Available: " . $calculation['total_available'] . "</li>\n";
            echo "<li>Total Spent: " . $calculation['total_spent'] . "</li>\n";
            echo "<li>Remaining: " . $calculation['remaining'] . "</li>\n";
            echo "</ul>\n";
        } else {
            echo "<p>❌ Budget calculation failed</p>\n";
        }
    }
    
    echo "<h2>🎉 Test Complete</h2>\n";
    echo "<p>If you see ✅ for all budget properties above, the object access fix is working correctly and the debug text issue should be resolved.</p>\n";
    
} catch (Exception $e) {
    echo "<p>❌ Error during testing: " . $e->getMessage() . "</p>\n";
    echo "<p>Stack trace:</p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

h1, h2, h3, h4 {
    color: #333;
}

pre {
    background: #f5f5f5;
    padding: 10px;
    border-radius: 4px;
    overflow-x: auto;
}

ul {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 4px;
}

li {
    margin: 5px 0;
}

button {
    background: #0073aa;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background: #005a87;
}
</style>
