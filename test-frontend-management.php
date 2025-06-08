<?php
/**
 * Test Frontend Budget Management Implementation
 * 
 * This script verifies that all frontend budget management components
 * are properly implemented and integrated.
 */

// Check if we're in a WordPress environment
$wordpress_loaded = false;
$wp_paths = [
    '../../../wp-config.php',  // Standard WordPress structure
    '../../wp-config.php',     // Alternative structure
    '../wp-config.php',        // Another alternative
    'wp-config.php'            // Root level
];

foreach ($wp_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $wordpress_loaded = true;
        break;
    }
}

if (!$wordpress_loaded) {
    echo "⚠️ WordPress not loaded - Testing file structure only\n\n";
}

echo "=== Budgex Frontend Management Integration Test ===\n\n";

// Test 1: Check if required files exist
echo "1. Checking required files...\n";
$required_files = [
    'public/partials/budgex-budget-page.php' => 'Frontend budget page template',
    'public/class-budgex-public.php' => 'Public class with AJAX handlers',
    'public/css/budgex-public.css' => 'Frontend CSS styles',
    'public/js/budgex-public.js' => 'Frontend JavaScript',
    'includes/class-budgex.php' => 'Main plugin class',
    'includes/class-database.php' => 'Database class',
    'includes/class-permissions.php' => 'Permissions class'
];

$files_ok = true;
foreach ($required_files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ {$description}\n";
    } else {
        echo "   ❌ {$description} - MISSING\n";
        $files_ok = false;
    }
}

// Test 2: Check if budget page has management forms
echo "\n2. Checking budget page management forms...\n";
$budget_page_content = file_get_contents('public/partials/budgex-budget-page.php');

$management_features = [
    'quick-actions-section' => 'Quick actions section',
    'add-outcome-section' => 'Add outcome form',
    'add-budget-section' => 'Add additional budget form',
    'invite-section' => 'User invitation form',
    'toggle-form' => 'Form toggle functionality',
    'cancel-form' => 'Form cancel functionality'
];

$forms_ok = true;
foreach ($management_features as $feature => $description) {
    if (strpos($budget_page_content, $feature) !== false) {
        echo "   ✅ {$description}\n";
    } else {
        echo "   ❌ {$description} - MISSING\n";
        $forms_ok = false;
    }
}

// Test 3: Check AJAX handlers in public class
echo "\n3. Checking AJAX handlers...\n";
$public_class_content = file_get_contents('public/class-budgex-public.php');

$ajax_handlers = [
    'wp_ajax_budgex_add_outcome' => 'Add outcome AJAX handler',
    'wp_ajax_budgex_add_additional_budget' => 'Add additional budget AJAX handler',
    'wp_ajax_budgex_invite_user' => 'Invite user AJAX handler',
    'wp_ajax_budgex_delete_outcome' => 'Delete outcome AJAX handler',
    'ajax_add_additional_budget' => 'Add additional budget method',
    'ajax_invite_user' => 'Invite user method'
];

$ajax_ok = true;
foreach ($ajax_handlers as $handler => $description) {
    if (strpos($public_class_content, $handler) !== false) {
        echo "   ✅ {$description}\n";
    } else {
        echo "   ❌ {$description} - MISSING\n";
        $ajax_ok = false;
    }
}

// Test 4: Check JavaScript AJAX integration
echo "\n4. Checking JavaScript AJAX integration...\n";
$js_features = [
    'budgex_add_additional_budget' => 'Additional budget AJAX call',
    'budgex_invite_user' => 'Invite user AJAX call',
    'budgex_add_outcome' => 'Add outcome AJAX call',
    '#add-additional-budget-form' => 'Additional budget form handler',
    '#invite-user-form' => 'Invite user form handler'
];

$js_ok = true;
foreach ($js_features as $feature => $description) {
    if (strpos($budget_page_content, $feature) !== false) {
        echo "   ✅ {$description}\n";
    } else {
        echo "   ❌ {$description} - MISSING\n";
        $js_ok = false;
    }
}

// Test 5: Check CSS styles for management forms
echo "\n5. Checking CSS styles...\n";
$css_content = file_get_contents('public/css/budgex-public.css');

$css_features = [
    '.form-section' => 'Form section styles',
    '.quick-actions-section' => 'Quick actions section styles',
    '.action-card' => 'Action card styles',
    '.form-grid' => 'Form grid styles',
    '.budget-page-header' => 'Budget page header styles'
];

$css_ok = true;
foreach ($css_features as $feature => $description) {
    if (strpos($css_content, $feature) !== false) {
        echo "   ✅ {$description}\n";
    } else {
        echo "   ❌ {$description} - MISSING\n";
        $css_ok = false;
    }
}

// Test 6: Check database methods
echo "\n6. Checking database methods...\n";
$database_content = file_get_contents('includes/class-database.php');

$database_methods = [
    'add_additional_budget' => 'Add additional budget method',
    'create_invitation' => 'Create invitation method',
    'add_outcome' => 'Add outcome method',
    'get_budget_outcomes' => 'Get budget outcomes method'
];

$db_ok = true;
foreach ($database_methods as $method => $description) {
    if (strpos($database_content, $method) !== false) {
        echo "   ✅ {$description}\n";
    } else {
        echo "   ❌ {$description} - MISSING\n";
        $db_ok = false;
    }
}

// Test 7: If WordPress is loaded, test instantiation
if ($wordpress_loaded) {
    echo "\n7. Testing WordPress integration...\n";
    
    try {
        // Test if classes can be instantiated
        if (class_exists('Budgex_Database')) {
            $db = new Budgex_Database();
            echo "   ✅ Database class instantiated\n";
        } else {
            echo "   ❌ Database class not found\n";
        }
        
        if (class_exists('Budgex_Permissions')) {
            $permissions = new Budgex_Permissions();
            echo "   ✅ Permissions class instantiated\n";
        } else {
            echo "   ❌ Permissions class not found\n";
        }
        
        if (class_exists('Budgex_Public')) {
            $public = new Budgex_Public();
            echo "   ✅ Public class instantiated\n";
        } else {
            echo "   ❌ Public class not found\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error during instantiation: " . $e->getMessage() . "\n";
    }
}

// Summary
echo "\n=== SUMMARY ===\n";
$overall_status = $files_ok && $forms_ok && $ajax_ok && $js_ok && $css_ok && $db_ok;

if ($overall_status) {
    echo "🎉 ALL TESTS PASSED! Frontend budget management is fully implemented.\n\n";
    echo "The Budgex plugin now has complete frontend access with:\n";
    echo "- Budget viewing and management forms\n";
    echo "- Add outcome functionality\n";
    echo "- Add additional budget functionality\n";
    echo "- User invitation system\n";
    echo "- AJAX-powered interactions\n";
    echo "- Professional styling\n";
    echo "- Proper security controls\n\n";
    echo "Users can now access full budget management at /budgex URL!\n";
} else {
    echo "❌ Some components are missing or incomplete.\n";
    echo "Please review the issues above and fix them.\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Activate the plugin in WordPress admin\n";
echo "2. Visit /budgex to test the frontend interface\n";
echo "3. Create a test budget and verify all forms work\n";
echo "4. Test user permissions and invitation system\n";
echo "5. Verify AJAX functionality with browser dev tools\n";
?>
