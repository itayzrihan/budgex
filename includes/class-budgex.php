<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Budgex {
    private $database;
    private $permissions;
    private $calculator;

    public function __construct() {
        $this->database = new Budgex_Database();
        $this->permissions = new Budgex_Permissions();
        $this->calculator = new Budgex_Budget_Calculator();
        
        add_action('init', array($this, 'init'));
        add_action('init', array($this, 'create_frontend_pages'));
        add_action('template_redirect', array($this, 'handle_budgex_frontend'));
        add_shortcode('budgex_app', array($this, 'display_budgex_app'));
        // Admin-only AJAX handlers should remain here if needed
        // Public AJAX handlers are now handled by Budgex_Public class
    }

    public function init() {
        // Add custom rewrite rules for budget pages
        add_rewrite_rule(
            '^budgex/?$',
            'index.php?budgex_page=dashboard',
            'top'
        );
        
        add_rewrite_rule(
            '^budgex/budget/([0-9]+)/?$',
            'index.php?budgex_page=budget&budget_id=$matches[1]',
            'top'
        );
        
        add_rewrite_rule(
            '^budgex/([^/]+)/?$',
            'index.php?budgex_page=$matches[1]',
            'top'
        );
        
        add_rewrite_tag('%budgex_page%', '([^&]+)');
        add_rewrite_tag('%budget_id%', '([0-9]+)');
        
        // Flush rewrite rules on activation
        if (get_option('budgex_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('budgex_flush_rewrite_rules');
        }
    }

    /**
     * Create frontend pages for Budgex
     */
    public function create_frontend_pages() {
        // Check if main Budgex page exists
        $budgex_page = get_page_by_path('budgex');
        
        if (!$budgex_page) {
            $page_data = array(
                'post_title'    => 'Budgex - ניהול תקציב',
                'post_content'  => '[budgex_app]',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_name'     => 'budgex',
                'post_author'   => 1
            );
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id) {
                // Set flag to flush rewrite rules
                update_option('budgex_flush_rewrite_rules', true);
            }
        }
    }

    /**
     * Handle frontend Budgex requests
     */
    public function handle_budgex_frontend() {
        $budgex_page = get_query_var('budgex_page');
        $budget_id = get_query_var('budget_id');
        
        // Debug logging (remove in production)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Budgex Frontend Handler - Page: $budgex_page, Budget ID: $budget_id, User logged in: " . (is_user_logged_in() ? 'Yes' : 'No'));
        }
        
        if ($budgex_page) {
            // Ensure user is logged in
            if (!is_user_logged_in()) {
                // Log the redirect for debugging
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("Budgex: Redirecting to login - Current URL: " . $_SERVER['REQUEST_URI']);
                }
                wp_redirect(wp_login_url(home_url('/budgex/')));
                exit;
            }
            
            // If this is a budget page, check user permissions
            if ($budgex_page === 'budget' && $budget_id) {
                $user_id = get_current_user_id();
                if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
                    // Log permission failure
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("Budgex: User $user_id denied access to budget $budget_id");
                    }
                    wp_redirect(home_url('/budgex/'));
                    exit;
                }
            }
            
            // Load the main budgex page template
            add_filter('template_include', array($this, 'load_budgex_template'));
        }
    }

    /**
     * Load custom template for Budgex pages
     */
    public function load_budgex_template($template) {
        $custom_template = BUDGEX_DIR . 'public/templates/budgex-app.php';
        
        if (file_exists($custom_template)) {
            return $custom_template;
        }
        
        return $template;
    }

    /**
     * Display Budgex app via shortcode
     */
    public function display_budgex_app($atts = array()) {
        if (!is_user_logged_in()) {
            return '<div class="budgex-login-required">' . 
                   '<p>' . __('נדרש להתחבר כדי לגשת למערכת ניהול התקציב', 'budgex') . '</p>' .
                   '<a href="' . wp_login_url(get_permalink()) . '" class="button">' . __('התחבר', 'budgex') . '</a>' .
                   '</div>';
        }
        
        // Load the public class to handle display
        $public = new Budgex_Public();
        
        $budgex_page = get_query_var('budgex_page', 'dashboard');
        $budget_id = get_query_var('budget_id');
        
        ob_start();
        
        switch ($budgex_page) {
            case 'budget':
                if ($budget_id) {
                    echo $public->display_single_budget_frontend($budget_id);
                } else {
                    echo $public->display_dashboard_frontend();
                }
                break;
            default:
                echo $public->display_dashboard_frontend();
                break;
        }
        
        return ob_get_clean();
    }

    public function create_budget($user_id, $budget_name, $monthly_budget, $currency, $start_date) {
        if (!$this->permissions->can_add_budget($user_id)) {
            return array('success' => false, 'message' => __('אין הרשאה ליצור תקציב', 'budgex'));
        }

        $budget_id = $this->database->add_budget($user_id, $budget_name, $monthly_budget, $currency, $start_date);
        
        if ($budget_id) {
            return array(
                'success' => true, 
                'message' => __('התקציב נוצר בהצלחה', 'budgex'),
                'budget_id' => $budget_id
            );
        }
        
        return array('success' => false, 'message' => __('שגיאה ביצירת התקציב', 'budgex'));
    }

    public function ajax_add_outcome() {
        check_ajax_referer('budgex_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['amount']);
        $description = sanitize_textarea_field($_POST['description']);
        $outcome_name = sanitize_text_field($_POST['outcome_name']);
        $outcome_date = sanitize_text_field($_POST['outcome_date']);
        
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_add_outcomes($budget_id, $user_id)) {
            wp_die(__('אין הרשאה להוסיף הוצאות לתקציב זה', 'budgex'));
        }
        
        $result = $this->database->add_outcome($budget_id, $amount, $description, $outcome_name, $outcome_date);
        
        if ($result) {
            wp_send_json_success(array('message' => __('ההוצאה נוספה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהוספת ההוצאה', 'budgex')));
        }
    }

    public function ajax_invite_user() {
        check_ajax_referer('budgex_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $user_email = sanitize_email($_POST['user_email']);
        $role = sanitize_text_field($_POST['role']);
        
        $inviter_id = get_current_user_id();
        
        if (!$this->permissions->can_invite_users($budget_id, $inviter_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה להזמין משתמשים לתקציב זה', 'budgex')));
        }
        
        $invited_user = get_user_by('email', $user_email);
        if (!$invited_user) {
            wp_send_json_error(array('message' => __('משתמש לא נמצא', 'budgex')));
        }
        
        $result = $this->database->create_invitation($budget_id, $inviter_id, $invited_user->ID, $role);
        
        if ($result) {
            // Send email notification
            $this->send_invitation_email($invited_user, $budget_id, $inviter_id, $role);
            wp_send_json_success(array('message' => __('ההזמנה נשלחה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בשליחת ההזמנה', 'budgex')));
        }
    }

    public function ajax_add_additional_budget() {
        check_ajax_referer('budgex_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['amount']);
        
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_edit_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך תקציב זה', 'budgex')));
        }
        
        $result = $this->database->add_additional_budget($budget_id, $amount);
        
        if ($result) {
            wp_send_json_success(array('message' => __('התקציב הנוסף נוסף בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהוספת התקציב הנוסף', 'budgex')));
        }
    }

    public function ajax_delete_outcome() {
        check_ajax_referer('budgex_nonce', 'nonce');
        
        $outcome_id = intval($_POST['outcome_id']);
        $user_id = get_current_user_id();
        
        // Get the outcome to check budget permissions
        global $wpdb;
        $outcomes_table = $wpdb->prefix . 'budgex_outcomes';
        $outcome = $wpdb->get_row(
            $wpdb->prepare("SELECT budget_id FROM {$outcomes_table} WHERE id = %d", $outcome_id)
        );
        
        if (!$outcome || !$this->permissions->can_edit_budget($outcome->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה למחוק הוצאה זו', 'budgex')));
        }
        
        $result = $this->database->delete_outcome($outcome_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('ההוצאה נמחקה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה במחיקת ההוצאה', 'budgex')));
        }
    }

    private function send_invitation_email($invited_user, $budget_id, $inviter_id, $role) {
        $inviter = get_userdata($inviter_id);
        $budget = $this->database->get_budget($budget_id);
        
        $subject = __('הוזמנת לצפות בתקציב ב-Budgex', 'budgex');
        $message = sprintf(
            __('שלום %s,\n\n%s הזמין אותך לצפות בתקציב "%s" כ%s.\n\nלחץ כאן לקבלת ההזמנה: %s\n\nתודה,\nצוות Budgex', 'budgex'),
            $invited_user->display_name,
            $inviter->display_name,
            $budget->budget_name,
            $role === 'admin' ? 'מנהל' : 'צופה',
            site_url('/budgex/invitation/' . $budget_id)
        );
        
        wp_mail($invited_user->user_email, $subject, $message);
    }

    public function get_budget_summary($budget_id, $user_id) {
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            return false;
        }
        
        $budget = $this->database->get_budget($budget_id);
        if (!$budget) {
            return false;
        }
        
        $budget_calculation = $this->calculator->calculate_remaining_budget($budget_id);
        $outcomes = $this->database->get_budget_outcomes($budget_id);
        $monthly_breakdown = $this->calculator->get_monthly_breakdown($budget_id);
        
        return array(
            'budget' => $budget,
            'calculation' => $budget_calculation,
            'outcomes' => $outcomes,
            'monthly_breakdown' => $monthly_breakdown,
            'user_role' => $this->permissions->get_user_role_on_budget($budget_id, $user_id)
        );
    }
}
?>