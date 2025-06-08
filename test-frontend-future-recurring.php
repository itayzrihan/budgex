<?php
/**
 * Frontend Testing Script for Future and Recurring Expenses
 * 
 * This script tests the frontend interface elements for the
 * future and recurring expenses functionality.
 * 
 * @package Budgex
 */

// Basic WordPress environment check
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

echo "<!DOCTYPE html>
<html dir='rtl' lang='he'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>בדיקת ממשק משתמש - הוצאות עתידיות וחוזרות</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .test-container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .test-section { margin: 25px 0; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .success { background: #f0f9ff; border-color: #0ea5e9; color: #0c4a6e; }
        .error { background: #fef2f2; border-color: #ef4444; color: #991b1b; }
        .warning { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
        .info { background: #f8fafc; border-color: #64748b; color: #334155; }
        .status { padding: 4px 8px; border-radius: 4px; font-weight: 500; margin-left: 8px; }
        .status.success { background: #dcfce7; color: #166534; }
        .status.error { background: #fee2e2; color: #991b1b; }
        .component-check { background: #f8fafc; padding: 12px; border-radius: 6px; margin: 8px 0; border-right: 4px solid #3b82f6; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 15px 0; }
        .stat-card { background: #fafafa; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #e5e5e5; }
        .code-preview { background: #f1f5f9; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 14px; overflow-x: auto; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: right; border: 1px solid #e5e5e5; }
        th { background: #f9fafb; font-weight: 600; }
        tr.success { background: #f0fdf4; }
        tr.error { background: #fef2f2; }
    </style>
</head>
<body>
<div class='test-container'>";

echo "<h1>🖥️ בדיקת ממשק משתמש - הוצאות עתידיות וחוזרות</h1>";

// Initialize test results
$frontend_tests = [];
$total_tests = 0;
$passed_tests = 0;

// Helper function to run tests
function run_test($name, $test_function) {
    global $total_tests, $passed_tests;
    $total_tests++;
    
    try {
        $result = $test_function();
        if ($result) {
            $passed_tests++;
            return ['status' => 'success', 'message' => 'עבר'];
        } else {
            return ['status' => 'error', 'message' => 'נכשל'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'שגיאה: ' . $e->getMessage()];
    }
}

echo "<div class='test-section info'>
<h2>ℹ️ מידע על בדיקת הממשק</h2>
<p>בדיקה זו בוחנת את רכיבי הממשק עבור:</p>
<ul>
<li>✅ טפסי הוספת הוצאות עתידיות</li>
<li>✅ טפסי הוספת הוצאות חוזרות</li>
<li>✅ תצוגת רשימות הוצאות</li>
<li>✅ כפתורי פעולה וניהול</li>
<li>✅ תחזית מאזן עתידי</li>
<li>✅ עיצוב ועקביות ויזואלית</li>
</ul>
</div>";

// Test 1: Budget Page Template
echo "<div class='test-section'>
<h2>📄 בדיקת תבנית עמוד תקציב</h2>";

$budget_page_file = 'public/partials/budgex-budget-page.php';
if (file_exists($budget_page_file)) {
    $budget_content = file_get_contents($budget_page_file);
    
    // Check for management section
    $result = run_test('Management Section', function() use ($budget_content) {
        return strpos($budget_content, 'management-section') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Management Section</strong> - קטע ניהול הוצאות עתידיות וחוזרות";
    echo "</div>";
    
    // Check for future expenses form
    $result = run_test('Future Expenses Form', function() use ($budget_content) {
        return strpos($budget_content, 'add-future-expense-form') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Future Expenses Form</strong> - טופס הוספת הוצאות עתידיות";
    echo "</div>";
    
    // Check for recurring expenses form
    $result = run_test('Recurring Expenses Form', function() use ($budget_content) {
        return strpos($budget_content, 'add-recurring-expense-form') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Recurring Expenses Form</strong> - טופס הוספת הוצאות חוזרות";
    echo "</div>";
    
    // Check for projected balance form
    $result = run_test('Projected Balance Form', function() use ($budget_content) {
        return strpos($budget_content, 'projected-balance-form') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Projected Balance Form</strong> - טופס תחזית מאזן עתידי";
    echo "</div>";
    
    // Check for AJAX JavaScript functions
    $result = run_test('AJAX Functions', function() use ($budget_content) {
        return strpos($budget_content, 'loadFutureExpenses()') !== false && 
               strpos($budget_content, 'loadRecurringExpenses()') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>AJAX Functions</strong> - פונקציות טעינת נתונים";
    echo "</div>";
    
} else {
    echo "<div class='error'>❌ קובץ תבנית עמוד תקציב לא נמצא: {$budget_page_file}</div>";
}

echo "</div>";

// Test 2: Form Fields and Validation
echo "<div class='test-section'>
<h2>📝 בדיקת שדות טפסים</h2>";

if (isset($budget_content)) {
    // Future expense form fields
    $future_fields = [
        'future_expense_name' => 'שם הוצאה עתידית',
        'future_expense_amount' => 'סכום הוצאה עתידית',
        'future_expense_date' => 'תאריך הוצאה עתידית',
        'future_expense_category' => 'קטגוריה הוצאה עתידית',
        'future_expense_description' => 'תיאור הוצאה עתידית'
    ];
    
    echo "<h4>שדות טופס הוצאות עתידיות:</h4>";
    foreach ($future_fields as $field_id => $field_name) {
        $result = run_test($field_name, function() use ($budget_content, $field_id) {
            return strpos($budget_content, "id=\"{$field_id}\"") !== false;
        });
        echo "<div class='component-check'>";
        echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
        echo "<strong>{$field_name}</strong>";
        echo "</div>";
    }
    
    // Recurring expense form fields
    $recurring_fields = [
        'recurring_expense_name' => 'שם הוצאה חוזרת',
        'recurring_expense_amount' => 'סכום הוצאה חוזרת',
        'recurring_expense_start_date' => 'תאריך התחלה',
        'recurring_expense_end_date' => 'תאריך סיום',
        'recurring_expense_frequency' => 'תדירות',
        'recurring_expense_interval' => 'מרווח תדירות',
        'recurring_expense_category' => 'קטגוריה הוצאה חוזרת'
    ];
    
    echo "<h4>שדות טופס הוצאות חוזרות:</h4>";
    foreach ($recurring_fields as $field_id => $field_name) {
        $result = run_test($field_name, function() use ($budget_content, $field_id) {
            return strpos($budget_content, "id=\"{$field_id}\"") !== false;
        });
        echo "<div class='component-check'>";
        echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
        echo "<strong>{$field_name}</strong>";
        echo "</div>";
    }
}

echo "</div>";

// Test 3: CSS Styling
echo "<div class='test-section'>
<h2>🎨 בדיקת עיצוב CSS</h2>";

$css_file = 'public/css/budgex-public.css';
if (file_exists($css_file)) {
    $css_content = file_get_contents($css_file);
    
    // Check for management cards styling
    $result = run_test('Management Cards Style', function() use ($css_content) {
        return strpos($css_content, '.management-card') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Management Cards</strong> - עיצוב כרטיסי ניהול";
    echo "</div>";
    
    // Check for form sections styling
    $result = run_test('Form Sections Style', function() use ($css_content) {
        return strpos($css_content, '.form-section') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Form Sections</strong> - עיצוב קטעי טפסים";
    echo "</div>";
    
    // Check for expenses table styling
    $result = run_test('Expenses Table Style', function() use ($css_content) {
        return strpos($css_content, '.expenses-table') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Expenses Table</strong> - עיצוב טבלת הוצאות";
    echo "</div>";
    
    // Check for status badges styling
    $result = run_test('Status Badges Style', function() use ($css_content) {
        return strpos($css_content, '.status-badge') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Status Badges</strong> - עיצוב תגי סטטוס";
    echo "</div>";
    
    // Check for projected balance styling
    $result = run_test('Projected Balance Style', function() use ($css_content) {
        return strpos($css_content, '.projected-balance') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Projected Balance</strong> - עיצוב תחזית מאזן";
    echo "</div>";
    
} else {
    echo "<div class='error'>❌ קובץ CSS לא נמצא: {$css_file}</div>";
}

echo "</div>";

// Test 4: JavaScript Functionality
echo "<div class='test-section'>
<h2>⚡ בדיקת פונקציונליות JavaScript</h2>";

if (isset($budget_content)) {
    // Check for form submission handlers
    $result = run_test('Form Submission Handlers', function() use ($budget_content) {
        return strpos($budget_content, "#add-future-expense-form').on('submit'") !== false &&
               strpos($budget_content, "#add-recurring-expense-form').on('submit'") !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Form Submission Handlers</strong> - מטפלי שליחת טפסים";
    echo "</div>";
    
    // Check for expense action handlers
    $result = run_test('Expense Action Handlers', function() use ($budget_content) {
        return strpos($budget_content, 'confirm-future-expense') !== false &&
               strpos($budget_content, 'delete-future-expense') !== false &&
               strpos($budget_content, 'toggle-recurring-expense') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Expense Action Handlers</strong> - מטפלי פעולות הוצאות";
    echo "</div>";
    
    // Check for display functions
    $result = run_test('Display Functions', function() use ($budget_content) {
        return strpos($budget_content, 'displayFutureExpenses') !== false &&
               strpos($budget_content, 'displayRecurringExpenses') !== false &&
               strpos($budget_content, 'displayProjectedBalance') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Display Functions</strong> - פונקציות תצוגה";
    echo "</div>";
    
    // Check for helper functions
    $result = run_test('Helper Functions', function() use ($budget_content) {
        return strpos($budget_content, 'getFrequencyText') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Helper Functions</strong> - פונקציות עזר";
    echo "</div>";
}

echo "</div>";

// Test 5: AJAX Actions and Integration
echo "<div class='test-section'>
<h2>🔗 בדיקת פעולות AJAX</h2>";

$public_file = 'public/class-budgex-public.php';
if (file_exists($public_file)) {
    $public_content = file_get_contents($public_file);
    
    $ajax_actions = [
        'ajax_add_future_expense' => 'הוספת הוצאה עתידית',
        'ajax_edit_future_expense' => 'עריכת הוצאה עתידית',
        'ajax_delete_future_expense' => 'מחיקת הוצאה עתידית',
        'ajax_confirm_future_expense' => 'אישור הוצאה עתידית',
        'ajax_get_future_expenses' => 'שליפת הוצאות עתידיות',
        'ajax_add_recurring_expense' => 'הוספת הוצאה חוזרת',
        'ajax_edit_recurring_expense' => 'עריכת הוצאה חוזרת',
        'ajax_delete_recurring_expense' => 'מחיקת הוצאה חוזרת',
        'ajax_toggle_recurring_expense' => 'שינוי סטטוס הוצאה חוזרת',
        'ajax_get_recurring_expenses' => 'שליפת הוצאות חוזרות',
        'ajax_get_projected_balance' => 'חישוב תחזית מאזן'
    ];
    
    foreach ($ajax_actions as $action => $description) {
        $result = run_test($description, function() use ($public_content, $action) {
            return strpos($public_content, "function {$action}()") !== false;
        });
        echo "<div class='component-check'>";
        echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
        echo "<strong>{$description}</strong>";
        echo "</div>";
    }
} else {
    echo "<div class='error'>❌ קובץ מחלקה ציבורית לא נמצא: {$public_file}</div>";
}

echo "</div>";

// Test 6: Responsive Design
echo "<div class='test-section'>
<h2>📱 בדיקת עיצוב רספונסיבי</h2>";

if (isset($css_content)) {
    // Check for mobile media queries
    $result = run_test('Mobile Media Queries', function() use ($css_content) {
        return strpos($css_content, '@media') !== false && 
               strpos($css_content, 'max-width') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Mobile Media Queries</strong> - שאילתות מדיה למובייל";
    echo "</div>";
    
    // Check for flexible grid
    $result = run_test('Flexible Grid', function() use ($css_content) {
        return strpos($css_content, 'flex') !== false || 
               strpos($css_content, 'grid') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Flexible Grid</strong> - רשת גמישה";
    echo "</div>";
    
    // Check for responsive tables
    $result = run_test('Responsive Tables', function() use ($css_content) {
        return strpos($css_content, 'overflow-x') !== false;
    });
    echo "<div class='component-check'>";
    echo "<span class='status {$result['status']}'>{$result['message']}</span> ";
    echo "<strong>Responsive Tables</strong> - טבלאות רספונסיביות";
    echo "</div>";
}

echo "</div>";

// Summary
$success_rate = $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 1) : 0;

echo "<div class='test-section " . ($success_rate >= 90 ? 'success' : ($success_rate >= 70 ? 'warning' : 'error')) . "'>";
echo "<h2>📊 סיכום בדיקת ממשק משתמש</h2>";

echo "<div class='grid'>";
echo "<div class='stat-card'>";
echo "<div style='font-size: 24px; font-weight: bold; color: #059669;'>{$passed_tests}/{$total_tests}</div>";
echo "<div style='font-size: 14px; color: #6b7280; margin-top: 5px;'>בדיקות עברו בהצלחה</div>";
echo "</div>";

echo "<div class='stat-card'>";
echo "<div style='font-size: 24px; font-weight: bold; color: #059669;'>{$success_rate}%</div>";
echo "<div style='font-size: 14px; color: #6b7280; margin-top: 5px;'>אחוז הצלחה</div>";
echo "</div>";
echo "</div>";

if ($success_rate >= 90) {
    echo "<div class='success'>";
    echo "<h3>🎉 ממשק המשתמש מוכן!</h3>";
    echo "<p>כל רכיבי הממשק עבור הוצאות עתידיות וחוזרות נמצאים במקום ועובדים כצפוי.</p>";
    echo "</div>";
} elseif ($success_rate >= 70) {
    echo "<div class='warning'>";
    echo "<h3>⚠️ ממשק המשתמש זקוק לתיקונים קלים</h3>";
    echo "<p>רוב הרכיבים עובדים, אך יש כמה נושאים שדורשים תשומת לב.</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>❌ ממשק המשתמש זקוק לעבודה נוספת</h3>";
    echo "<p>יש מספר רכיבים חסרים או לא פועלים כצפוי.</p>";
    echo "</div>";
}

echo "<h3>המלצות לשלב הבא:</h3>";
echo "<ul>";
echo "<li>🧪 <strong>בדיקות משתמש:</strong> בצע בדיקות עם משתמשים אמיתיים</li>";
echo "<li>📱 <strong>בדיקה במכשירים שונים:</strong> בדוק תצוגה במובייל וטאבלט</li>";
echo "<li>🌐 <strong>בדיקת דפדפנים:</strong> וודא תאימות לדפדפנים שונים</li>";
echo "<li>⚡ <strong>בדיקת ביצועים:</strong> בדוק זמני טעינה ותגובה</li>";
echo "<li>🎯 <strong>בדיקת נגישות:</strong> וודא נגישות למשתמשים עם מוגבלויות</li>";
echo "</ul>";

echo "</div>";

echo "</div>
</body>
</html>";
?>
