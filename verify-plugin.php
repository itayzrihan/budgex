<?php
/**
 * Plugin Verification Script
 * Verifies basic plugin structure and dependencies
 */

// Basic file existence checks
$required_files = [
    'budgex.php',
    'includes/class-budgex.php',
    'includes/class-database.php',
    'public/class-budgex-public.php',
    'admin/class-budgex-admin.php',
    'public/css/budgex-public.css',
    'public/js/budgex-public.js',
    'languages/budgex-he_IL.po'
];

echo "=== Budgex Plugin Structure Verification ===\n\n";

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ {$file} - EXISTS\n";
    } else {
        echo "✗ {$file} - MISSING\n";
    }
}

// Check PHP syntax of main files
echo "\n=== PHP Syntax Check ===\n";
$php_files = [
    'budgex.php',
    'includes/class-budgex.php',
    'includes/class-database.php',
    'public/class-budgex-public.php',
    'admin/class-budgex-admin.php'
];

foreach ($php_files as $file) {
    if (file_exists($file)) {
        $output = [];
        $return_var = 0;
        exec("php -l \"{$file}\"", $output, $return_var);
        if ($return_var === 0) {
            echo "✓ {$file} - SYNTAX OK\n";
        } else {
            echo "✗ {$file} - SYNTAX ERROR: " . implode("\n", $output) . "\n";
        }
    }
}

// Check plugin header
echo "\n=== Plugin Header Check ===\n";
if (file_exists('budgex.php')) {
    $plugin_content = file_get_contents('budgex.php');
    if (strpos($plugin_content, 'Plugin Name:') !== false) {
        echo "✓ Plugin header found\n";
        
        // Extract plugin info
        preg_match('/Plugin Name:\s*(.+)/i', $plugin_content, $matches);
        if (isset($matches[1])) {
            echo "  Plugin Name: " . trim($matches[1]) . "\n";
        }
        
        preg_match('/Version:\s*(.+)/i', $plugin_content, $matches);
        if (isset($matches[1])) {
            echo "  Version: " . trim($matches[1]) . "\n";
        }
    } else {
        echo "✗ No plugin header found\n";
    }
}

// Check for AJAX handlers
echo "\n=== AJAX Handlers Check ===\n";
if (file_exists('public/class-budgex-public.php')) {
    $public_content = file_get_contents('public/class-budgex-public.php');    $ajax_actions = [
        'add_outcome',
        'edit_outcome',
        'delete_outcome',
        'get_dashboard_stats',
        'send_invitation',
        'accept_invitation',
        'remove_permission',
        'cancel_invitation',
        'resend_invitation'
    ];
    
    foreach ($ajax_actions as $action) {
        if (strpos($public_content, "ajax_{$action}") !== false) {
            echo "✓ AJAX handler for {$action} found\n";
        } else {
            echo "✗ AJAX handler for {$action} missing\n";
        }
    }
}

echo "\n=== Verification Complete ===\n";
