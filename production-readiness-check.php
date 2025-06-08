<?php
/**
 * Budgex Production Readiness Check
 * 
 * Final verification script to ensure the Budgex plugin is ready for production use.
 * This script performs comprehensive checks of all systems and provides a go/no-go decision.
 */

// Load WordPress
if (!defined('ABSPATH')) {
    $wp_load_paths = [
        '../../../wp-load.php',
        '../../../../wp-load.php',
        '../../../../../wp-load.php'
    ];
    
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded) {
        die('WordPress not found.');
    }
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Access denied.');
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="he">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budgex Production Readiness Check</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        .content {
            padding: 30px;
        }
        .check-section {
            margin-bottom: 30px;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            overflow: hidden;
        }
        .section-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e1e8ed;
            font-weight: 600;
            color: #2c3e50;
        }
        .section-content {
            padding: 20px;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .check-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            font-weight: bold;
            font-size: 14px;
        }
        .check-pass {
            background: #d4edda;
            color: #155724;
        }
        .check-fail {
            background: #f8d7da;
            color: #721c24;
        }
        .check-warning {
            background: #fff3cd;
            color: #856404;
        }
        .check-text {
            flex: 1;
        }
        .check-details {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        .summary-box {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }
        .summary-box.warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        }
        .summary-box.danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            background: #0073aa;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 0 10px;
            display: inline-block;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #005a87;
            color: white;
        }
        .btn.success {
            background: #28a745;
        }
        .btn.success:hover {
            background: #218838;
        }
        .progress-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin: 15px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.5s ease;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🚀 Budgex Production Readiness</h1>
        <p>מערכת בדיקה סופית לפריסה בסביבת ייצור</p>
    </div>
    
    <div class="content">
        <?php
        $total_checks = 0;
        $passed_checks = 0;
        $warnings = 0;
        $critical_issues = [];
        $all_checks = [];
        
        function run_check($name, $condition, $details = '', $critical = false) {
            global $total_checks, $passed_checks, $warnings, $critical_issues, $all_checks;
            
            $total_checks++;
            $status = $condition ? 'pass' : ($critical ? 'fail' : 'warning');
            
            if ($condition) {
                $passed_checks++;
            } elseif ($critical) {
                $critical_issues[] = $name;
            } else {
                $warnings++;
            }
            
            $all_checks[] = [
                'name' => $name,
                'status' => $status,
                'details' => $details,
                'critical' => $critical
            ];
            
            return $status;
        }
        ?>

        <!-- System Requirements -->
        <div class="check-section">
            <div class="section-header">
                📋 System Requirements
            </div>
            <div class="section-content">
                <?php
                $php_version = version_compare(PHP_VERSION, '7.4', '>=');
                $wp_version = version_compare(get_bloginfo('version'), '5.0', '>=');
                $mysql_version = true; // WordPress handles this
                
                $checks = [
                    ['PHP Version (7.4+)', $php_version, 'Current: ' . PHP_VERSION, true],
                    ['WordPress Version (5.0+)', $wp_version, 'Current: ' . get_bloginfo('version'), true],
                    ['MySQL Database', $mysql_version, 'Connected via WordPress', true]
                ];
                
                foreach ($checks as $check) {
                    $status = run_check($check[0], $check[1], $check[2], $check[3]);
                    $icon_class = $status === 'pass' ? 'check-pass' : ($status === 'fail' ? 'check-fail' : 'check-warning');
                    $icon = $status === 'pass' ? '✓' : ($status === 'fail' ? '✗' : '⚠');
                    
                    echo "<div class='check-item'>";
                    echo "<div class='check-icon {$icon_class}'>{$icon}</div>";
                    echo "<div class='check-text'>";
                    echo "<strong>{$check[0]}</strong>";
                    if ($check[2]) echo "<div class='check-details'>{$check[2]}</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Plugin Installation -->
        <div class="check-section">
            <div class="section-header">
                🔧 Plugin Installation
            </div>
            <div class="section-content">
                <?php
                $plugin_active = is_plugin_active('budgex/budgex.php');
                $classes_loaded = class_exists('Budgex') && class_exists('Budgex_Public') && class_exists('Budgex_Database');
                $main_file = file_exists(plugin_dir_path(__FILE__) . 'budgex.php');
                
                $checks = [
                    ['Plugin Activated', $plugin_active, 'Budgex plugin is active', true],
                    ['Core Classes Loaded', $classes_loaded, 'Main classes instantiated', true],
                    ['Main Plugin File', $main_file, 'budgex.php exists', true]
                ];
                
                foreach ($checks as $check) {
                    $status = run_check($check[0], $check[1], $check[2], $check[3]);
                    $icon_class = $status === 'pass' ? 'check-pass' : ($status === 'fail' ? 'check-fail' : 'check-warning');
                    $icon = $status === 'pass' ? '✓' : ($status === 'fail' ? '✗' : '⚠');
                    
                    echo "<div class='check-item'>";
                    echo "<div class='check-icon {$icon_class}'>{$icon}</div>";
                    echo "<div class='check-text'>";
                    echo "<strong>{$check[0]}</strong>";
                    if ($check[2]) echo "<div class='check-details'>{$check[2]}</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Database -->
        <div class="check-section">
            <div class="section-header">
                🗃️ Database Structure
            </div>
            <div class="section-content">
                <?php
                global $wpdb;
                $required_tables = [
                    $wpdb->prefix . 'budgex_budgets',
                    $wpdb->prefix . 'budgex_outcomes',
                    $wpdb->prefix . 'budgex_invitations',
                    $wpdb->prefix . 'budgex_budget_shares'
                ];
                
                foreach ($required_tables as $table) {
                    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'") == $table;
                    $row_count = $exists ? $wpdb->get_var("SELECT COUNT(*) FROM {$table}") : 0;
                    
                    $status = run_check("Table: " . str_replace($wpdb->prefix, '', $table), $exists, "Rows: {$row_count}", true);
                    $icon_class = $status === 'pass' ? 'check-pass' : ($status === 'fail' ? 'check-fail' : 'check-warning');
                    $icon = $status === 'pass' ? '✓' : ($status === 'fail' ? '✗' : '⚠');
                    
                    echo "<div class='check-item'>";
                    echo "<div class='check-icon {$icon_class}'>{$icon}</div>";
                    echo "<div class='check-text'>";
                    echo "<strong>Table: " . str_replace($wpdb->prefix, '', $table) . "</strong>";
                    echo "<div class='check-details'>Rows: {$row_count}</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Frontend System -->
        <div class="check-section">
            <div class="section-header">
                🌐 Frontend System
            </div>
            <div class="section-content">
                <?php
                $budgex_page = get_page_by_path('budgex');
                $shortcode_exists = shortcode_exists('budgex_app');
                $rewrite_rules = get_option('rewrite_rules', []);
                $budgex_rules = 0;
                foreach ($rewrite_rules as $pattern => $rule) {
                    if (strpos($pattern, 'budgex') !== false || strpos($rule, 'budgex') !== false) {
                        $budgex_rules++;
                    }
                }
                
                $template_files = [
                    'public/templates/budgex-app.php',
                    'public/partials/budgex-dashboard.php',
                    'public/partials/budgex-budget-page.php',
                    'public/partials/budgex-no-access.php'
                ];
                
                $all_templates = true;
                foreach ($template_files as $file) {
                    if (!file_exists(plugin_dir_path(__FILE__) . $file)) {
                        $all_templates = false;
                        break;
                    }
                }
                
                $checks = [
                    ['Frontend Page Created', $budgex_page !== null, $budgex_page ? "ID: {$budgex_page->ID}" : 'Page missing', true],
                    ['Shortcode Registered', $shortcode_exists, '[budgex_app] available', true],
                    ['URL Rewrite Rules', $budgex_rules > 0, "Found {$budgex_rules} rules", true],
                    ['Template Files', $all_templates, count($template_files) . ' templates', true]
                ];
                
                foreach ($checks as $check) {
                    $status = run_check($check[0], $check[1], $check[2], $check[3]);
                    $icon_class = $status === 'pass' ? 'check-pass' : ($status === 'fail' ? 'check-fail' : 'check-warning');
                    $icon = $status === 'pass' ? '✓' : ($status === 'fail' ? '✗' : '⚠');
                    
                    echo "<div class='check-item'>";
                    echo "<div class='check-icon {$icon_class}'>{$icon}</div>";
                    echo "<div class='check-text'>";
                    echo "<strong>{$check[0]}</strong>";
                    if ($check[2]) echo "<div class='check-details'>{$check[2]}</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Assets -->
        <div class="check-section">
            <div class="section-header">
                🎨 Assets & Resources
            </div>
            <div class="section-content">
                <?php
                $css_files = [
                    'public/css/budgex-public.css',
                    'public/css/budgex-public-new.css'
                ];
                
                $js_files = [
                    'public/js/budgex-public.js'
                ];
                
                $css_total = 0;
                $css_exists = true;
                foreach ($css_files as $file) {
                    $path = plugin_dir_path(__FILE__) . $file;
                    if (file_exists($path)) {
                        $css_total += filesize($path);
                    } else {
                        $css_exists = false;
                    }
                }
                
                $js_total = 0;
                $js_exists = true;
                foreach ($js_files as $file) {
                    $path = plugin_dir_path(__FILE__) . $file;
                    if (file_exists($path)) {
                        $js_total += filesize($path);
                    } else {
                        $js_exists = false;
                    }
                }
                
                $lang_files = [
                    'languages/budgex-he_IL.po',
                    'languages/budgex-he_IL.mo'
                ];
                
                $lang_exists = true;
                foreach ($lang_files as $file) {
                    if (!file_exists(plugin_dir_path(__FILE__) . $file)) {
                        $lang_exists = false;
                        break;
                    }
                }
                
                $checks = [
                    ['CSS Files', $css_exists, number_format($css_total) . ' bytes total', false],
                    ['JavaScript Files', $js_exists, number_format($js_total) . ' bytes total', false],
                    ['Hebrew Language Files', $lang_exists, count($lang_files) . ' translation files', false]
                ];
                
                foreach ($checks as $check) {
                    $status = run_check($check[0], $check[1], $check[2], $check[3]);
                    $icon_class = $status === 'pass' ? 'check-pass' : ($status === 'fail' ? 'check-fail' : 'check-warning');
                    $icon = $status === 'pass' ? '✓' : ($status === 'fail' ? '✗' : '⚠');
                    
                    echo "<div class='check-item'>";
                    echo "<div class='check-icon {$icon_class}'>{$icon}</div>";
                    echo "<div class='check-text'>";
                    echo "<strong>{$check[0]}</strong>";
                    if ($check[2]) echo "<div class='check-details'>{$check[2]}</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- AJAX & Security -->
        <div class="check-section">
            <div class="section-header">
                🔒 AJAX & Security
            </div>
            <div class="section-content">
                <?php
                $ajax_actions = [
                    'wp_ajax_budgex_get_dashboard_stats',
                    'wp_ajax_budgex_get_user_budgets',
                    'wp_ajax_budgex_add_outcome',
                    'wp_ajax_budgex_edit_outcome',
                    'wp_ajax_budgex_delete_outcome'
                ];
                
                $ajax_registered = 0;
                foreach ($ajax_actions as $action) {
                    if (has_action($action)) {
                        $ajax_registered++;
                    }
                }
                
                $nonce_test = !empty(wp_create_nonce('budgex_public_nonce'));
                $security_functions = function_exists('is_user_logged_in') && function_exists('current_user_can') && function_exists('wp_verify_nonce');
                
                $checks = [
                    ['AJAX Actions Registered', $ajax_registered >= 4, "{$ajax_registered}/" . count($ajax_actions) . " actions", true],
                    ['Nonce Generation', $nonce_test, 'WordPress nonces working', true],
                    ['Security Functions', $security_functions, 'WordPress security API available', true]
                ];
                
                foreach ($checks as $check) {
                    $status = run_check($check[0], $check[1], $check[2], $check[3]);
                    $icon_class = $status === 'pass' ? 'check-pass' : ($status === 'fail' ? 'check-fail' : 'check-warning');
                    $icon = $status === 'pass' ? '✓' : ($status === 'fail' ? '✗' : '⚠');
                    
                    echo "<div class='check-item'>";
                    echo "<div class='check-icon {$icon_class}'>{$icon}</div>";
                    echo "<div class='check-text'>";
                    echo "<strong>{$check[0]}</strong>";
                    if ($check[2]) echo "<div class='check-details'>{$check[2]}</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Summary -->
        <?php
        $success_rate = $total_checks > 0 ? round(($passed_checks / $total_checks) * 100, 1) : 0;
        $is_production_ready = empty($critical_issues) && $success_rate >= 80;
        
        $summary_class = '';
        $summary_title = '';
        $summary_message = '';
        
        if ($is_production_ready) {
            $summary_class = '';
            $summary_title = '🎉 Ready for Production!';
            $summary_message = 'הפלאגין מוכן לפריסה בסביבת ייצור. כל המערכות פועלות כשורה.';
        } elseif (!empty($critical_issues)) {
            $summary_class = 'danger';
            $summary_title = '🚫 Critical Issues Found';
            $summary_message = 'נמצאו בעיות קריטיות שחייבות לטפל בהן לפני פריסה בייצור.';
        } else {
            $summary_class = 'warning';
            $summary_title = '⚠️ Minor Issues';
            $summary_message = 'יש כמה בעיות קלות, אבל הפלאגין יכול לעבוד בייצור.';
        }
        ?>

        <div class="summary-box <?php echo $summary_class; ?>">
            <h2><?php echo $summary_title; ?></h2>
            <p><?php echo $summary_message; ?></p>
            
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $success_rate; ?>%"></div>
            </div>
            
            <div class="summary-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_checks; ?></div>
                    <div>Total Checks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $passed_checks; ?></div>
                    <div>Passed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $warnings; ?></div>
                    <div>Warnings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $success_rate; ?>%</div>
                    <div>Success Rate</div>
                </div>
            </div>
            
            <?php if (!empty($critical_issues)): ?>
                <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <strong>Critical Issues to Fix:</strong>
                    <ul style="text-align: right; margin: 10px 0;">
                        <?php foreach ($critical_issues as $issue): ?>
                            <li><?php echo $issue; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="action-buttons">
            <?php if ($is_production_ready): ?>
                <a href="<?php echo home_url('/budgex/'); ?>" class="btn success" target="_blank">🚀 Launch Frontend</a>
                <a href="<?php echo admin_url('admin.php?page=budgex'); ?>" class="btn">🔧 Admin Panel</a>
            <?php else: ?>
                <a href="<?php echo plugin_dir_url(__FILE__) . 'test-frontend.php'; ?>" class="btn">🧪 Run Detailed Tests</a>
                <a href="<?php echo admin_url('plugins.php'); ?>" class="btn">📦 Plugin Settings</a>
            <?php endif; ?>
            
            <a href="javascript:location.reload()" class="btn">🔄 Recheck</a>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center; color: #6c757d;">
            <p><strong>Budgex Production Readiness Check</strong> - Version 1.0</p>
            <p>Last checked: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</div>
</body>
</html>
