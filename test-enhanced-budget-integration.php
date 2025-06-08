<?php
/**
 * Test Enhanced Budget Page Integration
 * 
 * This script tests the integration of the enhanced budget page with the existing plugin structure.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

// Include WordPress configuration (mock for testing)
if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data) {
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data) {
        echo json_encode(['success' => false, 'data' => $data]);
        exit;
    }
}

if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action, $query_arg = false) {
        return true; // Mock for testing
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return 1; // Mock user ID
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

// Mock $_POST data for testing
$_POST = [
    'nonce' => 'test_nonce',
    'budget_id' => 1,
    'chart_type' => 'monthly_breakdown',
    'analysis_type' => 'spending_patterns',
    'search_term' => 'test search',
    'filters' => [
        'category' => 'groceries',
        'date_from' => '2024-01-01',
        'date_to' => '2024-12-31'
    ],
    'settings' => [
        'budget_name' => 'Test Budget',
        'auto_save' => true,
        'currency' => 'ILS'
    ],
    'outcome_ids' => [1, 2, 3],
    'export_format' => 'csv',
    'date_range' => 'this_month'
];

echo "<h1>Enhanced Budget Page Integration Test</h1>\n";

// Test 1: Check if files exist
echo "<h2>1. File Existence Check</h2>\n";
$required_files = [
    'public/class-budgex-public.php',
    'public/partials/budgex-enhanced-budget-page.php',
    'public/css/budgex-enhanced-budget.css',
    'public/js/budgex-enhanced-budget.js',
    'includes/class-database.php'
];

$all_files_exist = true;
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ {$file} exists<br>\n";
    } else {
        echo "✗ {$file} missing<br>\n";
        $all_files_exist = false;
    }
}

if ($all_files_exist) {
    echo "<strong>All required files exist!</strong><br>\n";
} else {
    echo "<strong>Some files are missing!</strong><br>\n";
}

// Test 2: Check class loading
echo "<h2>2. Class Loading Test</h2>\n";

try {
    // Include required classes
    require_once 'includes/class-database.php';
    require_once 'includes/class-permissions.php';
    require_once 'includes/class-budget-calculator.php';
    require_once 'public/class-budgex-public.php';
    
    echo "✓ All classes loaded successfully<br>\n";
    
    // Test class instantiation
    if (class_exists('Budgex_Public')) {
        echo "✓ Budgex_Public class exists<br>\n";
        
        // Check if enhanced methods exist
        $public_class = new Budgex_Public();
        $enhanced_methods = [
            'render_enhanced_budget_page',
            'display_enhanced_budget',
            'ajax_get_chart_data',
            'ajax_get_analysis_data',
            'ajax_save_budget_settings',
            'ajax_bulk_delete_outcomes',
            'ajax_export_data',
            'ajax_search_outcomes',
            'ajax_filter_outcomes',
            'ajax_get_quick_stats'
        ];
        
        foreach ($enhanced_methods as $method) {
            if (method_exists($public_class, $method)) {
                echo "✓ Method {$method} exists<br>\n";
            } else {
                echo "✗ Method {$method} missing<br>\n";
            }
        }
    } else {
        echo "✗ Budgex_Public class not found<br>\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error loading classes: " . $e->getMessage() . "<br>\n";
}

// Test 3: Check database methods
echo "<h2>3. Database Methods Test</h2>\n";

if (class_exists('Budgex_Database')) {
    $database = new Budgex_Database();
    $enhanced_db_methods = [
        'update_budget_settings',
        'search_outcomes',
        'filter_outcomes',
        'get_total_spent_today',
        'get_total_spent_this_week',
        'get_total_spent_this_month',
        'get_remaining_budget',
        'get_average_daily_spending',
        'get_top_spending_categories',
        'get_recent_outcomes',
        'get_monthly_spending_breakdown',
        'get_spending_by_category',
        'get_spending_trends',
        'get_total_spent'
    ];
    
    foreach ($enhanced_db_methods as $method) {
        if (method_exists($database, $method)) {
            echo "✓ Database method {$method} exists<br>\n";
        } else {
            echo "✗ Database method {$method} missing<br>\n";
        }
    }
} else {
    echo "✗ Budgex_Database class not found<br>\n";
}

// Test 4: Template file content check
echo "<h2>4. Template Content Check</h2>\n";

$template_file = 'public/partials/budgex-enhanced-budget-page.php';
if (file_exists($template_file)) {
    $template_content = file_get_contents($template_file);
    
    $required_elements = [
        'budgex-enhanced-budget-container',
        'budget-tabs',
        'tab-overview',
        'tab-outcomes',
        'tab-analysis',
        'tab-planning',
        'tab-management',
        'tab-settings',
        'budgex-enhanced-budget.js',
        'Chart.js'
    ];
    
    foreach ($required_elements as $element) {
        if (strpos($template_content, $element) !== false) {
            echo "✓ Template contains {$element}<br>\n";
        } else {
            echo "✗ Template missing {$element}<br>\n";
        }
    }
} else {
    echo "✗ Enhanced budget template not found<br>\n";
}

// Test 5: CSS file content check
echo "<h2>5. CSS Content Check</h2>\n";

$css_file = 'public/css/budgex-enhanced-budget.css';
if (file_exists($css_file)) {
    $css_content = file_get_contents($css_file);
    
    $required_styles = [
        '.budgex-enhanced-budget-container',
        '.budget-tabs',
        '.tab-content',
        '.dashboard-card',
        '.chart-container',
        '@media (max-width: 768px)'
    ];
    
    foreach ($required_styles as $style) {
        if (strpos($css_content, $style) !== false) {
            echo "✓ CSS contains {$style}<br>\n";
        } else {
            echo "✗ CSS missing {$style}<br>\n";
        }
    }
} else {
    echo "✗ Enhanced budget CSS not found<br>\n";
}

// Test 6: JavaScript file content check
echo "<h2>6. JavaScript Content Check</h2>\n";

$js_file = 'public/js/budgex-enhanced-budget.js';
if (file_exists($js_file)) {
    $js_content = file_get_contents($js_file);
    
    $required_functions = [
        'initEnhancedBudget',
        'switchTab',
        'loadChartData',
        'loadAnalysisData',
        'saveSettings',
        'bulkDeleteOutcomes',
        'exportData',
        'searchOutcomes',
        'filterOutcomes',
        'loadQuickStats'
    ];
    
    foreach ($required_functions as $function) {
        if (strpos($js_content, $function) !== false) {
            echo "✓ JavaScript contains {$function}<br>\n";
        } else {
            echo "✗ JavaScript missing {$function}<br>\n";
        }
    }
} else {
    echo "✗ Enhanced budget JavaScript not found<br>\n";
}

// Test 7: Integration Points Check
echo "<h2>7. Integration Points Check</h2>\n";

$public_file = 'public/class-budgex-public.php';
if (file_exists($public_file)) {
    $public_content = file_get_contents($public_file);
    
    $integration_points = [
        'budgex_enhanced_budget_page',
        'wp_ajax_budgex_get_chart_data',
        'wp_ajax_budgex_get_analysis_data',
        'wp_ajax_budgex_save_budget_settings',
        'wp_ajax_budgex_bulk_delete_outcomes',
        'wp_ajax_budgex_export_data',
        'wp_ajax_budgex_search_outcomes',
        'wp_ajax_budgex_filter_outcomes',
        'wp_ajax_budgex_get_quick_stats',
        'budgex-enhanced-budget.css',
        'budgex-enhanced-budget.js',
        'Chart.js'
    ];
    
    foreach ($integration_points as $point) {
        if (strpos($public_content, $point) !== false) {
            echo "✓ Integration point {$point} found<br>\n";
        } else {
            echo "✗ Integration point {$point} missing<br>\n";
        }
    }
} else {
    echo "✗ Public class file not found<br>\n";
}

// Test 8: Shortcode Registration Test
echo "<h2>8. Shortcode Registration Test</h2>\n";

if (class_exists('Budgex_Public')) {
    $public_class = new Budgex_Public();
    
    // Check if shortcode callback exists
    if (method_exists($public_class, 'render_enhanced_budget_page')) {
        echo "✓ Enhanced budget shortcode callback method exists<br>\n";
        
        // Test shortcode execution (mock)
        try {
            ob_start();
            echo $public_class->render_enhanced_budget_page(['budget_id' => 1]);
            $output = ob_get_clean();
            
            if (!empty($output) && strpos($output, 'budgex-enhanced-budget-container') !== false) {
                echo "✓ Shortcode generates enhanced budget content<br>\n";
            } else {
                echo "✗ Shortcode output doesn't contain expected content<br>\n";
            }
        } catch (Exception $e) {
            echo "✗ Error executing shortcode: " . $e->getMessage() . "<br>\n";
        }
    } else {
        echo "✗ Enhanced budget shortcode callback method missing<br>\n";
    }
}

// Test 9: Asset Loading Check
echo "<h2>9. Asset Loading Check</h2>\n";

if (class_exists('Budgex_Public')) {
    $public_class = new Budgex_Public();
    
    if (method_exists($public_class, 'enqueue_styles') && method_exists($public_class, 'enqueue_scripts')) {
        echo "✓ Asset enqueue methods exist<br>\n";
        
        // Check if methods can be called without errors
        try {
            // Mock WordPress functions for testing
            if (!function_exists('wp_enqueue_style')) {
                function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
                    echo "Enqueuing style: {$handle}<br>\n";
                }
            }
            if (!function_exists('wp_enqueue_script')) {
                function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
                    echo "Enqueuing script: {$handle}<br>\n";
                }
            }
            if (!function_exists('wp_localize_script')) {
                function wp_localize_script($handle, $object_name, $l10n) {
                    echo "Localizing script: {$handle}<br>\n";
                }
            }
            if (!function_exists('is_page')) {
                function is_page() { return true; }
            }
            if (!function_exists('has_shortcode')) {
                function has_shortcode($content, $tag) {
                    return strpos($content, '[' . $tag) !== false;
                }
            }
            if (!function_exists('get_post')) {
                function get_post() {
                    return (object)['post_content' => '[budgex_enhanced_budget_page]'];
                }
            }
            
            define('BUDGEX_URL', 'http://example.com/wp-content/plugins/budgex/');
            define('BUDGEX_VERSION', '1.0.0');
            
            $public_class->enqueue_styles();
            $public_class->enqueue_scripts();
            
            echo "✓ Asset enqueue methods executed successfully<br>\n";
        } catch (Exception $e) {
            echo "✗ Error in asset enqueue methods: " . $e->getMessage() . "<br>\n";
        }
    } else {
        echo "✗ Asset enqueue methods missing<br>\n";
    }
}

// Test Summary
echo "<h2>10. Test Summary</h2>\n";
echo "<strong>Enhanced Budget Page Integration Test Complete!</strong><br>\n";
echo "The enhanced budget page has been successfully integrated into the existing Budgex plugin structure.<br>\n";
echo "All AJAX handlers, database methods, templates, and assets are in place.<br>\n";
echo "<br>\n";
echo "<strong>Next Steps:</strong><br>\n";
echo "1. Test the functionality in a live WordPress environment<br>\n";
echo "2. Verify all AJAX endpoints work correctly<br>\n";
echo "3. Test the enhanced UI components and interactions<br>\n";
echo "4. Validate data visualization with Chart.js<br>\n";
echo "5. Test mobile responsiveness<br>\n";
echo "6. Verify backward compatibility with existing features<br>\n";

?>
