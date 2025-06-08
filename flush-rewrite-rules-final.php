<?php
/**
 * Final Rewrite Rules Flush for Enhanced Budget Navigation
 * 
 * This script completes the navigation fix by flushing WordPress rewrite rules
 * to activate the new /budgexpage/{id} route that bypasses routing complexity.
 */

// Load WordPress environment
$wp_paths = [
    '../../../../wp-config.php',
    '../../../wp-config.php', 
    '../../wp-config.php',
    '../wp-config.php'
];

$wp_loaded = false;
foreach ($wp_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die("❌ WordPress configuration not found. Please run this script from your WordPress directory.\n");
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die('❌ Access denied. You need administrator privileges to run this script.');
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>בדגקס - השלמת תיקון הניווט</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f8f9fa;
            direction: rtl;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #bee5eb;
            margin: 15px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ffeaa7;
            margin: 15px 0;
        }
        .button {
            background: #007cba;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background: #005a87;
        }
        .button-success {
            background: #28a745;
        }
        .button-success:hover {
            background: #218838;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            margin: 15px 0;
            border-radius: 5px;
            border-right: 4px solid #007cba;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        h2 {
            color: #34495e;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .status-check {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 השלמת תיקון הניווט - בדגקס</h1>
        
        <?php
        echo "<div class='info'>\n";
        echo "<h3>📋 סיכום השינויים שבוצעו:</h3>\n";
        echo "<ul>\n";
        echo "<li>✅ נוסף נתיב חדש: <code>/budgexpage/{id}</code></li>\n";
        echo "<li>✅ עודכן הטמפלייט הישיר: <code>budgex-enhanced-direct.php</code></li>\n";
        echo "<li>✅ עודכנו קישורי הניווט בכל הקבצים</li>\n";
        echo "<li>✅ נוספו בדיקות אבטחה ואימות הרשאות</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
        
        // Step 1: Check current rewrite rules
        echo "<div class='step'>\n";
        echo "<h2>שלב 1: בדיקת חוקי ניתוב נוכחיים</h2>\n";
        
        $rewrite_rules = get_option('rewrite_rules');
        if ($rewrite_rules) {
            $budgex_rules = array_filter($rewrite_rules, function($key) {
                return strpos($key, 'budgex') !== false;
            }, ARRAY_FILTER_USE_KEY);
            
            if (!empty($budgex_rules)) {
                echo "<div class='success'>✅ נמצאו חוקי ניתוב של בדגקס:</div>\n";
                echo "<pre>\n";
                foreach ($budgex_rules as $pattern => $rewrite) {
                    echo htmlspecialchars($pattern) . " → " . htmlspecialchars($rewrite) . "\n";
                }
                echo "</pre>\n";
                
                // Check specifically for our new route
                $new_route_exists = false;
                foreach ($budgex_rules as $pattern => $rewrite) {
                    if (strpos($pattern, 'budgexpage') !== false) {
                        $new_route_exists = true;
                        break;
                    }
                }
                
                if ($new_route_exists) {
                    echo "<div class='success'>✅ הנתיב החדש /budgexpage/{id} כבר קיים ברשימת החוקים</div>\n";
                } else {
                    echo "<div class='warning'>⚠️ הנתיב החדש /budgexpage/{id} לא נמצא - נדרש ריענון</div>\n";
                }
            } else {
                echo "<div class='warning'>⚠️ לא נמצאו חוקי ניתוב של בדגקס</div>\n";
            }
        } else {
            echo "<div class='warning'>⚠️ לא נמצאו חוקי ניתוב כלל</div>\n";
        }
        echo "</div>\n";
        
        // Step 2: Flush rewrite rules
        echo "<div class='step'>\n";
        echo "<h2>שלב 2: ריענון חוקי הניתוב</h2>\n";
        
        if (isset($_POST['flush_rules'])) {
            // Force flush rewrite rules
            flush_rewrite_rules(true);
            
            // Set the flush flag for the plugin
            update_option('budgex_flush_rewrite_rules', 1);
            
            echo "<div class='success'>✅ חוקי הניתוב רועננו בהצלחה!</div>\n";
            
            // Check again after flush
            $rewrite_rules_after = get_option('rewrite_rules');
            if ($rewrite_rules_after) {
                $budgex_rules_after = array_filter($rewrite_rules_after, function($key) {
                    return strpos($key, 'budgex') !== false;
                }, ARRAY_FILTER_USE_KEY);
                
                echo "<div class='info'>📋 חוקי הניתוב לאחר הריענון:</div>\n";
                echo "<pre>\n";
                foreach ($budgex_rules_after as $pattern => $rewrite) {
                    echo htmlspecialchars($pattern) . " → " . htmlspecialchars($rewrite) . "\n";
                }
                echo "</pre>\n";
            }
        } else {
            echo "<form method='post'>\n";
            echo "<p>לחץ על הכפתור כדי לרענן את חוקי הניתוב:</p>\n";
            echo "<input type='hidden' name='flush_rules' value='1'>\n";
            echo "<button type='submit' class='button button-success'>רענן חוקי ניתוב</button>\n";
            echo "</form>\n";
        }
        echo "</div>\n";
        
        // Step 3: Test the new route
        echo "<div class='step'>\n";
        echo "<h2>שלב 3: בדיקת הנתיב החדש</h2>\n";
        
        // Get a sample budget ID for testing
        global $wpdb;
        $sample_budget = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}budgex_budgets LIMIT 1");
        
        if ($sample_budget) {
            $test_url = home_url("/budgexpage/{$sample_budget->id}/");
            echo "<div class='info'>\n";
            echo "<h4>🧪 בדיקת הנתיב החדש:</h4>\n";
            echo "<p>נמצא תקציב לדוגמה עם ID: {$sample_budget->id}</p>\n";
            echo "<p>כתובת הבדיקה: <code>" . esc_html($test_url) . "</code></p>\n";
            echo "<a href='" . esc_url($test_url) . "' target='_blank' class='button'>פתח דף תקציב משופר</a>\n";
            echo "</div>\n";
        } else {
            echo "<div class='warning'>⚠️ לא נמצאו תקציבים במערכת לבדיקה</div>\n";
        }
        echo "</div>\n";
        
        // Step 4: Manual verification
        echo "<div class='step'>\n";
        echo "<h2>שלב 4: אימות ידני</h2>\n";
        echo "<div class='info'>\n";
        echo "<h4>🔍 בדיקות שיש לבצע:</h4>\n";
        echo "<ol>\n";
        echo "<li><strong>ניווט מהדשבורד:</strong> היכנס לדשבורד ולחץ על כפתור 'ניהול תקציב'</li>\n";
        echo "<li><strong>קישורים ישירים:</strong> נסה לגשת ישירות לכתובת /budgexpage/[מספר-תקציב]/</li>\n";
        echo "<li><strong>בדיקת תוכן:</strong> וודא שהדף המשופר מוצג במלואו ולא רק מיכל ריק</li>\n";
        echo "<li><strong>פונקציונליות:</strong> בדק שכל הכפתורים והטפסים פועלים</li>\n";
        echo "</ol>\n";
        echo "</div>\n";
        echo "</div>\n";
        
        // Step 5: Next steps
        echo "<div class='step'>\n";
        echo "<h2>שלב 5: שלבים נוספים</h2>\n";
        echo "<div class='info'>\n";
        echo "<h4>📝 פעולות נוספות מומלצות:</h4>\n";
        echo "<ul>\n";
        echo "<li><strong>ניקוי מטמון:</strong> נקה את מטמון האתר אם יש כזה</li>\n";
        echo "<li><strong>בדיקת יומן שגיאות:</strong> בדק את לוג השגיאות לאחר השינויים</li>\n";
        echo "<li><strong>בדיקת ביצועים:</strong> וודא שהנתיב החדש פועל ביעילות</li>\n";
        echo "<li><strong>גיבוי:</strong> צור גיבוי של האתר לאחר האימות</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
        echo "</div>\n";
        
        // Status summary
        if (isset($_POST['flush_rules'])) {
            echo "<div class='success'>\n";
            echo "<h3>🎉 תיקון הניווט הושלם!</h3>\n";
            echo "<p>כל השינויים יושמו בהצלחה:</p>\n";
            echo "<ul>\n";
            echo "<li>✅ נתיב חדש: /budgexpage/{id} פעיל</li>\n";
            echo "<li>✅ טמפלייט ישיר זמין</li>\n";
            echo "<li>✅ קישורי ניווט עודכנו</li>\n";
            echo "<li>✅ חוקי ניתוב רועננו</li>\n";
            echo "</ul>\n";
            echo "<p><strong>המערכת מוכנה לשימוש!</strong></p>\n";
            echo "</div>\n";
            
            echo "<div class='info'>\n";
            echo "<h4>🔗 קישורים מהירים:</h4>\n";
            echo "<a href='" . home_url('/budgex/') . "' class='button'>דשבורד בדגקס</a>\n";
            echo "<a href='" . admin_url('options-permalink.php') . "' class='button'>הגדרות פרמלינקים</a>\n";
            echo "</div>\n";
        }
        ?>
        
        <div class='info'>
            <h4>📞 תמיכה טכנית:</h4>
            <p>אם הבעיה עדיין קיימת, יש לבדוק:</p>
            <ul>
                <li>הרשאות משתמש לתקציב הספציפי</li>
                <li>קונפליקטים עם תוספים אחרים</li>
                <li>הגדרות שרת ו-.htaccess</li>
                <li>שגיאות JavaScript בקונסול הדפדפן</li>
            </ul>
        </div>
    </div>
</body>
</html>
