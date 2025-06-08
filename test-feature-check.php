<?php
/**
 * Direct method existence check
 */

// Define the constant that the classes expect
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

echo "Checking method existence in classes...\n\n";

// Check Budget Calculator methods
$calculator_file = 'includes/class-budget-calculator.php';
$calculator_content = file_get_contents($calculator_file);

echo "Budget Calculator Methods:\n";
if (strpos($calculator_content, 'function calculate_total_budget_with_adjustments') !== false) {
    echo "✓ calculate_total_budget_with_adjustments method found\n";
} else {
    echo "✗ calculate_total_budget_with_adjustments method missing\n";
}

if (strpos($calculator_content, 'function get_monthly_breakdown') !== false) {
    echo "✓ get_monthly_breakdown method found\n";
} else {
    echo "✗ get_monthly_breakdown method missing\n";
}

// Check Database methods
$database_file = 'includes/class-database.php';
$database_content = file_get_contents($database_file);

echo "\nDatabase Methods:\n";
if (strpos($database_content, 'function add_budget_adjustment') !== false) {
    echo "✓ add_budget_adjustment method found\n";
} else {
    echo "✗ add_budget_adjustment method missing\n";
}

if (strpos($database_content, 'function get_budget_adjustments') !== false) {
    echo "✓ get_budget_adjustments method found\n";
} else {
    echo "✗ get_budget_adjustments method missing\n";
}

if (strpos($database_content, 'function get_monthly_budget_for_date') !== false) {
    echo "✓ get_monthly_budget_for_date method found\n";
} else {
    echo "✗ get_monthly_budget_for_date method missing\n";
}

// Check Public class AJAX handler
$public_file = 'public/class-budgex-public.php';
$public_content = file_get_contents($public_file);

echo "\nPublic Class AJAX Handler:\n";
if (strpos($public_content, 'function ajax_increase_monthly_budget') !== false) {
    echo "✓ ajax_increase_monthly_budget method found\n";
} else {
    echo "✗ ajax_increase_monthly_budget method missing\n";
}

// Check frontend form
$frontend_file = 'public/partials/budgex-budget-page.php';
$frontend_content = file_get_contents($frontend_file);

echo "\nFrontend Form:\n";
if (strpos($frontend_content, 'increase-monthly-budget-section') !== false) {
    echo "✓ Monthly budget increase form found\n";
} else {
    echo "✗ Monthly budget increase form missing\n";
}

if (strpos($frontend_content, 'name="new_monthly_amount"') !== false) {
    echo "✓ New monthly amount input found\n";
} else {
    echo "✗ New monthly amount input missing\n";
}

// Check CSS for new styles
$css_file = 'public/css/budgex-public.css';
$css_content = file_get_contents($css_file);

echo "\nCSS Styles:\n";
if (strpos($css_content, '.management-card') !== false) {
    echo "✓ Management card styles found\n";
} else {
    echo "✗ Management card styles missing\n";
}

if (strpos($css_content, '.input-group') !== false) {
    echo "✓ Input group styles found\n";
} else {
    echo "✗ Input group styles missing\n";
}

echo "\nFeature Implementation Check Complete!\n";
echo "\nSummary:\n";
echo "- All core methods for monthly budget increase functionality should be present\n";
echo "- Frontend form elements should be in place\n";
echo "- CSS styling should be applied\n";
echo "- AJAX handler should be implemented\n";
?>
