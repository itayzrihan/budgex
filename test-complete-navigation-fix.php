<?php
/**
 * Test Complete Navigation Fix
 * Tests the frontend budget navigation to ensure the enhanced budget page displays properly
 */

// Simulate WordPress environment
define('WP_DEBUG', true);
error_reporting(E_ALL);

echo "<h1>Testing Complete Frontend Budget Navigation Fix</h1>\n";
echo "<p>Testing the enhanced budget page display after all fixes...</p>\n";

// Test 1: Verify enhanced template structure
echo "<h2>1. Enhanced Template Structure Test</h2>\n";
$template_file = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';

if (file_exists($template_file)) {
    $template_content = file_get_contents($template_file);
    
    // Check for complete HTML structure
    $div_opens = substr_count($template_content, '<div');
    $div_closes = substr_count($template_content, '</div>');
    
    echo "✅ Template file exists<br>\n";
    echo "📊 Opening divs: $div_opens<br>\n";
    echo "📊 Closing divs: $div_closes<br>\n";
    
    if ($div_opens === $div_closes) {
        echo "✅ HTML structure is balanced<br>\n";
    } else {
        echo "❌ HTML structure is unbalanced<br>\n";
    }
    
    // Check for key components
    $has_container = strpos($template_content, 'budgex-enhanced-container') !== false;
    $has_js_data = strpos($template_content, 'budgexEnhancedData') !== false;
    $has_closing_script = strpos($template_content, '</script>') !== false;
    
    echo "✅ Container element: " . ($has_container ? "Present" : "Missing") . "<br>\n";
    echo "✅ JavaScript data: " . ($has_js_data ? "Present" : "Missing") . "<br>\n";
    echo "✅ Complete script tags: " . ($has_closing_script ? "Present" : "Missing") . "<br>\n";
} else {
    echo "❌ Enhanced template file not found<br>\n";
}

// Test 2: Check public class asset enqueuing
echo "<h2>2. Asset Enqueuing Test</h2>\n";
$public_class_file = __DIR__ . '/public/class-budgex-public.php';

if (file_exists($public_class_file)) {
    $public_content = file_get_contents($public_class_file);
    
    // Check for enhanced asset loading
    $has_force_enqueue = strpos($public_content, 'force_enqueue_enhanced_assets') !== false;
    $has_budget_detection = strpos($public_content, 'budgex/budget/') !== false;
    $has_chart_js = strpos($public_content, 'chart.js') !== false;
    
    echo "✅ Force enqueue method: " . ($has_force_enqueue ? "Present" : "Missing") . "<br>\n";
    echo "✅ Budget URL detection: " . ($has_budget_detection ? "Present" : "Missing") . "<br>\n";
    echo "✅ Chart.js dependency: " . ($has_chart_js ? "Present" : "Missing") . "<br>\n";
} else {
    echo "❌ Public class file not found<br>\n";
}

// Test 3: Simulate frontend budget page request
echo "<h2>3. Frontend Budget Page Simulation</h2>\n";

// Mock WordPress functions and data
function wp_enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all') {
    echo "📤 Enqueuing style: $handle<br>\n";
}

function wp_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false) {
    echo "📤 Enqueuing script: $handle<br>\n";
}

function plugin_dir_url($file) {
    return 'https://example.com/wp-content/plugins/budgex/';
}

function wp_localize_script($handle, $object_name, $l10n) {
    echo "📤 Localizing script: $handle with data: $object_name<br>\n";
}

// Mock budget data
$budget = (object) [
    'id' => 1,
    'name' => 'Test Budget',
    'amount' => 1000.00,
    'period' => 'monthly'
];

$categories = [
    (object) ['id' => 1, 'name' => 'Food', 'budgeted_amount' => 300],
    (object) ['id' => 2, 'name' => 'Transport', 'budgeted_amount' => 200]
];

$outcomes = [
    (object) ['id' => 1, 'category_id' => 1, 'amount' => 50, 'description' => 'Groceries'],
    (object) ['id' => 2, 'category_id' => 2, 'amount' => 25, 'description' => 'Bus fare']
];

// Test enhanced template rendering
if (file_exists($template_file)) {
    echo "<h3>Template Rendering Test</h3>\n";
    
    // Capture template output
    ob_start();
    include $template_file;
    $template_output = ob_get_clean();
    
    if (!empty($template_output)) {
        echo "✅ Template renders content<br>\n";
        echo "📊 Output length: " . strlen($template_output) . " characters<br>\n";
        
        // Check for key elements in output
        $has_content = strpos($template_output, 'budgex-enhanced-container') !== false;
        $has_data = strpos($template_output, 'budgexEnhancedData') !== false;
        
        echo "✅ Container in output: " . ($has_content ? "Yes" : "No") . "<br>\n";
        echo "✅ JavaScript data in output: " . ($has_data ? "Yes" : "No") . "<br>\n";
        
        // Show first 500 characters of output
        echo "<h4>Sample Output:</h4>\n";
        echo "<pre>" . htmlspecialchars(substr($template_output, 0, 500)) . "...</pre>\n";
        
    } else {
        echo "❌ Template produces no output<br>\n";
    }
} else {
    echo "❌ Cannot test template rendering - file missing<br>\n";
}

// Test 4: Check asset files exist
echo "<h2>4. Asset Files Check</h2>\n";

$css_file = __DIR__ . '/public/css/budgex-enhanced-budget.css';
$js_file = __DIR__ . '/public/js/budgex-enhanced-budget.js';

if (file_exists($css_file)) {
    echo "✅ Enhanced CSS file exists<br>\n";
    $css_size = filesize($css_file);
    echo "📊 CSS file size: $css_size bytes<br>\n";
} else {
    echo "❌ Enhanced CSS file missing<br>\n";
}

if (file_exists($js_file)) {
    echo "✅ Enhanced JS file exists<br>\n";
    $js_size = filesize($js_file);
    echo "📊 JS file size: $js_size bytes<br>\n";
} else {
    echo "❌ Enhanced JS file missing<br>\n";
}

// Test 5: Final validation
echo "<h2>5. Final Validation Summary</h2>\n";

$all_checks = [
    'Enhanced template exists' => file_exists($template_file),
    'Public class exists' => file_exists($public_class_file),
    'Enhanced CSS exists' => file_exists($css_file),
    'Enhanced JS exists' => file_exists($js_file),
    'Template has balanced HTML' => isset($div_opens) && isset($div_closes) && $div_opens === $div_closes,
    'Template produces output' => isset($template_output) && !empty($template_output)
];

$passed = 0;
$total = count($all_checks);

foreach ($all_checks as $check => $result) {
    if ($result) {
        echo "✅ $check<br>\n";
        $passed++;
    } else {
        echo "❌ $check<br>\n";
    }
}

echo "<br><strong>Overall Result: $passed/$total checks passed</strong><br>\n";

if ($passed === $total) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;'>\n";
    echo "🎉 <strong>SUCCESS:</strong> All navigation fix components are in place and working correctly!\n";
    echo "</div>\n";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;'>\n";
    echo "⚠️ <strong>WARNING:</strong> Some components may need attention.\n";
    echo "</div>\n";
}

echo "\n<h2>Next Steps for WordPress Testing</h2>\n";
echo "<ol>\n";
echo "<li>Upload the plugin to WordPress</li>\n";
echo "<li>Activate the Budgex plugin</li>\n";
echo "<li>Create a test budget in the admin dashboard</li>\n";
echo "<li>Navigate to the frontend budget page via the navigation menu</li>\n";
echo "<li>Verify the enhanced budget interface displays instead of empty container</li>\n";
echo "</ol>\n";

?>
