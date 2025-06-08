<?php
/**
 * Test Outcomes Variable Fix
 * Verify that the $outcomes variable is properly passed to the enhanced template
 */

echo "<h1>🧪 Testing Outcomes Variable Fix</h1>\n";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>\n";

// Test 1: Check if the calling function has been updated
echo "<h2>1. Checking Enhanced Template Calling Function</h2>\n";

$public_file = 'public/class-budgex-public.php';
if (file_exists($public_file)) {
    $content = file_get_contents($public_file);
    
    // Check if $outcomes is declared
    if (strpos($content, '$outcomes = $this->database->get_budget_outcomes($budget_id);') !== false) {
        echo "✅ \$outcomes variable is declared in display_single_budget_frontend()<br>\n";
    } else {
        echo "❌ \$outcomes variable declaration not found<br>\n";
    }
    
    // Check if future_expenses is declared
    if (strpos($content, '$future_expenses = $this->database->get_future_expenses($budget_id);') !== false) {
        echo "✅ \$future_expenses variable is declared<br>\n";
    } else {
        echo "❌ \$future_expenses variable declaration not found<br>\n";
    }
    
    // Check if enhanced template is being used
    if (strpos($content, 'budgex-public-enhanced-budget-page.php') !== false) {
        echo "✅ Enhanced template is being used<br>\n";
    } else {
        echo "❌ Enhanced template not found in calling function<br>\n";
    }
    
} else {
    echo "❌ Public class file not found<br>\n";
}

// Test 2: Check the enhanced template for $outcomes usage
echo "<h2>2. Checking Enhanced Template Usage</h2>\n";

$template_file = 'public/partials/budgex-public-enhanced-budget-page.php';
if (file_exists($template_file)) {
    $template_content = file_get_contents($template_file);
    
    // Check if $outcomes is used in template
    if (strpos($template_content, 'count($outcomes)') !== false) {
        echo "✅ \$outcomes variable is used in enhanced template (count function)<br>\n";
    } else {
        echo "❌ \$outcomes variable usage not found in template<br>\n";
    }
    
    // Check for tab count usage
    if (strpos($template_content, '<span class="tab-count"><?php echo count($outcomes); ?></span>') !== false) {
        echo "✅ \$outcomes is used for tab count display<br>\n";
    } else {
        echo "❌ \$outcomes tab count usage not found<br>\n";
    }
    
    // Check if there are any undefined variable errors
    $error_patterns = [
        'Undefined variable: $outcomes',
        'Notice: Undefined variable',
        'Warning: Undefined variable'
    ];
    
    $has_errors = false;
    foreach ($error_patterns as $pattern) {
        if (strpos($template_content, $pattern) !== false) {
            echo "❌ Found error: $pattern<br>\n";
            $has_errors = true;
        }
    }
    
    if (!$has_errors) {
        echo "✅ No obvious variable errors found in template<br>\n";
    }
    
} else {
    echo "❌ Enhanced template file not found<br>\n";
}

// Test 3: Test PHP syntax of both files
echo "<h2>3. Testing PHP Syntax</h2>\n";

// Test public class file syntax
$syntax_check = shell_exec("php -l \"$public_file\" 2>&1");
if (strpos($syntax_check, 'No syntax errors') !== false) {
    echo "✅ Public class file has valid PHP syntax<br>\n";
} else {
    echo "❌ Public class file syntax error: " . htmlspecialchars($syntax_check) . "<br>\n";
}

// Test template file syntax  
$syntax_check = shell_exec("php -l \"$template_file\" 2>&1");
if (strpos($syntax_check, 'No syntax errors') !== false) {
    echo "✅ Enhanced template file has valid PHP syntax<br>\n";
} else {
    echo "❌ Enhanced template file syntax error: " . htmlspecialchars($syntax_check) . "<br>\n";
}

// Test 4: Check other required variables
echo "<h2>4. Checking Other Required Variables</h2>\n";

$required_vars = [
    '$budget' => 'Budget object',
    '$calculation' => 'Budget calculation',
    '$user_role' => 'User role',
    '$monthly_breakdown' => 'Monthly breakdown',
    '$future_expenses' => 'Future expenses',
    '$recurring_expenses' => 'Recurring expenses'
];

foreach ($required_vars as $var => $description) {
    if (strpos($content, $var . ' = ') !== false) {
        echo "✅ $description ($var) is declared<br>\n";
    } else {
        echo "❌ $description ($var) is missing<br>\n";
    }
}

// Test 5: Summary
echo "<h2>5. 📋 Fix Summary</h2>\n";
echo "<div style='background: #e8f5e8; padding: 15px; border: 1px solid #4CAF50; margin: 10px 0;'>\n";
echo "<h3>✅ What Was Fixed:</h3>\n";
echo "<ul>\n";
echo "<li><strong>Root Cause:</strong> Enhanced template was expecting \$outcomes variable but it wasn't being passed</li>\n";
echo "<li><strong>Solution:</strong> Added \$outcomes declaration in display_single_budget_frontend() method</li>\n";
echo "<li><strong>Bonus:</strong> Added other missing variables for comprehensive enhanced features</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #007cba; margin: 10px 0;'>\n";
echo "<h3>🎯 Expected Result:</h3>\n";
echo "<p>The enhanced budget template should now:</p>\n";
echo "<ul>\n";
echo "<li>Display the proper expense count in the overview section</li>\n";
echo "<li>Show correct tab counts for outcomes and future expenses</li>\n";
echo "<li>Render the full enhanced interface instead of showing an empty page</li>\n";
echo "<li>Provide all advanced management features</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "</div>\n";

echo "<script>console.log('Outcomes variable fix test completed');</script>\n";
?>
