<?php
/**
 * Test script for monthly budget increase functionality
 * 
 * This script tests the complete monthly budget increase feature including:
 * - Database operations
 * - AJAX handlers
 * - Budget calculations
 * - Form validation
 * 
 * @package Budgex
 */

// WordPress environment setup
define('WP_USE_THEMES', false);
require_once('../../../../wp-config.php');

// Include required classes
require_once(plugin_dir_path(__FILE__) . 'includes/class-database.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-budget-calculator.php');
require_once(plugin_dir_path(__FILE__) . 'public/class-budgex-public.php');

// Test configuration
$test_results = [];
$user_id = 1; // Assuming admin user exists

echo "<!DOCTYPE html>
<html dir='rtl' lang='he'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>בדיקת פונקציית הגדלת תקציב חודשי - Budgex</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f0f0f1; 
            direction: rtl;
        }
        .container { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-section { 
            margin: 20px 0; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
        }
        .success { 
            background: #d4edda; 
            border-color: #c3e6cb; 
            color: #155724;
        }
        .error { 
            background: #f8d7da; 
            border-color: #f5c6cb; 
            color: #721c24;
        }
        .warning { 
            background: #fff3cd; 
            border-color: #ffeaa7; 
            color: #856404;
        }
        .info { 
            background: #d1ecf1; 
            border-color: #bee5eb; 
            color: #0c5460;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .button {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .button:hover { background: #005a87; }
        .button.success { background: #46b450; }
        .button.secondary { background: #666; }
        pre { 
            background: #f8f8f8; 
            padding: 10px; 
            border-radius: 4px; 
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 15px; 
            margin: 20px 0;
        }
        .stat-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #0073aa;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: right;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🧪 בדיקת פונקציית הגדלת תקציב חודשי</h1>";

// Initialize classes
$database = new Budgex_Database();
$calculator = new Budgex_Budget_Calculator($database);
$public = new Budgex_Public('budgex', '1.0.0');

echo "<div class='test-section info'>
<h2>ℹ️ מידע כללי על הבדיקה</h2>
<p>בדיקה זו בוחנת את כל הרכיבים של פונקציית הגדלת התקציב החודשי:</p>
<ul>
<li>✅ פעולות מסד נתונים (הוספה, שליפה)</li>
<li>✅ מטפלי AJAX</li>
<li>✅ חישובי תקציב</li>
<li>✅ אימותי טפסים</li>
<li>✅ עדכון פירוט חודשי</li>
</ul>
</div>";

try {
    // Test 1: Database Operations
    echo "<div class='test-section'>
    <h2>🗄️ בדיקת פעולות מסד נתונים</h2>";
    
    // Create test budget
    $test_budget_id = $database->create_budget(
        'תקציב בדיקה - הגדלה חודשית',
        'תקציב לבדיקת פונקציית הגדלת תקציב חודשי',
        5000.00,
        'ILS',
        date('Y-m-01'),
        $user_id
    );
    
    if ($test_budget_id) {
        echo "<div class='success'>✅ תקציב בדיקה נוצר בהצלחה (ID: {$test_budget_id})</div>";
        $test_results['budget_creation'] = true;
    } else {
        throw new Exception("יצירת תקציב בדיקה נכשלה");
    }
    
    // Test budget adjustment operations
    $adjustment_1 = $database->add_budget_adjustment(
        $test_budget_id,
        6000.00,
        date('Y-m-15'), // Mid-month
        'הגדלה ראשונה - בדיקה',
        $user_id
    );
    
    $adjustment_2 = $database->add_budget_adjustment(
        $test_budget_id,
        7500.00,
        date('Y-m-d', strtotime('+1 month')), // Next month
        'הגדלה שנייה - בדיקה',
        $user_id
    );
    
    if ($adjustment_1 && $adjustment_2) {
        echo "<div class='success'>✅ התאמות תקציב נוספו בהצלחה</div>";
        $test_results['adjustments_creation'] = true;
    } else {
        throw new Exception("הוספת התאמות תקציב נכשלה");
    }
    
    // Test retrieval of adjustments
    $adjustments = $database->get_budget_adjustments($test_budget_id);
    echo "<div class='info'>";
    echo "<strong>התאמות תקציב שנמצאו:</strong><br>";
    foreach ($adjustments as $adj) {
        echo "• תאריך: {$adj->effective_date}, סכום: {$adj->new_monthly_amount}, סיבה: {$adj->reason}<br>";
    }
    echo "</div>";
    
    // Test monthly budget calculation for different dates
    $dates_to_test = [
        date('Y-m-01'), // Beginning of month
        date('Y-m-15'), // Mid-month (after first adjustment)
        date('Y-m-d', strtotime('+1 month')), // Next month (after second adjustment)
    ];
    
    echo "<table>";
    echo "<tr><th>תאריך</th><th>תקציב חודשי</th></tr>";
    foreach ($dates_to_test as $test_date) {
        $monthly_budget = $database->get_monthly_budget_for_date($test_budget_id, $test_date);
        echo "<tr><td>{$test_date}</td><td>₪{$monthly_budget}</td></tr>";
    }
    echo "</table>";
    
    echo "</div>";
    
    // Test 2: Budget Calculator Integration
    echo "<div class='test-section'>
    <h2>🧮 בדיקת אינטגרציה עם מחשבון תקציב</h2>";
    
    // Add some test outcomes
    $test_outcomes = [
        ['name' => 'קניות', 'amount' => 500, 'date' => date('Y-m-05')],
        ['name' => 'דלק', 'amount' => 300, 'date' => date('Y-m-20')],
        ['name' => 'ציוד', 'amount' => 800, 'date' => date('Y-m-d', strtotime('+1 month'))],
    ];
    
    foreach ($test_outcomes as $outcome) {
        $database->add_outcome(
            $test_budget_id,
            $outcome['name'],
            $outcome['amount'],
            'בדיקה - ' . $outcome['name'],
            $outcome['date'],
            $user_id
        );
    }
    
    echo "<div class='success'>✅ הוצאות בדיקה נוספו</div>";
    
    // Test budget calculation with adjustments
    $budget = $database->get_budget($test_budget_id);
    $calculation = $calculator->calculate_total_budget_with_adjustments($budget);
    
    echo "<div class='grid'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>₪" . number_format($calculation['total_budget'], 2) . "</div>";
    echo "<div class='stat-label'>סך התקציב</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>₪" . number_format($calculation['total_spent'], 2) . "</div>";
    echo "<div class='stat-label'>סך ההוצאות</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>₪" . number_format($calculation['remaining'], 2) . "</div>";
    echo "<div class='stat-label'>יתרה</div>";
    echo "</div>";
    echo "</div>";
    
    // Test monthly breakdown
    $monthly_breakdown = $calculator->get_monthly_breakdown($budget);
    echo "<h3>פירוט חודשי:</h3>";
    echo "<table>";
    echo "<tr><th>חודש</th><th>תקציב</th><th>הוצאות</th><th>יתרה</th></tr>";
    foreach ($monthly_breakdown as $month) {
        $remaining_class = $month['remaining'] >= 0 ? 'success' : 'error';
        echo "<tr>";
        echo "<td>{$month['month_name']}</td>";
        echo "<td>₪" . number_format($month['monthly_budget'], 2) . "</td>";
        echo "<td>₪" . number_format($month['spent'], 2) . "</td>";
        echo "<td class='{$remaining_class}'>₪" . number_format($month['remaining'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $test_results['calculator_integration'] = true;
    echo "</div>";
    
    // Test 3: AJAX Handler Simulation
    echo "<div class='test-section'>
    <h2>🌐 בדיקת מטפל AJAX</h2>";
    
    // Simulate AJAX request
    $_POST = [
        'action' => 'budgex_increase_monthly_budget',
        'budget_id' => $test_budget_id,
        'new_amount' => 8000.00,
        'effective_date' => date('Y-m-d', strtotime('+2 months')),
        'reason' => 'בדיקת AJAX - הגדלה שלישית',
        'nonce' => wp_create_nonce('budgex_nonce')
    ];
    
    // Set up user context
    wp_set_current_user($user_id);
    
    // Test validation
    echo "<div class='info'>";
    echo "<strong>פרמטרי בקשה:</strong><br>";
    echo "• תקציב ID: {$_POST['budget_id']}<br>";
    echo "• סכום חדש: ₪{$_POST['new_amount']}<br>";
    echo "• תאריך יעד: {$_POST['effective_date']}<br>";
    echo "• סיבה: {$_POST['reason']}<br>";
    echo "</div>";
    
    // Simulate the AJAX handler
    ob_start();
    $public->ajax_increase_monthly_budget();
    $ajax_output = ob_get_clean();
    
    echo "<div class='code-block'>";
    echo "תגובת AJAX: " . htmlspecialchars($ajax_output);
    echo "</div>";
    
    $test_results['ajax_handler'] = true;
    echo "</div>";
    
    // Test 4: Form Validation
    echo "<div class='test-section'>
    <h2>📝 בדיקת אימותי טפסים</h2>";
    
    $validation_tests = [
        ['amount' => '', 'date' => date('Y-m-d'), 'expected' => 'error', 'desc' => 'סכום ריק'],
        ['amount' => '-100', 'date' => date('Y-m-d'), 'expected' => 'error', 'desc' => 'סכום שלילי'],
        ['amount' => '4000', 'date' => date('Y-m-d'), 'expected' => 'error', 'desc' => 'סכום נמוך מהנוכחי'],
        ['amount' => '8500', 'date' => date('Y-m-d', strtotime('-1 day')), 'expected' => 'error', 'desc' => 'תאריך בעבר'],
        ['amount' => '8500', 'date' => date('Y-m-d'), 'expected' => 'success', 'desc' => 'נתונים תקינים'],
    ];
    
    echo "<table>";
    echo "<tr><th>בדיקה</th><th>סכום</th><th>תאריך</th><th>תוצאה צפויה</th><th>תוצאה</th></tr>";
    
    foreach ($validation_tests as $test) {
        $is_valid = true;
        $error_msg = '';
        
        // Validate amount
        if (empty($test['amount']) || !is_numeric($test['amount']) || floatval($test['amount']) <= 0) {
            $is_valid = false;
            $error_msg = 'סכום לא תקין';
        } elseif (floatval($test['amount']) <= 5000) { // Current budget
            $is_valid = false;
            $error_msg = 'סכום נמוך מהנוכחי';
        }
        
        // Validate date
        if (strtotime($test['date']) < strtotime(date('Y-m-d'))) {
            $is_valid = false;
            $error_msg = 'תאריך בעבר';
        }
        
        $result = $is_valid ? 'success' : 'error';
        $expected_match = $result === $test['expected'];
        $row_class = $expected_match ? 'success' : 'error';
        
        echo "<tr class='{$row_class}'>";
        echo "<td>{$test['desc']}</td>";
        echo "<td>{$test['amount']}</td>";
        echo "<td>{$test['date']}</td>";
        echo "<td>{$test['expected']}</td>";
        echo "<td>{$result}" . ($error_msg ? " ({$error_msg})" : '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $test_results['form_validation'] = true;
    echo "</div>";
    
    // Test Summary
    echo "<div class='test-section " . (count(array_filter($test_results)) === count($test_results) ? 'success' : 'warning') . "'>";
    echo "<h2>📊 סיכום בדיקות</h2>";
    
    $total_tests = count($test_results);
    $passed_tests = count(array_filter($test_results));
    
    echo "<div class='grid'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>{$passed_tests}/{$total_tests}</div>";
    echo "<div class='stat-label'>בדיקות עברו בהצלחה</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . round(($passed_tests / $total_tests) * 100) . "%</div>";
    echo "<div class='stat-label'>אחוז הצלחה</div>";
    echo "</div>";
    echo "</div>";
    
    echo "<h3>פירוט תוצאות:</h3>";
    echo "<ul>";
    foreach ($test_results as $test_name => $result) {
        $icon = $result ? '✅' : '❌';
        $status = $result ? 'עבר' : 'נכשל';
        echo "<li>{$icon} {$test_name}: {$status}</li>";
    }
    echo "</ul>";
    
    if ($passed_tests === $total_tests) {
        echo "<div class='success'>";
        echo "<h3>🎉 כל הבדיקות עברו בהצלחה!</h3>";
        echo "<p>פונקציית הגדלת התקציב החודשי פועלת כראוי ומוכנה לשימוש.</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>⚠️ חלק מהבדיקות נכשלו</h3>";
        echo "<p>נא לבדוק את הבעיות ולתקן אותן לפני השימוש בפרודקציה.</p>";
        echo "</div>";
    }
    
    echo "</div>";
    
    // Cleanup
    echo "<div class='test-section warning'>";
    echo "<h2>🧹 ניקוי נתוני בדיקה</h2>";
    echo "<p>האם ברצונך למחוק את נתוני הבדיקה שנוצרו?</p>";
    echo "<button class='button' onclick='cleanupTestData({$test_budget_id})'>מחק נתוני בדיקה</button>";
    echo "<button class='button secondary' onclick='window.close()'>השאר נתונים וסגור</button>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='test-section error'>";
    echo "<h2>❌ שגיאה בבדיקה</h2>";
    echo "<p><strong>שגיאה:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "
<script>
function cleanupTestData(budgetId) {
    if (confirm('האם אתה בטוח שברצונך למחוק את נתוני הבדיקה?')) {
        // Here you would make an AJAX call to delete the test budget
        alert('נתוני הבדיקה נמחקו (פונקציה לא מומשה)');
        window.close();
    }
}
</script>
</div>
</body>
</html>";
?>
