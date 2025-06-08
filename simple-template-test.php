<?php
/**
 * Simple Template Test
 */

echo "<h1>🔧 Simple Template Test</h1>\n";

// Test direct template inclusion
$template_path = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';

if (file_exists($template_path)) {
    echo "<p>✅ Template exists: $template_path</p>\n";
    
    // Set up minimal test variables
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
    
    // Create dummy classes to prevent errors
    class Budgex_Budget_Calculator {
        public function format_currency($amount, $currency = 'ILS') {
            return '₪' . number_format($amount, 2);
        }
        public function get_monthly_breakdown($budget_id) { return []; }
    }
    
    class Budgex_Database {
        public function get_budget_shared_users($budget_id) { return []; }
        public function get_pending_invitations($budget_id) { return []; }
        public function get_future_expenses($budget_id) { return []; }
        public function get_recurring_expenses($budget_id) { return []; }
        public function get_budget_adjustments($budget_id) { return []; }
    }
    
    // Mock WordPress functions
    if (!function_exists('__')) {
        function __($text, $domain = '') { return $text; }
    }
    if (!function_exists('_e')) {
        function _e($text, $domain = '') { echo $text; }
    }
    if (!function_exists('esc_attr')) {
        function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES); }
    }
    if (!function_exists('esc_html')) {
        function esc_html($text) { return htmlspecialchars($text); }
    }
    if (!function_exists('home_url')) {
        function home_url($path = '') { return 'https://example.com' . $path; }
    }
    if (!function_exists('wp_create_nonce')) {
        function wp_create_nonce($action) { return 'test_nonce_123'; }
    }
    if (!function_exists('admin_url')) {
        function admin_url($path = '') { return 'https://example.com/wp-admin/' . $path; }
    }
    
    try {
        echo "<p>🧪 Testing template inclusion...</p>\n";
        
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "<p>❌ Template returned empty output</p>\n";
        } else {
            echo "<p>✅ Template generated content</p>\n";
            echo "<p>Content length: " . strlen($output) . " characters</p>\n";
            echo "<p>Contains HTML div? " . (strpos($output, '<div') !== false ? 'Yes' : 'No') . "</p>\n";
            echo "<p>Contains budget name? " . (strpos($output, 'Test Budget') !== false ? 'Yes' : 'No') . "</p>\n";
            
            // Show first 500 characters
            echo "<h3>First 500 characters of output:</h3>\n";
            echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow: auto;'>";
            echo htmlspecialchars(substr($output, 0, 500));
            echo "</pre>\n";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Template error: " . $e->getMessage() . "</p>\n";
    } catch (Error $e) {
        echo "<p>❌ Template fatal error: " . $e->getMessage() . "</p>\n";
    }
    
} else {
    echo "<p>❌ Template not found: $template_path</p>\n";
}

echo "<script>console.log('Template test complete');</script>\n";
?>
