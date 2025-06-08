<?php
/**
 * Budgex Rewrite Rules Fixer
 * 
 * Run this script to force flush WordPress rewrite rules
 * This should fix the login redirect issue
 */

// Set flag to flush rewrite rules on next init
add_option('budgex_flush_rewrite_rules', 1);

echo "Rewrite rules flush has been scheduled.\n";
echo "The rules will be flushed on the next page load.\n\n";

echo "After running this script:\n";
echo "1. Visit your WordPress site\n";
echo "2. Go to Settings → Permalinks in WordPress admin\n";
echo "3. Click 'Save Changes' (this will flush rewrite rules)\n";
echo "4. Test the navigation again\n\n";

echo "If the issue persists, the problem might be:\n";
echo "- User permissions on the specific budget\n";
echo "- Plugin not properly activated\n";
echo "- Theme conflicts\n";
echo "- Server configuration issues\n";
?>
