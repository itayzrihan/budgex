<?php
/**
 * Complete Enhanced Budget Template Test
 * Tests the comprehensive enhanced budget page implementation
 */

// WordPress test environment - skip WordPress for template validation
// define('WP_USE_THEMES', false);
// require_once('../../../wp-config.php');

// Define BUDGEX_DIR for testing
if (!defined('BUDGEX_DIR')) {
    define('BUDGEX_DIR', __DIR__ . '/');
}

// Test Configuration
$test_results = array();
$test_count = 0;
$passed_count = 0;

function run_test($test_name, $test_function) {
    global $test_results, $test_count, $passed_count;
    
    $test_count++;
    echo "Running Test: $test_name\n";
    
    try {
        $result = $test_function();
        if ($result) {
            $passed_count++;
            $test_results[$test_name] = 'PASSED';
            echo "✅ PASSED\n\n";
        } else {
            $test_results[$test_name] = 'FAILED';
            echo "❌ FAILED\n\n";
        }
    } catch (Exception $e) {
        $test_results[$test_name] = 'ERROR: ' . $e->getMessage();
        echo "🚨 ERROR: " . $e->getMessage() . "\n\n";
    }
}

// Test 1: Template File Exists and is Readable
run_test("Template File Exists", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    return file_exists($template_path) && is_readable($template_path);
});

// Test 2: Template Has No PHP Syntax Errors
run_test("Template Syntax Check", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $output = shell_exec("php -l \"$template_path\" 2>&1");
    return strpos($output, 'No syntax errors detected') !== false;
});

// Test 3: Template Contains Required Enhanced UI Elements
run_test("Enhanced UI Elements Present", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $required_elements = [
        'budgex-enhanced-budget-page',
        'dashboard-cards',
        'tab-nav',
        'tab-overview',
        'tab-outcomes', 
        'tab-analysis',
        'tab-planning',
        'tab-management',
        'tab-settings',
        'quick-add-outcome-modal',
        'spending-chart',
        'outcomes-table',
        'Chart.js'
    ];
    
    foreach ($required_elements as $element) {
        if (strpos($content, $element) === false) {
            throw new Exception("Missing required element: $element");
        }
    }
    
    return true;
});

// Test 4: JavaScript Functions Present
run_test("JavaScript Functions Present", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $required_functions = [
        'switchTab',
        'setupCharts',
        'initializeSpendingChart',
        'initializeCategoryChart', 
        'initializeTrendChart',
        'showModal',
        'hideModal',
        'filterOutcomes',
        'deleteOutcome',
        'showLoading',
        'hideLoading'
    ];
    
    foreach ($required_functions as $function) {
        if (strpos($content, "function $function") === false) {
            throw new Exception("Missing required function: $function");
        }
    }
    
    return true;
});

// Test 5: AJAX Endpoints Configured
run_test("AJAX Endpoints Configured", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $ajax_checks = [
        'admin_url("admin-ajax.php")',
        'budgex_add_outcome',
        'budgex_delete_outcome',
        'wp_create_nonce("budgex_nonce")'
    ];
    
    foreach ($ajax_checks as $check) {
        if (strpos($content, $check) === false) {
            throw new Exception("Missing AJAX configuration: $check");
        }
    }
    
    return true;
});

// Test 6: Chart.js Dependency Loaded
run_test("Chart.js Dependency Loaded", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    return strpos($content, 'chart.min.js') !== false || strpos($content, 'chart.js') !== false;
});

// Test 7: Template Handles Demo Data
run_test("Demo Data Handling", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $demo_elements = [
        'דוגמה למוצר',
        'hasValidData',
        'demo data',
        'fallback'
    ];
    
    $found_demo = false;
    foreach ($demo_elements as $element) {
        if (strpos($content, $element) !== false) {
            $found_demo = true;
            break;
        }
    }
    
    return $found_demo;
});

// Test 8: Role-Based UI Elements
run_test("Role-Based UI Elements", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $role_checks = [
        'userRole',
        'owner',
        'admin', 
        'viewer',
        'can_edit'
    ];
    
    $found_roles = 0;
    foreach ($role_checks as $check) {
        if (strpos($content, $check) !== false) {
            $found_roles++;
        }
    }
    
    return $found_roles >= 3; // At least 3 role-related elements
});

// Test 9: Responsive Design CSS
run_test("Responsive Design CSS", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $responsive_elements = [
        '@media',
        'mobile',
        'tablet',
        'grid',
        'flex'
    ];
    
    $found_responsive = 0;
    foreach ($responsive_elements as $element) {
        if (strpos($content, $element) !== false) {
            $found_responsive++;
        }
    }
    
    return $found_responsive >= 2; // At least 2 responsive design elements
});

// Test 10: Enhanced CSS Styling
run_test("Enhanced CSS Styling", function() {
    $template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
    $content = file_get_contents($template_path);
    
    $css_elements = [
        ':root',
        '--bg-primary',
        '--accent-primary',
        'box-shadow',
        'border-radius',
        'transition'
    ];
    
    foreach ($css_elements as $element) {
        if (strpos($content, $element) === false) {
            throw new Exception("Missing CSS enhancement: $element");
        }
    }
    
    return true;
});

// Test Results Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "ENHANCED BUDGET TEMPLATE TEST RESULTS\n";
echo str_repeat("=", 60) . "\n";

foreach ($test_results as $test_name => $result) {
    $status_icon = (strpos($result, 'PASSED') !== false) ? '✅' : '❌';
    echo "$status_icon $test_name: $result\n";
}

echo str_repeat("-", 60) . "\n";
echo "SUMMARY: $passed_count/$test_count tests passed\n";

if ($passed_count === $test_count) {
    echo "🎉 ALL TESTS PASSED! Enhanced budget template is ready for production.\n";
} else {
    echo "⚠️  Some tests failed. Please review and fix the issues.\n";
}

echo str_repeat("=", 60) . "\n";

// Additional Template Content Analysis
echo "\nTEMPLATE ANALYSIS:\n";
echo str_repeat("-", 30) . "\n";

$template_path = BUDGEX_DIR . 'public/templates/budgex-enhanced-direct.php';
$content = file_get_contents($template_path);
$lines = count(file($template_path));
$size = round(filesize($template_path) / 1024, 2);

echo "Template File Size: {$size}KB\n";
echo "Total Lines: $lines\n";
echo "JavaScript Lines: " . substr_count($content, 'function ') . " functions\n";
echo "CSS Variables: " . substr_count($content, '--') . " custom properties\n";
echo "PHP Blocks: " . substr_count($content, '<?php') . " blocks\n";
echo "AJAX Calls: " . substr_count($content, '$.ajax') . " calls\n";

echo "\nENHANCED FEATURES DETECTED:\n";
echo str_repeat("-", 30) . "\n";

$features = [
    'Dashboard Cards' => 'dashboard-card',
    'Tabbed Interface' => 'tab-nav',
    'Chart Integration' => 'Chart.js',
    'Modal Dialogs' => 'modal',
    'Search & Filter' => 'filter',
    'Loading States' => 'loading-overlay',
    'AJAX Operations' => 'admin-ajax.php',
    'Role-based UI' => 'userRole',
    'Responsive Design' => '@media',
    'CSS Variables' => ':root'
];

foreach ($features as $feature => $search_term) {
    $found = strpos($content, $search_term) !== false ? '✅' : '❌';
    echo "$found $feature\n";
}

echo "\n✅ Enhanced Budget Template Testing Complete!\n";
?>
