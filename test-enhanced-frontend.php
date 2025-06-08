<?php
/**
 * Test Enhanced Frontend Integration for Budgex Plugin
 * 
 * This file tests the enhanced budget functionality integration
 * specifically focusing on the public enhanced budget page.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for proper output
header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Frontend Test</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            direction: rtl;
            background: #f1f1f1;
        }
        .test-container { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            max-width: 800px; 
            margin: 0 auto; 
        }
        .success { color: #46b450; }
        .error { color: #dc3232; }
        .warning { color: #ffba00; }
        .test-item { 
            margin: 10px 0; 
            padding: 8px; 
            border-left: 4px solid #ddd; 
            background: #f9f9f9; 
        }
        .test-item.pass { border-color: #46b450; }
        .test-item.fail { border-color: #dc3232; }
        .test-item.warn { border-color: #ffba00; }
    </style>
</head>
<body>

<div class="test-container">
    <h1>🎯 בדיקת אינטגרציה של תכונות מתקדמות</h1>
    <p><strong>תאריך:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>

    <?php
    $tests_passed = 0;
    $tests_total = 0;
    
    function test_result($name, $condition, $description = '') {
        global $tests_passed, $tests_total;
        $tests_total++;
        
        if ($condition) {
            $tests_passed++;
            $class = 'pass';
            $icon = '✅';
            $status = 'הצלחה';
        } else {
            $class = 'fail';
            $icon = '❌';
            $status = 'שגיאה';
        }
        
        echo "<div class='test-item {$class}'>";
        echo "<strong>{$icon} {$name}:</strong> {$status}";
        if ($description) {
            echo "<br><small>{$description}</small>";
        }
        echo "</div>";
        
        return $condition;
    }

    // Test 1: File Structure
    echo "<h2>📁 Test 1: מבנה קבצים</h2>";
    
    test_result(
        "Public Enhanced Budget Template", 
        file_exists(__DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php'),
        "קובץ התבנית לעמוד התקציב המתקדם הציבורי"
    );
    
    test_result(
        "Admin Enhanced Budget Template", 
        file_exists(__DIR__ . '/public/partials/budgex-admin-enhanced-budget-page.php'),
        "קובץ התבנית לעמוד התקציב המתקדם בניהול"
    );
    
    test_result(
        "Enhanced CSS", 
        file_exists(__DIR__ . '/public/css/budgex-enhanced-budget.css'),
        "קובץ העיצוב המתקדם"
    );
    
    test_result(
        "Enhanced JavaScript", 
        file_exists(__DIR__ . '/public/js/budgex-enhanced-budget.js'),
        "קובץ הסקריפט המתקדם"
    );

    // Test 2: Class Files
    echo "<h2>🏗️ Test 2: קבצי מחלקות</h2>";
    
    test_result(
        "Public Class File", 
        file_exists(__DIR__ . '/public/class-budgex-public.php'),
        "מחלקת הציבור הראשית"
    );
    
    test_result(
        "Database Class File", 
        file_exists(__DIR__ . '/includes/class-database.php'),
        "מחלקת בסיס הנתונים"
    );
    
    test_result(
        "Permissions Class File", 
        file_exists(__DIR__ . '/includes/class-permissions.php'),
        "מחלקת ההרשאות"
    );

    // Test 3: Template Content
    echo "<h2>📄 Test 3: תוכן תבניות</h2>";
    
    $public_template = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';
    if (file_exists($public_template)) {
        $content = file_get_contents($public_template);
          test_result(
            "Tabbed Interface", 
            strpos($content, 'budget-tabs') !== false || strpos($content, 'tab-navigation') !== false,
            "האם הממשק המכויל קיים בתבנית"
        );
        
        test_result(
            "Enhanced Features", 
            strpos($content, 'enhanced') !== false || strpos($content, 'budget-details') !== false,
            "האם התכונות המתקדמות קיימות"
        );
        
        test_result(
            "RTL Support", 
            strpos($content, 'rtl') !== false || strpos($content, 'direction') !== false,
            "תמיכה בכיוון ימין לשמאל"
        );
    }

    // Test 4: Public Class Methods
    echo "<h2>⚙️ Test 4: שיטות מחלקת הציבור</h2>";
    
    $public_class_file = __DIR__ . '/public/class-budgex-public.php';
    if (file_exists($public_class_file)) {
        $content = file_get_contents($public_class_file);
        
        test_result(
            "Enhanced Budget Method", 
            strpos($content, 'display_enhanced_budget') !== false,
            "שיטת הצגת תקציב מתקדם"
        );
        
        test_result(
            "Public Template Include", 
            strpos($content, 'budgex-public-enhanced-budget-page.php') !== false,
            "כלול התבנית הציבורית הנכונה"
        );
        
        test_result(
            "Enhanced Page Render", 
            strpos($content, 'render_enhanced_budget_page') !== false,
            "שיטת רינדור עמוד מתקדם"
        );
        
        test_result(
            "Shortcode Handler", 
            strpos($content, 'shortcode') !== false || strpos($content, 'budgex_page') !== false,
            "מטפל קיצורי דרך"
        );
    }

    // Test 5: CSS and JavaScript
    echo "<h2>🎨 Test 5: עיצוב וסקריפטים</h2>";
    
    $css_file = __DIR__ . '/public/css/budgex-enhanced-budget.css';
    if (file_exists($css_file)) {
        $css_content = file_get_contents($css_file);
        
        test_result(
            "Tab Styles", 
            strpos($css_content, '.budgex-tabs') !== false || strpos($css_content, 'tab') !== false,
            "עיצובי כרטיסיות"
        );
        
        test_result(
            "RTL Styles", 
            strpos($css_content, 'rtl') !== false || strpos($css_content, 'direction') !== false,
            "עיצובי ימין לשמאל"
        );
    }
    
    $js_file = __DIR__ . '/public/js/budgex-enhanced-budget.js';
    if (file_exists($js_file)) {
        $js_content = file_get_contents($js_file);
        
        test_result(
            "Tab Functionality", 
            strpos($js_content, 'tab') !== false || strpos($js_content, 'click') !== false,
            "פונקציונליות כרטיסיות"
        );
        
        test_result(
            "AJAX Integration", 
            strpos($js_content, 'ajax') !== false || strpos($js_content, 'jQuery') !== false,
            "אינטגרציה עם AJAX"
        );
    }

    // Test 6: Configuration
    echo "<h2>⚙️ Test 6: הגדרות</h2>";
    
    $main_plugin_file = __DIR__ . '/budgex.php';
    if (file_exists($main_plugin_file)) {
        $plugin_content = file_get_contents($main_plugin_file);
        
        test_result(
            "Plugin Header", 
            strpos($plugin_content, 'Plugin Name') !== false,
            "כותרת התוסף"
        );
        
        test_result(
            "Version Declaration", 
            strpos($plugin_content, 'Version') !== false,
            "הצהרת גרסה"
        );
    }

    // Summary
    echo "<h2>📊 סיכום</h2>";
    
    $success_rate = ($tests_total > 0) ? round(($tests_passed / $tests_total) * 100, 1) : 0;
    
    if ($success_rate >= 90) {
        $status_class = 'success';
        $status_icon = '🎉';
        $status_text = 'מצוין!';
    } elseif ($success_rate >= 70) {
        $status_class = 'warning';
        $status_icon = '⚠️';
        $status_text = 'דורש תיקונים קלים';
    } else {
        $status_class = 'error';
        $status_icon = '❌';
        $status_text = 'דורש תיקונים משמעותיים';
    }
    
    echo "<div class='test-item {$status_class}'>";
    echo "<h3>{$status_icon} {$status_text}</h3>";
    echo "<p><strong>עברו בהצלחה:</strong> {$tests_passed} מתוך {$tests_total} בדיקות ({$success_rate}%)</p>";
    echo "</div>";

    // Next Steps
    echo "<h2>🚀 שלבים הבאים</h2>";
    echo "<div class='test-item'>";
    echo "<h4>פעולות מומלצות:</h4>";
    echo "<ul>";
    echo "<li>הפעל את התוסף בסביבת וורדפרס</li>";
    echo "<li>נווט לעמוד <code>/budgex/budget/1/</code> לבדיקה</li>";
    echo "<li>בדוק את הממשק המכויל בדפדפן</li>";
    echo "<li>וודא תפקוד נכון של AJAX</li>";
    echo "<li>בדוק תאימות נייד</li>";
    echo "</ul>";
    echo "</div>";

    ?>

</div>

</body>
</html>
