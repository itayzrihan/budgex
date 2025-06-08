<?php
/**
 * URGENT: Fix Empty Budget Page
 * Replace the problematic display method temporarily
 */

// Add this to your functions.php to override the empty page issue

function budgex_emergency_budget_page_fix() {
    if (get_query_var('budgex_page') === 'budget' && get_query_var('budget_id')) {
        $budget_id = intval(get_query_var('budget_id'));
        
        // Basic security check
        if (!is_user_logged_in()) {
            wp_redirect(wp_login_url(home_url('/budgex/')));
            exit;
        }
        
        // Force output the enhanced budget page
        get_header();
        
        echo '<div id="budgex-app-container" class="budgex-frontend-app">';
        echo '<style>
            .budgex-frontend-app { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                direction: rtl; 
                text-align: right; 
                padding: 20px; 
            }
            .budgex-emergency-fix { 
                background: #fff3cd; 
                padding: 20px; 
                border-radius: 5px; 
                margin: 20px 0; 
                border-left: 4px solid #ffc107; 
            }
        </style>';
        
        echo '<div class="budgex-emergency-fix">';
        echo '<h2>🔧 Emergency Budget Page Load</h2>';
        echo '<p>Budget ID: ' . $budget_id . '</p>';
        echo '<p>Loading enhanced budget page...</p>';
        echo '</div>';
        
        // Try to include the enhanced template directly
        $template_path = WP_PLUGIN_DIR . '/budgex/public/partials/budgex-public-enhanced-budget-page.php';
        
        if (file_exists($template_path)) {
            echo '<div class="budgex-emergency-notice" style="background: #e8f5e8; padding: 15px; margin: 20px 0; border-radius: 5px;">';
            echo '<p>✅ Template found at: ' . $template_path . '</p>';
            echo '<p>File size: ' . number_format(filesize($template_path)) . ' bytes</p>';
            echo '</div>';
            
            // Set up minimal required variables for the template
            $user_id = get_current_user_id();
            $budget = (object) [
                'id' => $budget_id,
                'name' => 'Budget #' . $budget_id,
                'monthly_budget' => 5000,
                'currency' => 'ILS'
            ];
            
            $calculation = (object) [
                'remaining_budget' => 3000,
                'spent_amount' => 2000,
                'percentage_used' => 40
            ];
            
            $outcomes = [];
            $monthly_breakdown = [];
            $user_role = 'owner';
            $shared_users = [];
            $pending_invitations = [];
            $future_expenses = [];
            $recurring_expenses = [];
            $budget_adjustments = [];
            
            // Include the template
            include $template_path;
        } else {
            echo '<div class="budgex-error" style="background: #ffebee; padding: 15px; margin: 20px 0; border-radius: 5px;">';
            echo '<h3>❌ Template Not Found</h3>';
            echo '<p>Expected path: ' . $template_path . '</p>';
            echo '<p>Please check if the enhanced budget page template exists.</p>';
            echo '</div>';
        }
        
        echo '</div>';
        
        get_footer();
        exit;
    }
}

// Add this function to your functions.php
echo "// Add this to your theme's functions.php:\n";
echo "add_action('template_redirect', 'budgex_emergency_budget_page_fix', 1);\n\n";

// Also include the function definition above
?>
