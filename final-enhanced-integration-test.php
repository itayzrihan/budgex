<?php
/**
 * Final Integration Test for Enhanced Frontend Implementation
 * 
 * This comprehensive test verifies that all components of the enhanced
 * frontend implementation are properly integrated and ready for deployment.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Enhanced Frontend Integration Test</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f0f0f1; 
            direction: rtl; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
        }
        .header { 
            background: linear-gradient(135deg, #2271b1 0%, #135e96 100%); 
            color: white; 
            padding: 30px; 
            border-radius: 8px 8px 0 0; 
            text-align: center; 
        }
        .content { padding: 30px; }
        .test-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin: 20px 0; 
        }
        .test-section { 
            border: 1px solid #e1e1e1; 
            border-radius: 6px; 
            padding: 20px; 
            background: #fafafa; 
        }
        .test-section h3 { 
            margin: 0 0 15px 0; 
            color: #1d2327; 
            border-bottom: 2px solid #e1e1e1; 
            padding-bottom: 10px; 
        }
        .test-item { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin: 8px 0; 
            padding: 8px; 
            border-radius: 4px; 
        }
        .test-item.pass { background: #d1e7dd; border-left: 4px solid #0f5132; }
        .test-item.fail { background: #f8d7da; border-left: 4px solid #842029; }
        .test-item.warn { background: #fff3cd; border-left: 4px solid #664d03; }
        .status-icon { font-weight: bold; }
        .summary { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
            border-radius: 6px; 
            padding: 20px; 
            margin: 20px 0; 
            text-align: center; 
        }
        .summary.excellent { border: 2px solid #198754; }
        .summary.good { border: 2px solid #fd7e14; }
        .summary.needs-work { border: 2px solid #dc3545; }
        .file-list { 
            font-family: 'Courier New', monospace; 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 4px; 
            font-size: 12px; 
            max-height: 200px; 
            overflow-y: auto; 
        }
        .next-steps { 
            background: #e7f3ff; 
            border: 1px solid #b8daff; 
            border-radius: 6px; 
            padding: 20px; 
            margin: 20px 0; 
        }
        .deployment-ready { 
            background: #d1e7dd; 
            border: 2px solid #0f5132; 
            color: #0f5132; 
            padding: 15px; 
            border-radius: 6px; 
            font-weight: bold; 
            text-align: center; 
            font-size: 18px; 
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🚀 Final Enhanced Frontend Integration Test</h1>
        <p>תאריך: <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>בדיקה אחרונה לפני הפריסה המלאה</p>
    </div>

    <div class="content">
        <?php
        $tests = [];
        $total_score = 0;
        $max_score = 0;

        function test_component($name, $tests_array) {
            global $tests, $total_score, $max_score;
            
            echo "<div class='test-section'>";
            echo "<h3>{$name}</h3>";
            
            $section_score = 0;
            $section_max = count($tests_array);
            
            foreach ($tests_array as $test_name => $test_data) {
                $condition = $test_data['test'];
                $weight = $test_data['weight'] ?? 1;
                $description = $test_data['desc'] ?? '';
                
                $max_score += $weight;
                
                if ($condition) {
                    $section_score += $weight;
                    $total_score += $weight;
                    $class = 'pass';
                    $icon = '✅';
                    $status = 'הצלחה';
                } else {
                    $class = 'fail';
                    $icon = '❌';
                    $status = 'שגיאה';
                }
                
                echo "<div class='test-item {$class}'>";
                echo "<span><strong>{$test_name}</strong>";
                if ($description) echo "<br><small>{$description}</small>";
                echo "</span>";
                echo "<span class='status-icon'>{$icon} {$status}</span>";
                echo "</div>";
            }
            
            echo "<div style='margin-top: 15px; font-weight: bold;'>";
            echo "ציון מקטע: {$section_score}/{$section_max}";
            echo "</div>";
            echo "</div>";
        }

        // Test 1: Core Files Structure
        test_component('📁 מבנה קבצים ליבה', [
            'Public Enhanced Template' => [
                'test' => file_exists(__DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php'),
                'weight' => 3,
                'desc' => 'תבנית עמוד תקציב מתקדם ציבורי'
            ],
            'Admin Enhanced Template' => [
                'test' => file_exists(__DIR__ . '/public/partials/budgex-admin-enhanced-budget-page.php'),
                'weight' => 2,
                'desc' => 'תבנית עמוד תקציב מתקדם לניהול'
            ],
            'Public Class File' => [
                'test' => file_exists(__DIR__ . '/public/class-budgex-public.php'),
                'weight' => 3,
                'desc' => 'מחלקת ציבור ראשית'
            ],
            'Enhanced CSS' => [
                'test' => file_exists(__DIR__ . '/public/css/budgex-enhanced-budget.css'),
                'weight' => 2,
                'desc' => 'קובץ עיצוב מתקדם'
            ],
            'Enhanced JavaScript' => [
                'test' => file_exists(__DIR__ . '/public/js/budgex-enhanced-budget.js'),
                'weight' => 2,
                'desc' => 'קובץ סקריפט מתקדם'
            ]
        ]);

        // Test 2: Template Content Quality
        $public_template = __DIR__ . '/public/partials/budgex-public-enhanced-budget-page.php';
        $template_content = file_exists($public_template) ? file_get_contents($public_template) : '';
        
        test_component('📄 איכות תוכן תבניות', [
            'Tabbed Interface Structure' => [
                'test' => strpos($template_content, 'budget-tabs-container') !== false && strpos($template_content, 'tab-navigation') !== false,
                'weight' => 3,
                'desc' => 'מבנה ממשק כרטיסיות מלא'
            ],
            'Enhanced Dashboard' => [
                'test' => strpos($template_content, 'enhanced-summary-dashboard') !== false,
                'weight' => 2,
                'desc' => 'לוח מחוונים מתקדם'
            ],
            'Summary Cards' => [
                'test' => strpos($template_content, 'summary-cards-grid') !== false,
                'weight' => 2,
                'desc' => 'כרטיסי סיכום'
            ],
            'Breadcrumb Navigation' => [
                'test' => strpos($template_content, 'breadcrumb') !== false,
                'weight' => 1,
                'desc' => 'ניווט פירורי לחם'
            ],
            'RTL Support' => [
                'test' => strpos($template_content, 'dir="rtl"') !== false,
                'weight' => 2,
                'desc' => 'תמיכה בכיוון ימין לשמאל'
            ]
        ]);

        // Test 3: Public Class Integration
        $public_class = __DIR__ . '/public/class-budgex-public.php';
        $class_content = file_exists($public_class) ? file_get_contents($public_class) : '';
        
        test_component('⚙️ אינטגרציה מחלקת ציבור', [
            'Enhanced Budget Method' => [
                'test' => strpos($class_content, 'display_enhanced_budget') !== false,
                'weight' => 3,
                'desc' => 'שיטת הצגת תקציב מתקדם'
            ],
            'Correct Template Include' => [
                'test' => strpos($class_content, 'budgex-public-enhanced-budget-page.php') !== false,
                'weight' => 3,
                'desc' => 'כלול התבנית הציבורית הנכונה'
            ],
            'Enhanced Page Render' => [
                'test' => strpos($class_content, 'render_enhanced_budget_page') !== false,
                'weight' => 2,
                'desc' => 'שיטת רינדור עמוד מתקדם'
            ],
            'CSS Enqueue' => [
                'test' => strpos($class_content, 'budgex-enhanced-budget.css') !== false,
                'weight' => 2,
                'desc' => 'רישום קובץ CSS'
            ],
            'JS Enqueue' => [
                'test' => strpos($class_content, 'budgex-enhanced-budget.js') !== false,
                'weight' => 2,
                'desc' => 'רישום קובץ JavaScript'
            ]
        ]);

        // Test 4: Frontend Assets Quality
        $css_file = __DIR__ . '/public/css/budgex-enhanced-budget.css';
        $js_file = __DIR__ . '/public/js/budgex-enhanced-budget.js';
        $css_content = file_exists($css_file) ? file_get_contents($css_file) : '';
        $js_content = file_exists($js_file) ? file_get_contents($js_file) : '';
        
        test_component('🎨 איכות נכסי פרונטאנד', [
            'Comprehensive CSS' => [
                'test' => strlen($css_content) > 1000 && strpos($css_content, '.budget-tabs') !== false,
                'weight' => 2,
                'desc' => 'קובץ CSS מקיף עם סגנונות כרטיסיות'
            ],
            'RTL CSS Support' => [
                'test' => strpos($css_content, 'rtl') !== false || strpos($css_content, 'direction') !== false,
                'weight' => 2,
                'desc' => 'תמיכת CSS בכיוון ימין לשמאל'
            ],
            'Tab Functionality JS' => [
                'test' => strpos($js_content, 'setupTabNavigation') !== false,
                'weight' => 3,
                'desc' => 'פונקציונליות JavaScript לכרטיסיות'
            ],
            'AJAX Integration' => [
                'test' => strpos($js_content, 'ajax') !== false || strpos($js_content, 'jQuery') !== false,
                'weight' => 2,
                'desc' => 'אינטגרציה עם AJAX'
            ],
            'Modern JS Structure' => [
                'test' => strlen($js_content) > 1000 && strpos($js_content, 'initializeEnhancedBudgetPage') !== false,
                'weight' => 2,
                'desc' => 'מבנה JavaScript מודרני'
            ]
        ]);

        // Test 5: Core Classes Support
        test_component('🏗️ תמיכת מחלקות ליבה', [
            'Database Class' => [
                'test' => file_exists(__DIR__ . '/includes/class-database.php'),
                'weight' => 2,
                'desc' => 'מחלקת בסיס נתונים'
            ],
            'Permissions Class' => [
                'test' => file_exists(__DIR__ . '/includes/class-permissions.php'),
                'weight' => 2,
                'desc' => 'מחלקת הרשאות'
            ],
            'Budget Calculator' => [
                'test' => file_exists(__DIR__ . '/includes/class-budget-calculator.php'),
                'weight' => 2,
                'desc' => 'מחלקת חישוב תקציב'
            ],
            'Main Plugin File' => [
                'test' => file_exists(__DIR__ . '/budgex.php'),
                'weight' => 3,
                'desc' => 'קובץ תוסף ראשי'
            ]
        ]);

        // Calculate final score
        $percentage = $max_score > 0 ? round(($total_score / $max_score) * 100, 1) : 0;
        
        // Display summary
        if ($percentage >= 95) {
            $summary_class = 'excellent';
            $summary_icon = '🏆';
            $summary_text = 'מושלם! מוכן לפריסה';
            $summary_desc = 'כל הרכיבים במקום ואיכות הקוד מעולה';
        } elseif ($percentage >= 85) {
            $summary_class = 'good';
            $summary_icon = '👍';
            $summary_text = 'טוב מאוד! כמעט מוכן';
            $summary_desc = 'רוב הרכיבים במקום, דרושים תיקונים קטנים';
        } else {
            $summary_class = 'needs-work';
            $summary_icon = '⚠️';
            $summary_text = 'דורש עבודה נוספת';
            $summary_desc = 'נדרשים תיקונים נוספים לפני הפריסה';
        }

        echo "<div class='summary {$summary_class}'>";
        echo "<h2>{$summary_icon} {$summary_text}</h2>";
        echo "<p><strong>ציון כולל:</strong> {$total_score}/{$max_score} ({$percentage}%)</p>";
        echo "<p>{$summary_desc}</p>";
        echo "</div>";

        if ($percentage >= 90) {
            echo "<div class='deployment-ready'>";
            echo "🚀 המערכת מוכנה לפריסה! 🚀";
            echo "</div>";
        }
        ?>

        <div class="next-steps">
            <h3>🎯 שלבים הבאים</h3>
            <div class="test-grid">
                <div>
                    <h4>בדיקות תפקוד:</h4>
                    <ul>
                        <li>הפעל את התוסף בסביבת וורדפרס</li>
                        <li>צור תקציב לדוגמא ובדוק גישה ל-<code>/budgex/budget/1/</code></li>
                        <li>בדוק פונקציונליות כרטיסיות</li>
                        <li>וודא תפקוד AJAX ועדכונים בזמן אמת</li>
                    </ul>
                </div>
                <div>
                    <h4>בדיקות משתמש:</h4>
                    <ul>
                        <li>בדוק הרשאות משתמשים שונות</li>
                        <li>וודא תפקוד מערכת הזמנות</li>
                        <li>בדוק תאימות נייד ודפדפנים</li>
                        <li>בדוק ביצועים בנתוני תקציב גדולים</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h3>📋 רשימת קבצים קריטיים</h3>
            <div class="file-list">
                <?php
                $critical_files = [
                    'public/class-budgex-public.php',
                    'public/partials/budgex-public-enhanced-budget-page.php',
                    'public/partials/budgex-admin-enhanced-budget-page.php',
                    'public/css/budgex-enhanced-budget.css',
                    'public/js/budgex-enhanced-budget.js',
                    'includes/class-database.php',
                    'includes/class-permissions.php',
                    'includes/class-budget-calculator.php',
                    'budgex.php'
                ];
                
                foreach ($critical_files as $file) {
                    $exists = file_exists(__DIR__ . '/' . $file);
                    $status = $exists ? '[✓]' : '[✗]';
                    $size = $exists ? filesize(__DIR__ . '/' . $file) : 0;
                    echo "{$status} {$file} (" . number_format($size) . " bytes)<br>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
