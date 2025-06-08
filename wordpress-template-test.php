<?php
/**
 * WordPress Environment Template Test
 */

echo "<h1>🔧 WordPress Environment Template Test</h1>\n";

// Mock WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Mock WordPress functions that are needed
if (!function_exists('__')) {
    function __($text, $domain = '') { 
        return $text; 
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = '') { 
        echo $text; 
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) { 
        return htmlspecialchars($text, ENT_QUOTES); 
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) { 
        return htmlspecialchars($text); 
    }
}

if (!function_exists('home_url')) {
    function home_url($path = '') { 
        return 'https://example.com' . $path; 
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) { 
        return 'test_nonce_123'; 
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path = '') { 
        return 'https://example.com/wp-admin/' . $path; 
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() { 
        return 1; 
    }
}

if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in() { 
        return true; 
    }
}

if (!function_exists('wp_get_current_user')) {
    function wp_get_current_user() { 
        return (object) ['ID' => 1, 'user_login' => 'testuser']; 
    }
}

if (!function_exists('date_i18n')) {
    function date_i18n($format, $timestamp = null) { 
        return date($format, $timestamp ?: time()); 
    }
}

// Create dummy classes
if (!class_exists('Budgex_Budget_Calculator')) {
    class Budgex_Budget_Calculator {
        public function format_currency($amount, $currency = 'ILS') {
            return '₪' . number_format($amount, 2);
        }
        public function get_monthly_breakdown($budget_id) { 
            return [
                [
                    'month' => date('Y-m'),
                    'budget' => 5000,
                    'spent' => 1500,
                    'remaining' => 3500
                ]
            ]; 
        }
        public function calculate_projected_balance($budget_id) {
            return [
                'end_of_month' => 3500,
                'next_month' => 5000
            ];
        }
    }
}

if (!class_exists('Budgex_Database')) {
    class Budgex_Database {
        public function get_budget_shared_users($budget_id) { 
            return []; 
        }
        public function get_pending_invitations($budget_id) { 
            return []; 
        }
        public function get_future_expenses($budget_id) { 
            return []; 
        }
        public function get_recurring_expenses($budget_id) { 
            return []; 
        }
        public function get_budget_adjustments($budget_id) { 
            return []; 
        }
    }
}

// Set up test data
$budget = (object) [
    'id' => 7,
    'budget_name' => 'Test Budget',
    'monthly_budget' => 5000,
    'currency' => 'ILS',
    'created_at' => date('Y-m-d H:i:s'),
    'start_date' => date('Y-m-d', strtotime('-30 days'))
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

$outcomes = [
    (object) [
        'id' => 1,
        'outcome_name' => 'Test Expense',
        'amount' => 500,
        'outcome_date' => date('Y-m-d'),
        'description' => 'Test description'
    ]
];

$user_role = 'owner';

echo "<p>✅ Mock environment set up</p>\n";
echo "<p>🧪 Testing template with proper environment...</p>\n";

$template_path = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';

if (file_exists($template_path)) {
    try {
        ob_start();
        include $template_path;
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "<p>❌ Template returned empty output</p>\n";
        } else {
            echo "<p>✅ Template generated content successfully!</p>\n";
            echo "<p>Content length: " . strlen($output) . " characters</p>\n";
            echo "<p>Contains budget title? " . (strpos($output, 'Test Budget') !== false ? 'Yes' : 'No') . "</p>\n";
            echo "<p>Contains currency? " . (strpos($output, '₪') !== false ? 'Yes' : 'No') . "</p>\n";
            
            // Show a sample of the output
            echo "<h3>Sample Output (first 800 characters):</h3>\n";
            echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #4CAF50; margin: 10px 0; overflow: auto;'>";
            echo "<pre>" . htmlspecialchars(substr($output, 0, 800)) . "...</pre>";
            echo "</div>\n";
            
            echo "<p><strong>🎉 SUCCESS! The template is working correctly.</strong></p>\n";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Template exception: " . $e->getMessage() . "</p>\n";
    } catch (Error $e) {
        echo "<p>❌ Template fatal error: " . $e->getMessage() . "</p>\n";
        echo "<p>File: " . $e->getFile() . "</p>\n";
        echo "<p>Line: " . $e->getLine() . "</p>\n";
    }
} else {
    echo "<p>❌ Template not found: $template_path</p>\n";
}

echo "<script>console.log('WordPress environment template test complete');</script>\n";
?>
