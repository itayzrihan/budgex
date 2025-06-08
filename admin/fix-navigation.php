<?php
/**
 * Navigation Fix Tool for Budgex Plugin
 * 
 * This file provides a simple interface to fix navigation issues
 * by flushing rewrite rules and testing navigation functionality.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if user has admin privileges
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

// Handle form submissions
if (isset($_POST['action'])) {
    $action = sanitize_text_field($_POST['action']);
    
    switch ($action) {
        case 'flush_rewrite_rules':
            flush_rewrite_rules();
            $message = 'Rewrite rules flushed successfully!';
            break;
        
        case 'test_navigation':
            // Test navigation by checking URLs and database
            $database = new Budgex_Database();
            $current_user = wp_get_current_user();
            $budgets = $database->get_user_budgets($current_user->ID);
            
            $test_results = array();
            
            // Test 1: Check if main page exists
            $main_page = get_page_by_path('budgex');
            $test_results['main_page'] = $main_page ? 'PASS' : 'FAIL';
            
            // Test 2: Check if we have budgets to test with
            $test_results['budgets_exist'] = !empty($budgets) ? 'PASS' : 'FAIL';
            
            // Test 3: Check URL structure
            if (!empty($budgets)) {
                $test_budget = $budgets[0];
                $test_url = home_url('/budgex/budget/' . $test_budget->id . '/');
                $test_results['url_structure'] = $test_url;
            }
            
            break;
    }
}
?>

<div class="wrap">
    <h1>Budgex Navigation Fix Tool</h1>
    
    <?php if (isset($message)): ?>
        <div class="notice notice-success">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h2>Fix Navigation Issues</h2>
        <p>Use this tool to fix navigation problems in the Budgex plugin.</p>
        
        <form method="post">
            <input type="hidden" name="action" value="flush_rewrite_rules">
            <button type="submit" class="button button-primary">
                Flush Rewrite Rules
            </button>
            <p class="description">This will refresh WordPress URL routing rules.</p>
        </form>
        
        <hr>
        
        <form method="post">
            <input type="hidden" name="action" value="test_navigation">
            <button type="submit" class="button button-secondary">
                Test Navigation
            </button>
            <p class="description">Run diagnostic tests on navigation system.</p>
        </form>
        
        <?php if (isset($test_results)): ?>
            <div class="notice notice-info">
                <h3>Test Results:</h3>
                <ul>
                    <li>Main Page Exists: <strong><?php echo $test_results['main_page']; ?></strong></li>
                    <li>User Has Budgets: <strong><?php echo $test_results['budgets_exist']; ?></strong></li>
                    <?php if (isset($test_results['url_structure'])): ?>
                        <li>Test URL: <strong><?php echo esc_html($test_results['url_structure']); ?></strong></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Navigation Debug Info</h2>
        <h3>Current Configuration:</h3>
        <ul>
            <li><strong>Plugin Version:</strong> <?php echo BUDGEX_VERSION; ?></li>
            <li><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></li>
            <li><strong>Home URL:</strong> <?php echo home_url(); ?></li>
            <li><strong>Main Page URL:</strong> <?php echo home_url('/budgex/'); ?></li>
        </ul>
        
        <h3>Recent Changes Applied:</h3>
        <ul>
            <li>✅ Fixed budget ID data attribute (budget->name → budget->budget_name)</li>
            <li>✅ Added comprehensive JavaScript debugging</li>
            <li>✅ Updated dashboard URL configuration</li>
            <li>✅ Added advanced management panel functionality</li>
        </ul>
        
        <h3>Next Steps:</h3>
        <ol>
            <li>Click "Flush Rewrite Rules" above</li>
            <li>Go to the Budgex dashboard</li>
            <li>Try clicking "ניהול מתקדם" button</li>
            <li>Check browser console for debug messages</li>
        </ol>
    </div>
</div>

<style>
.card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.card h2 {
    margin-top: 0;
}

.notice ul {
    margin: 10px 0;
}

.notice li {
    margin: 5px 0;
}
</style>
