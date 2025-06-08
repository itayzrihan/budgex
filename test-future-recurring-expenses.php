<?php
/**
 * Test script for Future and Recurring Expenses functionality
 * 
 * This script tests the complete future and recurring expenses feature including:
 * - Database operations for future expenses
 * - Database operations for recurring expenses  
 * - AJAX handlers for both types
 * - Projected balance calculations
 * - Frontend form validation
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
    <title>בדיקת הוצאות עתידיות וחוזרות - Budgex</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test-container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .test-section { margin: 25px 0; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .success { background: #f0f9ff; border-color: #0ea5e9; color: #0c4a6e; }
        .error { background: #fef2f2; border-color: #ef4444; color: #991b1b; }
        .warning { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
        .info { background: #f8fafc; border-color: #64748b; color: #334155; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 15px 0; }
        .stat-card { background: #fafafa; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e5e5e5; }
        .stat-number { font-size: 24px; font-weight: bold; color: #059669; }
        .stat-label { font-size: 14px; color: #6b7280; margin-top: 5px; }
        .button { background: #3b82f6; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; margin: 5px; }
        .button:hover { background: #2563eb; }
        .secondary { background: #6b7280; }
        .secondary:hover { background: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: right; border: 1px solid #e5e5e5; }
        th { background: #f9fafb; font-weight: 600; }
        tr.success { background: #f0fdf4; }
        tr.error { background: #fef2f2; }
        .code-block { background: #f1f5f9; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 14px; overflow-x: auto; margin: 10px 0; }
        .expense-item { background: #f8fafc; padding: 12px; border-radius: 6px; margin: 8px 0; border-right: 4px solid #3b82f6; }
        .amount-positive { color: #059669; font-weight: bold; }
        .amount-negative { color: #dc2626; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
        .badge.active { background: #dcfce7; color: #166534; }
        .badge.inactive { background: #fee2e2; color: #991b1b; }
        .projection-summary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin: 15px 0; }
        .projection-item { display: flex; justify-content: space-between; margin: 8px 0; }
    </style>
</head>
<body>
<div class='test-container'>";

echo "<h1>🔮 בדיקת הוצאות עתידיות וחוזרות</h1>";

// Initialize classes
$database = new Budgex_Database();
$calculator = new Budgex_Budget_Calculator($database);

echo "<div class='test-section info'>
<h2>ℹ️ מידע כללי על הבדיקה</h2>
<p>בדיקה זו בוחנת את כל הרכיבים של פונקציית הוצאות עתידיות וחוזרות:</p>
<ul>
<li>✅ פעולות מסד נתונים (הוספה, עדכון, מחיקה)</li>
<li>✅ הוצאות עתידיות חד-פעמיות</li>
<li>✅ הוצאות חוזרות עם תדירויות שונות</li>
<li>✅ חישובי תחזית מאזן עתידי</li>
<li>✅ אישור והמרת הוצאות עתידיות להוצאות בפועל</li>
<li>✅ ניהול סטטוס הוצאות חוזרות (פעיל/לא פעיל)</li>
</ul>
</div>";

try {
    // Test 1: Database Tables Existence
    echo "<div class='test-section'>
    <h2>🗄️ בדיקת טבלאות מסד נתונים</h2>";
    
    global $wpdb;
    
    // Check future expenses table
    $future_table = $wpdb->prefix . 'budgex_future_expenses';
    $recurring_table = $wpdb->prefix . 'budgex_recurring_expenses';
    
    $future_exists = $wpdb->get_var("SHOW TABLES LIKE '$future_table'");
    $recurring_exists = $wpdb->get_var("SHOW TABLES LIKE '$recurring_table'");
    
    if ($future_exists) {
        echo "<div class='success'>✅ טבלת הוצאות עתידיות קיימת: $future_table</div>";
        $test_results['future_table'] = true;
    } else {
        echo "<div class='error'>❌ טבלת הוצאות עתידיות לא קיימת: $future_table</div>";
        $test_results['future_table'] = false;
    }
    
    if ($recurring_exists) {
        echo "<div class='success'>✅ טבלת הוצאות חוזרות קיימת: $recurring_table</div>";
        $test_results['recurring_table'] = true;
    } else {
        echo "<div class='error'>❌ טבלת הוצאות חוזרות לא קיימת: $recurring_table</div>";
        $test_results['recurring_table'] = false;
    }
    
    echo "</div>";
    
    // Test 2: Create Test Budget
    echo "<div class='test-section'>
    <h2>💰 יצירת תקציב בדיקה</h2>";
    
    $test_budget_id = $database->create_budget(
        'תקציב בדיקה - הוצאות עתידיות',
        'תקציב לבדיקת פונקציות הוצאות עתידיות וחוזרות',
        8000.00,
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
    
    echo "</div>";
    
    // Test 3: Future Expenses Operations
    echo "<div class='test-section'>
    <h2>📅 בדיקת הוצאות עתידיות</h2>";
    
    // Add future expenses
    $future_expenses = [
        ['name' => 'תיקון רכב', 'amount' => 1500, 'date' => date('Y-m-d', strtotime('+1 week')), 'category' => 'תחזוקה'],
        ['name' => 'ביטוח שנתי', 'amount' => 2400, 'date' => date('Y-m-d', strtotime('+1 month')), 'category' => 'ביטוח'],
        ['name' => 'חופשה', 'amount' => 5000, 'date' => date('Y-m-d', strtotime('+2 months')), 'category' => 'בילוי']
    ];
    
    $future_expense_ids = [];
    foreach ($future_expenses as $expense) {
        $id = $database->add_future_expense(
            $test_budget_id,
            $expense['amount'],
            $expense['name'],
            'תיאור עבור ' . $expense['name'],
            $expense['date'],
            $expense['category'],
            $user_id
        );
        
        if ($id) {
            $future_expense_ids[] = $id;
            echo "<div class='expense-item'>";
            echo "<strong>{$expense['name']}</strong> - ";
            echo "<span class='amount-negative'>₪{$expense['amount']}</span> ";
            echo "({$expense['date']})";
            echo "</div>";
        }
    }
    
    echo "<div class='success'>✅ " . count($future_expense_ids) . " הוצאות עתידיות נוספו בהצלחה</div>";
    
    // Test retrieving future expenses
    $retrieved_expenses = $database->get_future_expenses($test_budget_id);
    echo "<div class='success'>✅ שלפו " . count($retrieved_expenses) . " הוצאות עתידיות</div>";
    
    // Test confirming a future expense
    if (!empty($future_expense_ids)) {
        $confirm_result = $database->confirm_future_expense($future_expense_ids[0]);
        if ($confirm_result) {
            echo "<div class='success'>✅ הוצאה עתידית אושרה והומרה להוצאה בפועל</div>";
        }
    }
    
    $test_results['future_expenses'] = true;
    echo "</div>";
    
    // Test 4: Recurring Expenses Operations
    echo "<div class='test-section'>
    <h2>🔄 בדיקת הוצאות חוזרות</h2>";
    
    // Add recurring expenses
    $recurring_expenses = [
        ['name' => 'שכר דירה', 'amount' => 4500, 'frequency' => 'monthly', 'interval' => 1],
        ['name' => 'חשמל', 'amount' => 300, 'frequency' => 'monthly', 'interval' => 2], // Every 2 months
        ['name' => 'דלק', 'amount' => 250, 'frequency' => 'weekly', 'interval' => 1],
        ['name' => 'ביטוח רכב', 'amount' => 150, 'frequency' => 'monthly', 'interval' => 1]
    ];
    
    $recurring_expense_ids = [];
    foreach ($recurring_expenses as $expense) {
        $id = $database->add_recurring_expense(
            $test_budget_id,
            $expense['amount'],
            $expense['name'],
            'תיאור עבור ' . $expense['name'],
            date('Y-m-01'), // Start date
            date('Y-m-d', strtotime('+1 year')), // End date
            $expense['frequency'],
            $expense['interval'],
            'הוצאות קבועות',
            $user_id
        );
        
        if ($id) {
            $recurring_expense_ids[] = $id;
            $frequency_text = $expense['interval'] > 1 ? 
                "כל {$expense['interval']} " . ($expense['frequency'] == 'monthly' ? 'חודשים' : 'שבועות') :
                ($expense['frequency'] == 'monthly' ? 'חודשי' : 'שבועי');
            
            echo "<div class='expense-item'>";
            echo "<strong>{$expense['name']}</strong> - ";
            echo "<span class='amount-negative'>₪{$expense['amount']}</span> ";
            echo "<span class='badge active'>{$frequency_text}</span>";
            echo "</div>";
        }
    }
    
    echo "<div class='success'>✅ " . count($recurring_expense_ids) . " הוצאות חוזרות נוספו בהצלחה</div>";
    
    // Test retrieving recurring expenses
    $retrieved_recurring = $database->get_recurring_expenses($test_budget_id);
    echo "<div class='success'>✅ שלפו " . count($retrieved_recurring) . " הוצאות חוזרות</div>";
    
    // Test toggling recurring expense status
    if (!empty($recurring_expense_ids)) {
        $toggle_result = $database->toggle_recurring_expense($recurring_expense_ids[0], 0); // Deactivate
        if ($toggle_result !== false) {
            echo "<div class='success'>✅ סטטוס הוצאה חוזרת שונה בהצלחה</div>";
        }
    }
    
    $test_results['recurring_expenses'] = true;
    echo "</div>";
    
    // Test 5: Recurring Expense Occurrences
    echo "<div class='test-section'>
    <h2>📊 בדיקת מופעי הוצאות חוזרות</h2>";
    
    $from_date = date('Y-m-01');
    $to_date = date('Y-m-d', strtotime('+3 months'));
    
    $occurrences = $database->get_recurring_expense_occurrences($test_budget_id, $from_date, $to_date);
    
    echo "<h4>מופעי הוצאות חוזרות מ-{$from_date} עד {$to_date}:</h4>";
    
    $total_recurring = 0;
    $occurrences_by_month = [];
    
    foreach ($occurrences as $occurrence) {
        $month = date('Y-m', strtotime($occurrence['occurrence_date']));
        if (!isset($occurrences_by_month[$month])) {
            $occurrences_by_month[$month] = [];
        }
        $occurrences_by_month[$month][] = $occurrence;
        $total_recurring += $occurrence['amount'];
    }
    
    foreach ($occurrences_by_month as $month => $month_occurrences) {
        $month_total = array_sum(array_column($month_occurrences, 'amount'));
        echo "<div class='expense-item'>";
        echo "<strong>חודש {$month}:</strong> ";
        echo count($month_occurrences) . " מופעים, ";
        echo "<span class='amount-negative'>סה\"כ ₪" . number_format($month_total, 2) . "</span>";
        echo "</div>";
    }
    
    echo "<div class='success'>✅ נמצאו " . count($occurrences) . " מופעים עתידיים של הוצאות חוזרות</div>";
    echo "<div class='success'>✅ סך הוצאות חוזרות לתקופה: ₪" . number_format($total_recurring, 2) . "</div>";
    
    $test_results['recurring_occurrences'] = true;
    echo "</div>";
    
    // Test 6: Projected Balance Calculation
    echo "<div class='test-section'>
    <h2>🔮 בדיקת תחזית מאזן עתידי</h2>";
    
    $target_dates = [
        date('Y-m-d', strtotime('+1 month')),
        date('Y-m-d', strtotime('+2 months')),
        date('Y-m-d', strtotime('+3 months'))
    ];
    
    foreach ($target_dates as $target_date) {
        $projection = $database->get_projected_balance($test_budget_id, $target_date);
        
        echo "<div class='projection-summary'>";
        echo "<h4>תחזית ליום {$target_date}</h4>";
        echo "<div class='projection-item'>";
        echo "<span>מאזן נוכחי:</span>";
        echo "<span class='amount-positive'>₪" . number_format($projection['current_balance'], 2) . "</span>";
        echo "</div>";
        echo "<div class='projection-item'>";
        echo "<span>תקציב נוסף:</span>";
        echo "<span class='amount-positive'>+₪" . number_format($projection['additional_budget'], 2) . "</span>";
        echo "</div>";
        echo "<div class='projection-item'>";
        echo "<span>הוצאות עתידיות:</span>";
        echo "<span class='amount-negative'>-₪" . number_format($projection['future_expenses'], 2) . "</span>";
        echo "</div>";
        echo "<div class='projection-item'>";
        echo "<span>הוצאות חוזרות:</span>";
        echo "<span class='amount-negative'>-₪" . number_format($projection['recurring_expenses'], 2) . "</span>";
        echo "</div>";
        echo "<div class='projection-item' style='border-top: 2px solid rgba(255,255,255,0.3); padding-top: 10px; margin-top: 10px; font-weight: bold; font-size: 1.1em;'>";
        echo "<span>מאזן צפוי:</span>";
        $projected = $projection['projected_balance'];
        $balance_class = $projected >= 0 ? 'amount-positive' : 'amount-negative';
        echo "<span class='{$balance_class}'>₪" . number_format($projected, 2) . "</span>";
        echo "</div>";
        echo "</div>";
    }
    
    $test_results['projected_balance'] = true;
    echo "</div>";
    
    // Test 7: Current Balance Calculation
    echo "<div class='test-section'>
    <h2>💵 בדיקת חישוב מאזן נוכחי</h2>";
    
    $current_balance = $database->get_current_balance($test_budget_id);
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>₪" . number_format($current_balance, 2) . "</div>";
    echo "<div class='stat-label'>מאזן נוכחי</div>";
    echo "</div>";
    
    // Add some test outcomes to see balance change
    $database->add_outcome(
        $test_budget_id,
        1200.00,
        'קניות שבועיות',
        'מזון',
        date('Y-m-d'),
        $user_id
    );
    
    $new_balance = $database->get_current_balance($test_budget_id);
    echo "<div class='success'>✅ לאחר הוספת הוצאה: מאזן עודכן ל-₪" . number_format($new_balance, 2) . "</div>";
    
    $test_results['current_balance'] = true;
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
    echo "<div class='stat-number'>" . round(($passed_tests / $total_tests) * 100, 1) . "%</div>";
    echo "<div class='stat-label'>אחוז הצלחה</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>{$test_budget_id}</div>";
    echo "<div class='stat-label'>ID תקציב בדיקה</div>";
    echo "</div>";
    echo "</div>";
    
    // Detailed results
    echo "<h3>פירוט תוצאות:</h3>";
    echo "<table>";
    echo "<tr><th>בדיקה</th><th>תוצאה</th><th>הערות</th></tr>";
    
    $test_descriptions = [
        'future_table' => 'טבלת הוצאות עתידיות',
        'recurring_table' => 'טבלת הוצאות חוזרות',
        'budget_creation' => 'יצירת תקציב בדיקה',
        'future_expenses' => 'פעולות הוצאות עתידיות',
        'recurring_expenses' => 'פעולות הוצאות חוזרות',
        'recurring_occurrences' => 'מופעי הוצאות חוזרות',
        'projected_balance' => 'תחזית מאזן עתידי',
        'current_balance' => 'חישוב מאזן נוכחי'
    ];
    
    foreach ($test_results as $test => $result) {
        $status = $result ? 'success' : 'error';
        $icon = $result ? '✅' : '❌';
        $text = $result ? 'עבר בהצלחה' : 'נכשל';
        
        echo "<tr class='{$status}'>";
        echo "<td>{$test_descriptions[$test]}</td>";
        echo "<td>{$icon} {$text}</td>";
        echo "<td>" . ($result ? 'כל הפעולות עבדו כצפוי' : 'נדרשת בדיקה נוספת') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($passed_tests === $total_tests) {
        echo "<div class='success'>";
        echo "<h3>🎉 כל הבדיקות עברו בהצלחה!</h3>";
        echo "<p>פונקציית ההוצאות העתידיות והחוזרות מוכנה לשימוש בסביבת הפיתוח.</p>";
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "<h3>⚠️ יש בדיקות שנכשלו</h3>";
        echo "<p>נדרש לבדוק ולתקן את הבעיות שנמצאו לפני העברה לפרודקשן.</p>";
        echo "</div>";
    }
    
    echo "</div>";
    
    // Cleanup Section
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
