<?php
/**
 * The public-facing functionality of the plugin.
 *
 * This class is responsible for handling user interactions and displaying budget information.
 *
 * @package Budgex
 * @subpackage Budgex/public
 */

class Budgex_Public {
    private $database;
    private $permissions;

    public function __construct() {
        $this->database = new Budgex_Database();
        $this->permissions = new Budgex_Permissions();
        
        add_shortcode('budgex_dashboard', [$this, 'render_dashboard']);
        add_shortcode('budgex_budget_page', [$this, 'render_budget_page']);
        add_shortcode('budgex_enhanced_budget_page', [$this, 'render_enhanced_budget_page']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // AJAX handlers for logged-in users
        add_action('wp_ajax_budgex_add_outcome', [$this, 'ajax_add_outcome']);
        add_action('wp_ajax_budgex_edit_outcome', [$this, 'ajax_edit_outcome']);
        add_action('wp_ajax_budgex_delete_outcome', [$this, 'ajax_delete_outcome']);
        add_action('wp_ajax_budgex_accept_invitation', [$this, 'ajax_accept_invitation']);
        add_action('wp_ajax_budgex_add_additional_budget', [$this, 'ajax_add_additional_budget']);
        add_action('wp_ajax_budgex_increase_monthly_budget', [$this, 'ajax_increase_monthly_budget']);
        add_action('wp_ajax_budgex_invite_user', [$this, 'ajax_invite_user']);
        add_action('wp_ajax_budgex_create_budget_frontend', [$this, 'ajax_create_budget_frontend']);
        add_action('wp_ajax_budgex_get_dashboard_stats', [$this, 'ajax_get_dashboard_stats']);
        add_action('wp_ajax_budgex_get_user_budgets', [$this, 'ajax_get_user_budgets']);
        add_action('wp_ajax_budgex_get_outcomes', [$this, 'ajax_get_outcomes']);
        add_action('wp_ajax_budgex_get_monthly_breakdown', [$this, 'ajax_get_monthly_breakdown']);
        add_action('wp_ajax_budgex_get_budget_summary', [$this, 'ajax_get_budget_summary']);
        add_action('wp_ajax_budgex_get_outcome_edit_form', [$this, 'ajax_get_outcome_edit_form']);
        add_action('wp_ajax_budgex_get_invitation_details', [$this, 'ajax_get_invitation_details']);
        add_action('wp_ajax_budgex_send_invitation', [$this, 'ajax_send_invitation']);
        add_action('wp_ajax_budgex_remove_permission', [$this, 'ajax_remove_permission']);
        add_action('wp_ajax_budgex_cancel_invitation', [$this, 'ajax_cancel_invitation']);
        add_action('wp_ajax_budgex_resend_invitation', [$this, 'ajax_resend_invitation']);
        add_action('wp_ajax_budgex_edit_budget_details', [$this, 'ajax_edit_budget_details']);
        add_action('wp_ajax_budgex_change_start_date', [$this, 'ajax_change_start_date']);
        add_action('wp_ajax_budgex_change_user_role', [$this, 'ajax_change_user_role']);
        add_action('wp_ajax_budgex_remove_user', [$this, 'ajax_remove_user']);
        add_action('wp_ajax_budgex_generate_report', [$this, 'ajax_generate_report']);
        add_action('wp_ajax_budgex_delete_budget', [$this, 'ajax_delete_budget']);
        add_action('wp_ajax_budgex_export_excel', [$this, 'ajax_export_excel']);
        add_action('wp_ajax_budgex_generate_pdf', [$this, 'ajax_generate_pdf']);
        add_action('wp_ajax_nopriv_budgex_export_excel', [$this, 'ajax_export_excel']);
        add_action('wp_ajax_nopriv_budgex_generate_pdf', [$this, 'ajax_generate_pdf']);
        
        // Future and recurring expenses AJAX handlers
        add_action('wp_ajax_budgex_add_future_expense', [$this, 'ajax_add_future_expense']);
        add_action('wp_ajax_budgex_edit_future_expense', [$this, 'ajax_edit_future_expense']);
        add_action('wp_ajax_budgex_delete_future_expense', [$this, 'ajax_delete_future_expense']);
        add_action('wp_ajax_budgex_confirm_future_expense', [$this, 'ajax_confirm_future_expense']);
        add_action('wp_ajax_budgex_get_future_expenses', [$this, 'ajax_get_future_expenses']);
        add_action('wp_ajax_budgex_add_recurring_expense', [$this, 'ajax_add_recurring_expense']);
        add_action('wp_ajax_budgex_edit_recurring_expense', [$this, 'ajax_edit_recurring_expense']);
        add_action('wp_ajax_budgex_delete_recurring_expense', [$this, 'ajax_delete_recurring_expense']);
        add_action('wp_ajax_budgex_toggle_recurring_expense', [$this, 'ajax_toggle_recurring_expense']);
        add_action('wp_ajax_budgex_get_recurring_expenses', [$this, 'ajax_get_recurring_expenses']);
        add_action('wp_ajax_budgex_get_projected_balance', [$this, 'ajax_get_projected_balance']);
        
        // Enhanced budget page AJAX handlers
        add_action('wp_ajax_budgex_get_chart_data', [$this, 'ajax_get_chart_data']);
        add_action('wp_ajax_budgex_get_analysis_data', [$this, 'ajax_get_analysis_data']);
        add_action('wp_ajax_budgex_save_budget_settings', [$this, 'ajax_save_budget_settings']);
        add_action('wp_ajax_budgex_bulk_delete_outcomes', [$this, 'ajax_bulk_delete_outcomes']);
        add_action('wp_ajax_budgex_export_data', [$this, 'ajax_export_data']);
        add_action('wp_ajax_budgex_search_outcomes', [$this, 'ajax_search_outcomes']);
        add_action('wp_ajax_budgex_filter_outcomes', [$this, 'ajax_filter_outcomes']);
        add_action('wp_ajax_budgex_get_quick_stats', [$this, 'ajax_get_quick_stats']);
        add_action('wp_ajax_budgex_export_selected_outcomes', [$this, 'ajax_export_selected_outcomes']);
        add_action('wp_ajax_budgex_update_outcomes_category', [$this, 'ajax_update_outcomes_category']);
        
        add_action('wp', [$this, 'handle_budget_page_display']);
    }

    public function enqueue_styles() {
        wp_enqueue_style('budgex-public', BUDGEX_URL . 'public/css/budgex-public.css', array(), BUDGEX_VERSION);
        
        // Check if we're on a page that needs enhanced budget styles
        if ((is_page() && (has_shortcode(get_post()->post_content, 'budgex_enhanced_budget_page') || 
            get_query_var('budget_id'))) || 
            strpos($_SERVER['REQUEST_URI'], '/budgex/budget/') !== false ||
            (isset($_GET['page']) && $_GET['page'] === 'budgex') ||
            get_query_var('budget_id')) {
            wp_enqueue_style('budgex-enhanced-budget', BUDGEX_URL . 'public/css/budgex-enhanced-budget.css', array('budgex-public'), BUDGEX_VERSION);
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_script('budgex-public', BUDGEX_URL . 'public/js/budgex-public.js', ['jquery'], BUDGEX_VERSION, true);
        
        // Check if we're on a page that needs enhanced budget scripts
        if ((is_page() && (has_shortcode(get_post()->post_content, 'budgex_enhanced_budget_page') || 
            get_query_var('budget_id'))) || 
            strpos($_SERVER['REQUEST_URI'], '/budgex/budget/') !== false ||
            (isset($_GET['page']) && $_GET['page'] === 'budgex') ||
            get_query_var('budget_id')) {
            // Enqueue Chart.js for data visualization
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
            wp_enqueue_script('budgex-enhanced-budget', BUDGEX_URL . 'public/js/budgex-enhanced-budget.js', 
                ['jquery', 'budgex-public', 'chart-js'], BUDGEX_VERSION, true);
        }
        
        wp_localize_script('budgex-public', 'budgex_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('budgex_public_nonce'),
            'dashboard_url' => home_url('/budgex/'),
            'budget_url' => home_url('/budgexpage/'),
            'strings' => array(
                'loading' => __('טוען...', 'budgex'),
                'error' => __('שגיאה', 'budgex'),
                'success' => __('הצלחה', 'budgex'),
                'confirm_delete' => __('האם אתה בטוח שברצונך למחוק?', 'budgex'),
                'delete_outcome' => __('מחק הוצאה', 'budgex'),
                'edit_outcome' => __('ערוך הוצאה', 'budgex'),
                'save_changes' => __('שמור שינויים', 'budgex'),
                'cancel' => __('ביטול', 'budgex'),
                'no_data' => __('אין נתונים', 'budgex'),
                'outcome_added' => __('ההוצאה נוספה בהצלחה', 'budgex'),
                'outcome_updated' => __('ההוצאה עודכנה בהצלחה', 'budgex'),
                'outcome_deleted' => __('ההוצאה נמחקה בהצלחה', 'budgex'),
                'invitation_accepted' => __('ההזמנה אושרה בהצלחה', 'budgex'),
                'bulk_delete_confirm' => __('האם אתה בטוח שברצונך למחוק את כל הפריטים הנבחרים?', 'budgex'),
                'export_success' => __('הנתונים יוצאו בהצלחה', 'budgex'),
                'settings_saved' => __('ההגדרות נשמרו בהצלחה', 'budgex'),
                'auto_saved' => __('נשמר אוטומטית', 'budgex')
            )
        ));
    }

    public function handle_budget_page_display() {
        global $post;
        
        if ($post && $post->post_title === 'Budgex' && get_query_var('budget_id')) {
            $budget_id = intval(get_query_var('budget_id'));
            $this->display_single_budget($budget_id);
            exit;
        }
    }

    public function render_dashboard($atts = array()) {
        if (!is_user_logged_in()) {
            return '<div class="budgex-login-required">' . 
                   '<p>' . __('נדרש להתחבר כדי לצפות בתקציבים', 'budgex') . '</p>' .
                   '<a href="' . wp_login_url(get_permalink()) . '" class="button">' . __('התחבר', 'budgex') . '</a>' .
                   '</div>';
        }
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/budgex-dashboard.php';
        return ob_get_clean();
    }

    public function render_budget_page($atts) {
        $atts = shortcode_atts(['id' => ''], $atts);
        
        if (!is_user_logged_in()) {
            return '<div class="budgex-login-required">' . 
                   '<p>' . __('נדרש להתחבר כדי לצפות בתקציב', 'budgex') . '</p>' .
                   '<a href="' . wp_login_url(get_permalink()) . '" class="button">' . __('התחבר', 'budgex') . '</a>' .
                   '</div>';
        }
        
        $budget_id = intval($atts['id']);
        if (!$budget_id) {
            return '<div class="budgex-error"><p>' . __('לא נבחר תקציב תקין', 'budgex') . '</p></div>';
        }
        
        ob_start();
        $this->display_single_budget($budget_id);
        return ob_get_clean();
    }

    public function render_enhanced_budget_page($atts) {
        $atts = shortcode_atts(['id' => ''], $atts);
        
        if (!is_user_logged_in()) {
            return '<div class="budgex-login-required">' . 
                   '<p>' . __('נדרש להתחבר כדי לצפות בתקציב', 'budgex') . '</p>' .
                   '<a href="' . wp_login_url(get_permalink()) . '" class="button">' . __('התחבר', 'budgex') . '</a>' .
                   '</div>';
        }
        
        $budget_id = intval($atts['id']);
        if (!$budget_id) {
            return '<div class="budgex-error"><p>' . __('לא נבחר תקציב תקין', 'budgex') . '</p></div>';
        }
        
        ob_start();
        $this->display_enhanced_budget($budget_id);
        return ob_get_clean();
    }

    private function display_single_budget($budget_id) {
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            include plugin_dir_path(__FILE__) . 'partials/budgex-no-access.php';
            return;
        }
        
        $budget = $this->database->get_budget($budget_id);
        $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
        $outcomes = $this->database->get_budget_outcomes($budget_id);
        $monthly_breakdown = Budgex_Budget_Calculator::get_monthly_breakdown($budget_id);
        $user_role = $this->permissions->get_user_role_on_budget($budget_id, $user_id);
        
        include plugin_dir_path(__FILE__) . 'partials/budgex-budget-page.php';
    }

    private function display_enhanced_budget($budget_id) {
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            include plugin_dir_path(__FILE__) . 'partials/budgex-no-access.php';
            return;
        }
        
        $budget = $this->database->get_budget($budget_id);
        $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
        $outcomes = $this->database->get_budget_outcomes($budget_id);
        $monthly_breakdown = Budgex_Budget_Calculator::get_monthly_breakdown($budget_id);
        $user_role = $this->permissions->get_user_role_on_budget($budget_id, $user_id);
        
        // Get additional data for enhanced features
        $future_expenses = $this->database->get_future_expenses($budget_id);
        $recurring_expenses = $this->database->get_recurring_expenses($budget_id);
        $budget_users = $this->database->get_budget_users($budget_id);
        $pending_invitations = $this->database->get_budget_invitations($budget_id);
        
        include plugin_dir_path(__FILE__) . 'partials/budgex-public-enhanced-budget-page.php';
    }

    /**
     * Display dashboard for frontend access
     */
    public function display_dashboard_frontend() {
        if (!is_user_logged_in()) {
            return '<div class="budgex-login-required">' . 
                   '<p>' . __('נדרש להתחבר כדי לצפות בתקציבים', 'budgex') . '</p>' .
                   '<a href="' . wp_login_url(home_url('/budgex/')) . '" class="button">' . __('התחבר', 'budgex') . '</a>' .
                   '</div>';
        }
        
        $user_id = get_current_user_id();
        $budgets = $this->database->get_user_budgets($user_id);
        $shared_budgets = $this->database->get_shared_budgets($user_id);
        $pending_invitations = $this->database->get_pending_invitations($user_id);
        
        // Calculate total statistics
        $total_budgets = count($budgets) + count($shared_budgets);
        $total_spent = 0;
        $total_remaining = 0;
        
        foreach (array_merge($budgets, $shared_budgets) as $budget) {
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget->id);
            $total_spent += $calculation['total_spent'];
            $total_remaining += $calculation['remaining'];
        }
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/budgex-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Display single budget for frontend access with security check
     */
    public function display_single_budget_frontend($budget_id) {
        // Force enqueue enhanced assets for this page
        $this->force_enqueue_enhanced_assets();
        
        // Add error reporting for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Budgex: display_single_budget_frontend called with budget_id: $budget_id");
        }
        
        if (!is_user_logged_in()) {
            return '<div class="budgex-login-required">' . 
                   '<p>' . __('נדרש להתחבר כדי לצפות בתקציב', 'budgex') . '</p>' .
                   '<a href="' . wp_login_url(home_url('/budgex/')) . '" class="button">' . __('התחבר', 'budgex') . '</a>' .
                   '</div>';
        }
        
        $user_id = get_current_user_id();
        $budget_id = intval($budget_id);
        
        // Debug log
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Budgex: User ID: $user_id, Budget ID: $budget_id");
        }
        
        // Security check: Can user view this budget?
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Budgex: Access denied for user $user_id to budget $budget_id");
            }
            return '<div class="budgex-no-access">' . 
                   '<h3>' . __('אין הרשאה לצפות בתקציב זה', 'budgex') . '</h3>' .
                   '<p>' . __('אין לך הרשאה לצפות בתקציב זה. אם לדעתך זה טעות, צור קשר עם בעל התקציב.', 'budgex') . '</p>' .
                   '<a href="' . home_url('/budgex/') . '" class="button">' . __('חזור לדף הבית', 'budgex') . '</a>' .
                   '</div>';
        }
        
        $budget = $this->database->get_budget($budget_id);
        if (!$budget) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Budgex: Budget $budget_id not found in database");
            }
            return '<div class="budgex-error">' . 
                   '<h3>' . __('תקציב לא נמצא', 'budgex') . '</h3>' .
                   '<p>' . __('התקציב שביקשת לא נמצא במערכת.', 'budgex') . '</p>' .
                   '<a href="' . home_url('/budgex/') . '" class="button">' . __('חזור לדף הבית', 'budgex') . '</a>' .
                   '</div>';
        }
        
        // Debug log
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Budgex: Budget found, starting data collection");
        }
        
        try {
            // Use the enhanced budget page template for better user experience
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
            $outcomes = $this->database->get_budget_outcomes($budget_id);
            $monthly_breakdown = Budgex_Budget_Calculator::get_monthly_breakdown($budget_id);
            $user_role = $this->permissions->get_user_role_on_budget($budget_id, $user_id);
            
            // Get additional data for enhanced features
            $calculator = new Budgex_Budget_Calculator();
            $shared_users = $this->database->get_budget_shared_users($budget_id);
            $pending_invitations = $this->database->get_pending_invitations($budget_id);
            $future_expenses = $this->database->get_future_expenses($budget_id);
            $recurring_expenses = $this->database->get_recurring_expenses($budget_id);
            $budget_adjustments = $this->database->get_budget_adjustments($budget_id);
            
            // Calculate projected balance for enhanced features
            $projected_balance = $calculator->calculate_projected_balance($budget_id);
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Budgex: Data collection complete, loading template");
                error_log("Budgex: Outcomes count: " . count($outcomes));
                error_log("Budgex: Future expenses count: " . count($future_expenses));
            }
            
            $template_path = plugin_dir_path(__FILE__) . 'partials/budgex-public-enhanced-budget-page.php';
            if (!file_exists($template_path)) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("Budgex: Template not found at $template_path");
                }
                return '<div class="budgex-error"><h3>Template Error</h3><p>Enhanced budget template not found.</p></div>';
            }
            
            ob_start();
            include $template_path;
            $content = ob_get_clean();
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Budgex: Template loaded, content length: " . strlen($content));
            }
            
            return $content;
            
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Budgex: Exception in display_single_budget_frontend: " . $e->getMessage());
            }
            return '<div class="budgex-error"><h3>Error Loading Budget</h3><p>' . $e->getMessage() . '</p></div>';
        }
    }

    public function ajax_add_outcome() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['amount']);
        $description = sanitize_textarea_field($_POST['description']);
        $outcome_name = sanitize_text_field($_POST['outcome_name']);
        $outcome_date = sanitize_text_field($_POST['outcome_date']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_add_outcomes($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה להוסיף הוצאות לתקציב זה', 'budgex')));
        }

        if ($amount <= 0 || empty($description) || empty($outcome_name)) {
            wp_send_json_error(array('message' => __('נא למלא את כל השדות הנדרשים', 'budgex')));
        }

        $result = $this->database->add_outcome($budget_id, $amount, $description, $outcome_name, $outcome_date);
        
        if ($result) {
            $budget = $this->database->get_budget($budget_id);
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
            
            wp_send_json_success(array(
                'message' => __('ההוצאה נוספה בהצלחה', 'budgex'),
                'remaining_budget' => $calculation['remaining'],
                'spent_amount' => $calculation['total_spent'],
                'outcome_id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהוספת ההוצאה', 'budgex')));
        }
    }

    public function ajax_accept_invitation() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $invitation_id = intval($_POST['invitation_id']);
        $user_id = get_current_user_id();
        
        $result = $this->database->accept_invitation($invitation_id, $user_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('ההזמנה אושרה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה באישור ההזמנה', 'budgex')));
        }
    }

    public function ajax_edit_outcome() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $outcome_id = intval($_POST['outcome_id']);
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['amount']);
        $description = sanitize_textarea_field($_POST['description']);
        $outcome_name = sanitize_text_field($_POST['outcome_name']);
        $outcome_date = sanitize_text_field($_POST['outcome_date']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_edit_outcomes($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך הוצאות בתקציב זה', 'budgex')));
        }

        if ($amount <= 0 || empty($description) || empty($outcome_name)) {
            wp_send_json_error(array('message' => __('נא למלא את כל השדות הנדרשים', 'budgex')));
        }

        $result = $this->database->update_outcome($outcome_id, $amount, $description, $outcome_name, $outcome_date);
        
        if ($result) {
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
            
            wp_send_json_success(array(
                'message' => __('ההוצאה עודכנה בהצלחה', 'budgex'),
                'remaining_budget' => $calculation['remaining'],
                'spent_amount' => $calculation['total_spent']
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בעדכון ההוצאה', 'budgex')));
        }
    }

    public function ajax_delete_outcome() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $outcome_id = intval($_POST['outcome_id']);
        $budget_id = intval($_POST['budget_id']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_delete_outcomes($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה למחוק הוצאות בתקציב זה', 'budgex')));
        }

        $result = $this->database->delete_outcome($outcome_id);
        
        if ($result) {
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
            
            wp_send_json_success(array(
                'message' => __('ההוצאה נמחקה בהצלחה', 'budgex'),
                'remaining_budget' => $calculation['remaining'],
                'spent_amount' => $calculation['total_spent']
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה במחיקת ההוצאה', 'budgex')));
        }
    }

    public function ajax_get_dashboard_stats() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $budgets = $this->database->get_user_budgets($user_id);
        
        $total_budgets = count($budgets);
        $total_budget_amount = 0;
        $total_spent = 0;
        $active_budgets = 0;
        
        foreach ($budgets as $budget) {
            $total_budget_amount += $budget->monthly_budget;
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget->id);
            $total_spent += $calculation['total_spent'];
            
            if ($calculation['remaining'] > 0) {
                $active_budgets++;
            }
        }
        
        wp_send_json_success(array(
            'total_budgets' => $total_budgets,
            'total_budget_amount' => $total_budget_amount,
            'total_spent' => $total_spent,
            'total_remaining' => $total_budget_amount - $total_spent,
            'active_budgets' => $active_budgets
        ));
    }

    public function ajax_get_user_budgets() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        $budgets = $this->database->get_user_budgets($user_id);
        
        $formatted_budgets = array();
        foreach ($budgets as $budget) {
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget->id);
            $user_role = $this->permissions->get_user_role_on_budget($budget->id, $user_id);
            
            $formatted_budgets[] = array(
                'id' => $budget->id,
                'name' => $budget->budget_name,
                'amount' => $budget->monthly_budget,
                'remaining' => $calculation['remaining'],
                'spent' => $calculation['total_spent'],
                'percentage_used' => $calculation['percentage_used'],
                'role' => $user_role,
                'start_date' => $budget->start_date,
                'end_date' => isset($budget->end_date) ? $budget->end_date : null
            );
        }
        
        wp_send_json_success(array('budgets' => $formatted_budgets));
    }

    public function ajax_get_outcomes() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }
        
        $outcomes = $this->database->get_budget_outcomes($budget_id);
        
        wp_send_json_success(array('outcomes' => $outcomes));
    }

    public function ajax_get_monthly_breakdown() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }
        
        $breakdown = Budgex_Budget_Calculator::get_monthly_breakdown($budget_id);
        
        wp_send_json_success(array('breakdown' => $breakdown));
    }

    public function ajax_get_budget_summary() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $user_id = get_current_user_id();
        
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }
        
        $budget = $this->database->get_budget($budget_id);
        $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
        $user_role = $this->permissions->get_user_role_on_budget($budget_id, $user_id);
        
        wp_send_json_success(array(
            'budget' => $budget,
            'calculation' => $calculation,
            'user_role' => $user_role
        ));
    }

    public function ajax_get_outcome_edit_form() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $outcome_id = intval($_POST['outcome_id']);
        $budget_id = intval($_POST['budget_id']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_edit_outcomes($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך הוצאות בתקציב זה', 'budgex')));
        }
        
        $outcome = $this->database->get_outcome($outcome_id);
        
        if (!$outcome) {
            wp_send_json_error(array('message' => __('הוצאה לא נמצאה', 'budgex')));
        }
        
        wp_send_json_success(array('outcome' => $outcome));
    }

    public function ajax_get_invitation_details() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $invitation_id = intval($_POST['invitation_id']);
        $user_id = get_current_user_id();
        
        $invitation = $this->database->get_invitation($invitation_id);
        
        if (!$invitation || $invitation['invited_user_id'] != $user_id) {
            wp_send_json_error(array('message' => __('הזמנה לא נמצאה', 'budgex')));
        }
        
        $budget = $this->database->get_budget($invitation['budget_id']);
        $inviter = get_userdata($invitation['inviter_id']);
        
        wp_send_json_success(array(
            'invitation' => $invitation,
            'budget' => $budget,
            'inviter' => array(
                'display_name' => $inviter->display_name,
                'user_email' => $inviter->user_email
            )
        ));
    }

    public function ajax_send_invitation() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $invite_email = sanitize_email($_POST['invite_email']);
        $role = sanitize_text_field($_POST['role']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_invite_users($budget_id, $user_id)) {
            wp_send_json_error(__('אין הרשאה להזמין משתמשים לתקציב זה', 'budgex'));
        }

        if (empty($invite_email) || !is_email($invite_email)) {
            wp_send_json_error(__('כתובת אימייל לא תקינה', 'budgex'));
        }

        if (!in_array($role, ['viewer', 'editor', 'admin'])) {
            wp_send_json_error(__('תפקיד לא תקין', 'budgex'));
        }

        // Check if user exists
        $invited_user = get_user_by('email', $invite_email);
        if (!$invited_user) {
            wp_send_json_error(__('משתמש עם כתובת אימייל זו לא נמצא', 'budgex'));
        }

        // Check if user is already invited or has access
        $existing_permission = $this->permissions->get_user_role_on_budget($budget_id, $invited_user->ID);
        if ($existing_permission) {
            wp_send_json_error(__('משתמש זה כבר יש לו גישה לתקציב', 'budgex'));
        }

        $result = $this->database->create_invitation($budget_id, $user_id, $invited_user->ID, $role);
        
        if ($result) {
            wp_send_json_success(__('ההזמנה נשלחה בהצלחה', 'budgex'));
        } else {
            wp_send_json_error(__('שגיאה בשליחת ההזמנה', 'budgex'));
        }
    }

    public function ajax_remove_permission() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $permission_id = intval($_POST['permission_id']);
        $user_id = get_current_user_id();

        // Get permission details
        global $wpdb;
        $permission = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}budgex_budget_shares WHERE id = %d",
                $permission_id
            ),
            ARRAY_A
        );

        if (!$permission) {
            wp_send_json_error(__('הרשאה לא נמצאה', 'budgex'));
        }

        if (!$this->permissions->can_invite_users($permission['budget_id'], $user_id)) {
            wp_send_json_error(__('אין הרשאה לנהל משתמשים בתקציב זה', 'budgex'));
        }

        $result = $wpdb->delete(
            $wpdb->prefix . 'budgex_budget_shares',
            array('id' => $permission_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success(__('המשתמש הוסר בהצלחה', 'budgex'));
        } else {
            wp_send_json_error(__('שגיאה בהסרת המשתמש', 'budgex'));
        }
    }

    /**
     * AJAX handler for adding additional budget on frontend
     */
    public function ajax_add_additional_budget() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['additional_amount']);
        $description = sanitize_text_field($_POST['additional_description']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_edit_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך תקציב זה', 'budgex')));
        }

        if ($amount <= 0) {
            wp_send_json_error(array('message' => __('נא להזין סכום חיובי', 'budgex')));
        }

        $result = $this->database->add_additional_budget($budget_id, $amount, $description);
        
        if ($result) {
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
            
            wp_send_json_success(array(
                'message' => __('התקציב הנוסף נוסף בהצלחה', 'budgex'),
                'new_total' => $calculation['total_available'],
                'additional_amount' => $amount
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהוספת התקציב הנוסף', 'budgex')));
        }
    }

    /**
     * AJAX handler for creating budget from frontend
     */
    public function ajax_create_budget_frontend() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        // Validate input
        $budget_name = sanitize_text_field($_POST['budget_name']);
        $monthly_budget = floatval($_POST['monthly_budget']);
        $currency = sanitize_text_field($_POST['currency']);
        $start_date = sanitize_text_field($_POST['start_date']);
        
        if (empty($budget_name) || $monthly_budget <= 0 || empty($currency) || empty($start_date)) {
            wp_send_json_error(array('message' => __('נדרש למלא את כל השדות', 'budgex')));
        }
        
        // Validate currency
        $allowed_currencies = array('ILS', 'USD', 'EUR');
        if (!in_array($currency, $allowed_currencies)) {
            wp_send_json_error(array('message' => __('מטבע לא תקין', 'budgex')));
        }
        
        // Validate date
        if (!strtotime($start_date)) {
            wp_send_json_error(array('message' => __('תאריך לא תקין', 'budgex')));
        }
        
        // Create budget
        $budget_id = $this->database->create_budget($user_id, $budget_name, $monthly_budget, $currency, $start_date);
        
        if ($budget_id) {
            wp_send_json_success(array(
                'message' => __('התקציב נוצר בהצלחה', 'budgex'),
                'budget_id' => $budget_id,
                'budget_name' => $budget_name
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה ביצירת התקציב', 'budgex')));
        }
    }

    /**
     * AJAX handler for editing budget details
     */
    public function ajax_edit_budget_details() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        
        // Check permissions
        if (!$this->permissions->is_budget_owner($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לעריכת התקציב', 'budgex')));
        }
        
        $budget_name = sanitize_text_field($_POST['budget_name']);
        $budget_description = sanitize_textarea_field($_POST['budget_description']);
        
        if (empty($budget_name)) {
            wp_send_json_error(array('message' => __('שם התקציב הוא שדה חובה', 'budgex')));
        }
        
        $success = $this->database->update_budget_details($budget_id, $budget_name, $budget_description);
        
        if ($success) {
            wp_send_json_success(array('message' => __('פרטי התקציב עודכנו בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בעדכון פרטי התקציב', 'budgex')));
        }
    }

    /**
     * AJAX handler for changing budget start date
     */
    public function ajax_change_start_date() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        
        // Check permissions
        if (!$this->permissions->is_budget_owner($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לעריכת התקציב', 'budgex')));
        }
        
        $new_start_date = sanitize_text_field($_POST['new_start_date']);
        
        if (!strtotime($new_start_date)) {
            wp_send_json_error(array('message' => __('תאריך לא תקין', 'budgex')));
        }
        
        $success = $this->database->update_budget_start_date($budget_id, $new_start_date);
        
        if ($success) {
            wp_send_json_success(array('message' => __('תאריך ההתחלה עודכן בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בעדכון תאריך ההתחלה', 'budgex')));
        }
    }

    /**
     * AJAX handler for changing user role
     */
    public function ajax_change_user_role() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        $target_user_id = intval($_POST['user_id']);
        $new_role = sanitize_text_field($_POST['new_role']);
        
        // Check permissions
        if (!$this->permissions->is_budget_owner($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לניהול משתמשים', 'budgex')));
        }
        
        // Validate role
        $allowed_roles = array('viewer', 'admin');
        if (!in_array($new_role, $allowed_roles)) {
            wp_send_json_error(array('message' => __('תפקיד לא תקין', 'budgex')));
        }
        
        $success = $this->database->update_user_role($budget_id, $target_user_id, $new_role);
        
        if ($success) {
            wp_send_json_success(array('message' => __('תפקיד המשתמש עודכן בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בעדכון תפקיד המשתמש', 'budgex')));
        }
    }

    /**
     * AJAX handler for removing user from budget
     */
    public function ajax_remove_user() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        $target_user_id = intval($_POST['user_id']);
        
        // Check permissions
        if (!$this->permissions->is_budget_owner($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לניהול משתמשים', 'budgex')));
        }
        
        // Cannot remove owner
        if ($this->permissions->is_budget_owner($budget_id, $target_user_id)) {
            wp_send_json_error(array('message' => __('לא ניתן להסיר את בעל התקציב', 'budgex')));
        }
        
        $success = $this->database->remove_user_from_budget($budget_id, $target_user_id);
        
        if ($success) {
            wp_send_json_success(array('message' => __('המשתמש הוסר בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהסרת המשתמש', 'budgex')));
        }
    }

    /**
     * AJAX handler for resending invitation
     */
    public function ajax_resend_invitation() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $invitation_id = intval($_POST['invitation_id']);
        $invitation = $this->database->get_invitation($invitation_id);
        
        if (!$invitation) {
            wp_send_json_error(array('message' => __('הזמנה לא נמצאה', 'budgex')));
        }
        
        // Check permissions
        if (!$this->permissions->is_budget_owner($invitation->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לניהול הזמנות', 'budgex')));
        }
        
        $success = $this->database->resend_invitation($invitation_id);
        
        if ($success) {
            wp_send_json_success(array('message' => __('ההזמנה נשלחה שוב בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בשליחת ההזמנה', 'budgex')));
        }
    }

    /**
     * AJAX handler for canceling invitation
     */
    public function ajax_cancel_invitation() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $invitation_id = intval($_POST['invitation_id']);
        $invitation = $this->database->get_invitation($invitation_id);
        
        if (!$invitation) {
            wp_send_json_error(array('message' => __('הזמנה לא נמצאה', 'budgex')));
        }
        
        // Check permissions
        if (!$this->permissions->is_budget_owner($invitation->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לניהול הזמנות', 'budgex')));
        }
        
        $success = $this->database->cancel_invitation($invitation_id);
        
        if ($success) {
            wp_send_json_success(array('message' => __('ההזמנה בוטלה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בביטול ההזמנה', 'budgex')));
        }
    }

    /**
     * AJAX handler for generating reports
     */
    public function ajax_generate_report() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        $report_type = sanitize_text_field($_POST['report_type']);
        
        // Check permissions
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפייה בתקציב', 'budgex')));
        }
        
        $report_content = '';
        
        switch ($report_type) {
            case 'monthly':
                $report_content = $this->generate_monthly_report($budget_id);
                break;
            case 'category':
                $report_content = $this->generate_category_report($budget_id);
                break;
            default:
                wp_send_json_error(array('message' => __('סוג דוח לא תקין', 'budgex')));
        }
        
        wp_send_json_success(array('content' => $report_content));
    }

    /**
     * AJAX handler for Excel export
     */
    public function ajax_export_excel() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_die(__('נדרש להתחבר', 'budgex'));
        }
        
        $budget_id = intval($_GET['budget_id']);
        
        // Check permissions
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_die(__('אין הרשאה לצפייה בתקציב', 'budgex'));
        }
        
        $budget = $this->database->get_budget($budget_id);
        $outcomes = $this->database->get_outcomes($budget_id);
        
        // Set headers for Excel download
        $filename = sanitize_file_name('budget_' . $budget->name . '_' . date('Y-m-d') . '.csv');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Create CSV content
        $output = fopen('php://output', 'w');
        
        // Add BOM for Hebrew support
        fputs($output, "\xEF\xBB\xBF");
        
        // CSV headers
        fputcsv($output, array(
            __('תאריך', 'budgex'),
            __('שם ההוצאה', 'budgex'),
            __('תיאור', 'budgex'),
            __('סכום', 'budgex'),
            __('מטבע', 'budgex')
        ));
        
        // Add budget info
        fputcsv($output, array());
        fputcsv($output, array(__('פרטי תקציב:', 'budgex')));
        fputcsv($output, array(__('שם התקציב:', 'budgex'), $budget->name));
        fputcsv($output, array(__('תקציב חודשי:', 'budgex'), $budget->monthly_budget));
        fputcsv($output, array(__('מטבע:', 'budgex'), $budget->currency));
        fputcsv($output, array(__('תאריך התחלה:', 'budgex'), $budget->start_date));
        fputcsv($output, array());
        fputcsv($output, array(__('הוצאות:', 'budgex')));
        
        // Add outcomes
        foreach ($outcomes as $outcome) {
            fputcsv($output, array(
                $outcome->date,
                $outcome->name,
                $outcome->description,
                $outcome->amount,
                $budget->currency
            ));
        }
        
        fclose($output);
        exit;
    }

    /**
     * AJAX handler for PDF generation
     */
    public function ajax_generate_pdf() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_die(__('נדרש להתחבר', 'budgex'));
        }
        
        $budget_id = intval($_GET['budget_id']);
        
        // Check permissions
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_die(__('אין הרשאה לצפייה בתקציב', 'budgex'));
        }
        
        $budget = $this->database->get_budget($budget_id);
        $outcomes = $this->database->get_outcomes($budget_id);
        $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
        
        // Generate HTML content for PDF
        $html = '<!DOCTYPE html>
        <html dir="rtl" lang="he">
        <head>
            <meta charset="UTF-8">
            <title>' . esc_html($budget->name) . ' - דוח מפורט</title>
            <style>
                body { font-family: Arial, sans-serif; direction: rtl; }
                .header { text-align: center; margin-bottom: 30px; }
                .budget-info { margin-bottom: 20px; }
                .budget-info table { width: 100%; border-collapse: collapse; }
                .budget-info th, .budget-info td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                .budget-info th { background-color: #f2f2f2; }
                .outcomes-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .outcomes-table th, .outcomes-table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                .outcomes-table th { background-color: #f2f2f2; }
                .summary { margin-top: 30px; }
                .summary-box { background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>דוח תקציב מפורט</h1>
                <h2>' . esc_html($budget->name) . '</h2>
                <p>נוצר בתאריך: ' . date_i18n(get_option('date_format')) . '</p>
            </div>
            
            <div class="budget-info">
                <h3>פרטי התקציב</h3>
                <table>
                    <tr><th>שם התקציב</th><td>' . esc_html($budget->name) . '</td></tr>
                    <tr><th>תיאור</th><td>' . esc_html($budget->description) . '</td></tr>
                    <tr><th>תקציב חודשי</th><td>' . number_format($budget->monthly_budget, 2) . ' ' . $budget->currency . '</td></tr>
                    <tr><th>מטבע</th><td>' . $budget->currency . '</td></tr>
                    <tr><th>תאריך התחלה</th><td>' . date_i18n(get_option('date_format'), strtotime($budget->start_date)) . '</td></tr>
                </table>
            </div>
            
            <div class="summary">
                <h3>סיכום</h3>
                <div class="summary-box">
                    <p><strong>סך הוצאות:</strong> ' . number_format($calculation['total_spent'], 2) . ' ' . $budget->currency . '</p>
                    <p><strong>יתרה:</strong> ' . number_format($calculation['remaining'], 2) . ' ' . $budget->currency . '</p>
                    <p><strong>מספר הוצאות:</strong> ' . count($outcomes) . '</p>
                </div>
            </div>';
        
        if (!empty($outcomes)) {
            $html .= '
            <div class="outcomes">
                <h3>רשימת הוצאות</h3>
                <table class="outcomes-table">
                    <thead>
                        <tr>
                            <th>תאריך</th>
                            <th>שם ההוצאה</th>
                            <th>תיאור</th>
                            <th>סכום</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($outcomes as $outcome) {
                $html .= '<tr>
                    <td>' . date_i18n(get_option('date_format'), strtotime($outcome->date)) . '</td>
                    <td>' . esc_html($outcome->name) . '</td>
                    <td>' . esc_html($outcome->description) . '</td>
                    <td>' . number_format($outcome->amount, 2) . ' ' . $budget->currency . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table></div>';
        }
        
        $html .= '</body></html>';
        
        // Simple HTML to PDF conversion (basic implementation)
        $filename = sanitize_file_name('budget_' . $budget->name . '_' . date('Y-m-d') . '.html');
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo $html;
        exit;
    }

    /**
     * Generate monthly report
     */
    private function generate_monthly_report($budget_id) {
        $budget = $this->database->get_budget($budget_id);
        $outcomes = $this->database->get_outcomes($budget_id);
        
        // Group outcomes by month
        $monthly_data = array();
        foreach ($outcomes as $outcome) {
            $month = date('Y-m', strtotime($outcome->date));
            if (!isset($monthly_data[$month])) {
                $monthly_data[$month] = array('count' => 0, 'total' => 0);
            }
            $monthly_data[$month]['count']++;
            $monthly_data[$month]['total'] += $outcome->amount;
        }
        
        $html = '<div class="monthly-report">';
        $html .= '<h4>' . __('דוח חודשי', 'budgex') . '</h4>';
        $html .= '<table class="report-table">';
        $html .= '<thead><tr><th>' . __('חודש', 'budgex') . '</th><th>' . __('מספר הוצאות', 'budgex') . '</th><th>' . __('סכום כולל', 'budgex') . '</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($monthly_data as $month => $data) {
            $html .= '<tr>';
            $html .= '<td>' . date_i18n('F Y', strtotime($month . '-01')) . '</td>';
            $html .= '<td>' . $data['count'] . '</td>';
            $html .= '<td>' . number_format($data['total'], 2) . ' ' . $budget->currency . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></div>';
        
        return $html;
    }

    /**
     * Generate category report
     */
    private function generate_category_report($budget_id) {
        $outcomes = $this->database->get_outcomes($budget_id);
        $budget = $this->database->get_budget($budget_id);
        
        // Group by category (first word of description)
        $category_data = array();
        foreach ($outcomes as $outcome) {
            $category = explode(' ', $outcome->description)[0];
            if (!isset($category_data[$category])) {
                $category_data[$category] = array('count' => 0, 'total' => 0);
            }
            $category_data[$category]['count']++;
            $category_data[$category]['total'] += $outcome->amount;
        }
        
        $html = '<div class="category-report">';
        $html .= '<h4>' . __('דוח לפי קטגוריות', 'budgex') . '</h4>';
        $html .= '<table class="report-table">';
        $html .= '<thead><tr><th>' . __('קטגוריה', 'budgex') . '</th><th>' . __('מספר הוצאות', 'budgex') . '</th><th>' . __('סכום כולל', 'budgex') . '</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($category_data as $category => $data) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($category) . '</td>';
            $html .= '<td>' . $data['count'] . '</td>';
            $html .= '<td>' . number_format($data['total'], 2) . ' ' . $budget->currency . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></div>';
        
        return $html;
    }

    /**
     * AJAX handler for deleting budget
     */
    public function ajax_delete_budget() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        
        // Check permissions - only budget owner can delete
        if (!$this->permissions->can_delete_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה למחוק תקציב זה', 'budgex')));
        }
        
        $budget = $this->database->get_budget($budget_id);
        if (!$budget) {
            wp_send_json_error(array('message' => __('תקציב לא נמצא', 'budgex')));
        }
        
        // Start transaction to ensure data integrity
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        
        try {
            // Delete related data first (outcomes, permissions, invitations)
            $wpdb->delete(
                $wpdb->prefix . 'budgex_outcomes',
                array('budget_id' => $budget_id),
                array('%d')
            );
            
            $wpdb->delete(
                $wpdb->prefix . 'budgex_budget_permissions',
                array('budget_id' => $budget_id),
                array('%d')
            );
            
            $wpdb->delete(
                $wpdb->prefix . 'budgex_invitations',
                array('budget_id' => $budget_id),
                array('%d')
            );
            
            // Delete the budget itself
            $result = $this->database->delete_budget($budget_id);
            
            if ($result === false) {
                throw new Exception('Failed to delete budget');
            }
            
            $wpdb->query('COMMIT');
            
            wp_send_json_success(array(
                'message' => __('התקציב נמחק בהצלחה', 'budgex'),
                'budget_name' => $budget->name
            ));
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            $this->log_error('Failed to delete budget: ' . $e->getMessage(), array('budget_id' => $budget_id));
            wp_send_json_error(array('message' => __('שגיאה במחיקת התקציב', 'budgex')));
        }
    }

    /**
     * AJAX handler for increasing monthly budget
     */
    public function ajax_increase_monthly_budget() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => __('נדרש להתחבר', 'budgex')));
        }
        
        $budget_id = intval($_POST['budget_id']);
        $new_amount = floatval($_POST['new_amount']);
        $effective_date = sanitize_text_field($_POST['effective_date']);
        $reason = sanitize_textarea_field($_POST['reason']);
        
        // Validate inputs
        if (!$budget_id || $new_amount <= 0) {
            wp_send_json_error(array('message' => __('נתונים לא תקינים', 'budgex')));
        }
        
        // Validate date format
        if (!$effective_date || !strtotime($effective_date)) {
            wp_send_json_error(array('message' => __('תאריך לא תקין', 'budgex')));
        }
        
        // Check permissions - only budget owner can increase budget
        if (!$this->permissions->is_budget_owner($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לשנות תקציב זה', 'budgex')));
        }
        
        // Get current budget
        $budget = $this->database->get_budget($budget_id);
        if (!$budget) {
            wp_send_json_error(array('message' => __('תקציב לא נמצא', 'budgex')));
        }
        
        // Check that new amount is higher than current
        $current_amount = $this->database->get_monthly_budget_for_date($budget_id, $effective_date);
        if ($new_amount <= $current_amount) {
            wp_send_json_error(array('message' => __('הסכום החדש חייב להיות גבוה מהסכום הנוכחי', 'budgex')));
        }
        
        // Check that effective date is not in the past (allow today)
        $today = date('Y-m-d');
        if ($effective_date < $today) {
            wp_send_json_error(array('message' => __('לא ניתן להגדיר תאריך יעד בעבר', 'budgex')));
        }
        
        // Add the budget adjustment
        $adjustment_id = $this->database->add_budget_adjustment(
            $budget_id,
            $new_amount,
            $effective_date,
            $reason,
            $user_id
        );
        
        if ($adjustment_id) {
            wp_send_json_success(array(
                'message' => __('התקציב החודשי הוגדל בהצלחה', 'budgex'),
                'adjustment_id' => $adjustment_id,
                'new_amount' => number_format($new_amount, 2),
                'effective_date' => date_i18n(get_option('date_format'), strtotime($effective_date)),
                'currency' => $budget->currency
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהגדלת התקציב', 'budgex')));
        }
    }

    /**
     * FUTURE EXPENSES AJAX HANDLERS
     */

    public function ajax_add_future_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['amount']);
        $expense_name = sanitize_text_field($_POST['expense_name']);
        $description = sanitize_textarea_field($_POST['description']);
        $expected_date = sanitize_text_field($_POST['expected_date']);
        $category = sanitize_text_field($_POST['category']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_add_outcomes($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה להוסיף הוצאות לתקציב זה', 'budgex')));
        }

        if ($amount <= 0 || empty($expense_name) || empty($expected_date)) {
            wp_send_json_error(array('message' => __('נא למלא את כל השדות הנדרשים', 'budgex')));
        }

        $result = $this->database->add_future_expense($budget_id, $amount, $expense_name, $description, $expected_date, $category, $user_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('ההוצאה העתידית נוספה בהצלחה', 'budgex'),
                'expense_id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהוספת ההוצאה העתידית', 'budgex')));
        }
    }

    public function ajax_edit_future_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $expense_id = intval($_POST['expense_id']);
        $amount = floatval($_POST['amount']);
        $expense_name = sanitize_text_field($_POST['expense_name']);
        $description = sanitize_textarea_field($_POST['description']);
        $expected_date = sanitize_text_field($_POST['expected_date']);
        $category = sanitize_text_field($_POST['category']);
        $user_id = get_current_user_id();

        // Get the future expense to check budget permissions
        $expense = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT budget_id FROM {$this->database->wpdb->prefix}budgex_future_expenses WHERE id = %d",
                $expense_id
            )
        );

        if (!$expense || !$this->permissions->can_add_outcomes($expense->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך הוצאה זו', 'budgex')));
        }

        if ($amount <= 0 || empty($expense_name) || empty($expected_date)) {
            wp_send_json_error(array('message' => __('נא למלא את כל השדות הנדרשים', 'budgex')));
        }

        $result = $this->database->update_future_expense($expense_id, $amount, $expense_name, $description, $expected_date, $category);
        
        if ($result !== false) {
            wp_send_json_success(array('message' => __('ההוצאה העתידית עודכנה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בעדכון ההוצאה העתידית', 'budgex')));
        }
    }

    public function ajax_delete_future_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $expense_id = intval($_POST['expense_id']);
        $user_id = get_current_user_id();

        // Get the future expense to check budget permissions
        $expense = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT budget_id FROM {$this->database->wpdb->prefix}budgex_future_expenses WHERE id = %d",
                $expense_id
            )
        );

        if (!$expense || !$this->permissions->can_add_outcomes($expense->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה למחוק הוצאה זו', 'budgex')));
        }

        $result = $this->database->delete_future_expense($expense_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('ההוצאה העתידית נמחקה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה במחיקת ההוצאה העתידית', 'budgex')));
        }
    }

    public function ajax_confirm_future_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $expense_id = intval($_POST['expense_id']);
        $user_id = get_current_user_id();

        // Get the future expense to check budget permissions
        $expense = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT budget_id FROM {$this->database->wpdb->prefix}budgex_future_expenses WHERE id = %d",
                $expense_id
            )
        );

        if (!$expense || !$this->permissions->can_add_outcomes($expense->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לאשר הוצאה זו', 'budgex')));
        }

        $result = $this->database->confirm_future_expense($expense_id);
        
        if ($result) {
            // Get updated budget calculation
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($expense->budget_id);
            
            wp_send_json_success(array(
                'message' => __('ההוצאה אושרה והוספה לתקציב', 'budgex'),
                'remaining_budget' => $calculation['remaining'],
                'spent_amount' => $calculation['total_spent']
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה באישור ההוצאה', 'budgex')));
        }
    }

    public function ajax_get_future_expenses() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $from_date = sanitize_text_field($_POST['from_date'] ?? '');
        $to_date = sanitize_text_field($_POST['to_date'] ?? '');
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $expenses = $this->database->get_future_expenses($budget_id, $from_date, $to_date);
        
        wp_send_json_success(array('expenses' => $expenses));
    }

    /**
     * RECURRING EXPENSES AJAX HANDLERS
     */

    public function ajax_add_recurring_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $amount = floatval($_POST['amount']);
        $expense_name = sanitize_text_field($_POST['expense_name']);
        $description = sanitize_textarea_field($_POST['description']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date'] ?? '');
        $frequency = sanitize_text_field($_POST['frequency']);
        $frequency_interval = intval($_POST['frequency_interval'] ?? 1);
        $category = sanitize_text_field($_POST['category']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_add_outcomes($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה להוסיף הוצאות לתקציב זה', 'budgex')));
        }

        if ($amount <= 0 || empty($expense_name) || empty($start_date) || empty($frequency)) {
            wp_send_json_error(array('message' => __('נא למלא את כל השדות הנדרשים', 'budgex')));
        }

        $result = $this->database->add_recurring_expense($budget_id, $amount, $expense_name, $description, $start_date, $end_date, $frequency, $frequency_interval, $category, $user_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('ההוצאה החוזרת נוספה בהצלחה', 'budgex'),
                'expense_id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בהוספת ההוצאה החוזרת', 'budgex')));
        }
    }

    public function ajax_edit_recurring_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $expense_id = intval($_POST['expense_id']);
        $amount = floatval($_POST['amount']);
        $expense_name = sanitize_text_field($_POST['expense_name']);
        $description = sanitize_textarea_field($_POST['description']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date'] ?? '');
        $frequency = sanitize_text_field($_POST['frequency']);
        $frequency_interval = intval($_POST['frequency_interval'] ?? 1);
        $category = sanitize_text_field($_POST['category']);
        $user_id = get_current_user_id();

        // Get the recurring expense to check budget permissions
        $expense = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT budget_id FROM {$this->database->wpdb->prefix}budgex_recurring_expenses WHERE id = %d",
                $expense_id
            )
        );

        if (!$expense || !$this->permissions->can_add_outcomes($expense->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך הוצאה זו', 'budgex')));
        }

        if ($amount <= 0 || empty($expense_name) || empty($start_date) || empty($frequency)) {
            wp_send_json_error(array('message' => __('נא למלא את כל השדות הנדרשים', 'budgex')));
        }

        $result = $this->database->update_recurring_expense($expense_id, $amount, $expense_name, $description, $start_date, $end_date, $frequency, $frequency_interval, $category);
        
        if ($result !== false) {
            wp_send_json_success(array('message' => __('ההוצאה החוזרת עודכנה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בעדכון ההוצאה החוזרת', 'budgex')));
        }
    }

    public function ajax_delete_recurring_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $expense_id = intval($_POST['expense_id']);
        $user_id = get_current_user_id();

        // Get the recurring expense to check budget permissions
        $expense = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT budget_id FROM {$this->database->wpdb->prefix}budgex_recurring_expenses WHERE id = %d",
                $expense_id
            )
        );

        if (!$expense || !$this->permissions->can_add_outcomes($expense->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה למחוק הוצאה זו', 'budgex')));
        }

        $result = $this->database->delete_recurring_expense($expense_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('ההוצאה החוזרת נמחקה בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה במחיקת ההוצאה החוזרת', 'budgex')));
        }
    }

    public function ajax_toggle_recurring_expense() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $expense_id = intval($_POST['expense_id']);
        $is_active = intval($_POST['is_active']);
        $user_id = get_current_user_id();

        // Get the recurring expense to check budget permissions
        $expense = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT budget_id FROM {$this->database->wpdb->prefix}budgex_recurring_expenses WHERE id = %d",
                $expense_id
            )
        );

        if (!$expense || !$this->permissions->can_add_outcomes($expense->budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לשנות הוצאה זו', 'budgex')));
        }

        $result = $this->database->toggle_recurring_expense($expense_id, $is_active);
        
        if ($result !== false) {
            $status_text = $is_active ? __('פעיל', 'budgex') : __('לא פעיל', 'budgex');
            wp_send_json_success(array('message' => sprintf(__('ההוצאה החוזרת הוגדרה כ%s', 'budgex'), $status_text)));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בשינוי סטטוס ההוצאה', 'budgex')));
        }
    }

    public function ajax_get_recurring_expenses() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $active_only = isset($_POST['active_only']) ? intval($_POST['active_only']) : true;
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $expenses = $this->database->get_recurring_expenses($budget_id, $active_only);
        
        wp_send_json_success(array('expenses' => $expenses));
    }

    public function ajax_get_projected_balance() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $target_date = sanitize_text_field($_POST['target_date']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        if (empty($target_date)) {
            wp_send_json_error(array('message' => __('נא לבחור תאריך יעד', 'budgex')));
        }

        $projection = $this->database->get_projected_balance($budget_id, $target_date);
        
        wp_send_json_success(array('projection' => $projection));
    }

    // Enhanced Budget Page AJAX Handlers
    
    public function ajax_get_chart_data() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $chart_type = sanitize_text_field($_POST['chart_type']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $data = array();
        
        switch ($chart_type) {
            case 'monthly_breakdown':
                $data = $this->get_monthly_breakdown_chart_data($budget_id);
                break;
            case 'category_analysis':
                $data = $this->get_category_analysis_chart_data($budget_id);
                break;
            case 'spending_trends':
                $data = $this->get_spending_trends_chart_data($budget_id);
                break;
            case 'budget_progress':
                $data = $this->get_budget_progress_chart_data($budget_id);
                break;
            default:
                wp_send_json_error(array('message' => __('סוג גרף לא תקין', 'budgex')));
        }
        
        wp_send_json_success($data);
    }
    
    public function ajax_get_analysis_data() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $analysis_type = sanitize_text_field($_POST['analysis_type']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $data = array();
        
        switch ($analysis_type) {
            case 'spending_patterns':
                $data = $this->get_spending_patterns_analysis($budget_id);
                break;
            case 'budget_health':
                $data = $this->get_budget_health_analysis($budget_id);
                break;
            case 'predictions':
                $data = $this->get_budget_predictions($budget_id);
                break;
            case 'comparisons':
                $data = $this->get_budget_comparisons($budget_id);
                break;
            default:
                wp_send_json_error(array('message' => __('סוג ניתוח לא תקין', 'budgex')));
        }
        
        wp_send_json_success($data);
    }
    
    public function ajax_save_budget_settings() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $settings = $_POST['settings'];
        $user_id = get_current_user_id();

        if (!$this->permissions->can_edit_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך תקציב זה', 'budgex')));
        }

        // Validate and sanitize settings
        $validated_settings = array();
        if (isset($settings['budget_name'])) {
            $validated_settings['budget_name'] = sanitize_text_field($settings['budget_name']);
        }
        if (isset($settings['budget_description'])) {
            $validated_settings['budget_description'] = sanitize_textarea_field($settings['budget_description']);
        }
        if (isset($settings['auto_save'])) {
            $validated_settings['auto_save'] = (bool) $settings['auto_save'];
        }
        if (isset($settings['notifications_enabled'])) {
            $validated_settings['notifications_enabled'] = (bool) $settings['notifications_enabled'];
        }
        if (isset($settings['currency'])) {
            $validated_settings['currency'] = sanitize_text_field($settings['currency']);
        }
        if (isset($settings['budget_period'])) {
            $validated_settings['budget_period'] = sanitize_text_field($settings['budget_period']);
        }
        
        $result = $this->database->update_budget_settings($budget_id, $validated_settings);
        
        if ($result) {
            wp_send_json_success(array('message' => __('הגדרות נשמרו בהצלחה', 'budgex')));
        } else {
            wp_send_json_error(array('message' => __('שגיאה בשמירת ההגדרות', 'budgex')));
        }
    }
    
    public function ajax_bulk_delete_outcomes() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $outcome_ids = array_map('intval', $_POST['outcome_ids']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_edit_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך תקציב זה', 'budgex')));
        }

        if (empty($outcome_ids)) {
            wp_send_json_error(array('message' => __('לא נבחרו פריטים למחיקה', 'budgex')));
        }

        $deleted_count = 0;
        foreach ($outcome_ids as $outcome_id) {
            if ($this->database->delete_outcome($outcome_id, $budget_id)) {
                $deleted_count++;
            }
        }
        
        wp_send_json_success(array(
            'deleted_count' => $deleted_count,
            'message' => sprintf(__('נמחקו %d פריטים בהצלחה', 'budgex'), $deleted_count)
        ));
    }
    
    public function ajax_export_data() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $export_format = sanitize_text_field($_POST['export_format']);
        $date_range = sanitize_text_field($_POST['date_range']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $export_data = $this->prepare_export_data($budget_id, $date_range);
        
        switch ($export_format) {
            case 'csv':
                $file_url = $this->generate_csv_export($export_data, $budget_id);
                break;
            case 'excel':
                $file_url = $this->generate_excel_export($export_data, $budget_id);
                break;
            case 'pdf':
                $file_url = $this->generate_pdf_export($export_data, $budget_id);
                break;
            default:
                wp_send_json_error(array('message' => __('פורמט יצוא לא תקין', 'budgex')));
        }
        
        if ($file_url) {
            wp_send_json_success(array(
                'file_url' => $file_url,
                'message' => __('הקובץ נוצר בהצלחה', 'budgex')
            ));
        } else {
            wp_send_json_error(array('message' => __('שגיאה ביצירת הקובץ', 'budgex')));
        }
    }
    
    public function ajax_search_outcomes() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $search_term = sanitize_text_field($_POST['search_term']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $outcomes = $this->database->search_outcomes($budget_id, $search_term);
        
        wp_send_json_success(array('outcomes' => $outcomes));
    }
    
    public function ajax_filter_outcomes() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $filters = $_POST['filters'];
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        // Sanitize filters
        $sanitized_filters = array();
        if (isset($filters['category'])) {
            $sanitized_filters['category'] = sanitize_text_field($filters['category']);
        }
        if (isset($filters['date_from'])) {
            $sanitized_filters['date_from'] = sanitize_text_field($filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $sanitized_filters['date_to'] = sanitize_text_field($filters['date_to']);
        }
        if (isset($filters['amount_min'])) {
            $sanitized_filters['amount_min'] = floatval($filters['amount_min']);
        }
        if (isset($filters['amount_max'])) {
            $sanitized_filters['amount_max'] = floatval($filters['amount_max']);
        }
        if (isset($filters['type'])) {
            $sanitized_filters['type'] = sanitize_text_field($filters['type']);
        }

        $outcomes = $this->database->filter_outcomes($budget_id, $sanitized_filters);
        
        wp_send_json_success(array('outcomes' => $outcomes));
    }
    
    public function ajax_get_quick_stats() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        $stats = array(
            'total_spent_today' => $this->database->get_total_spent_today($budget_id),
            'total_spent_this_week' => $this->database->get_total_spent_this_week($budget_id),
            'total_spent_this_month' => $this->database->get_total_spent_this_month($budget_id),
            'remaining_budget' => $this->database->get_remaining_budget($budget_id),
            'average_daily_spending' => $this->database->get_average_daily_spending($budget_id),
            'top_categories' => $this->database->get_top_spending_categories($budget_id, 5),
            'recent_transactions' => $this->database->get_recent_outcomes($budget_id, 5),
            'budget_health_score' => $this->calculate_budget_health_score($budget_id)
        );
        
        wp_send_json_success($stats);
    }

    // Helper methods for chart data generation
    private function get_monthly_breakdown_chart_data($budget_id) {
        $monthly_data = $this->database->get_monthly_spending_breakdown($budget_id);
        return array(
            'labels' => array_column($monthly_data, 'month'),
            'datasets' => array(
                array(
                    'label' => __('הוצאות חודשיות', 'budgex'),
                    'data' => array_column($monthly_data, 'total_spent'),
                    'backgroundColor' => 'rgba(52, 152, 219, 0.8)',
                    'borderColor' => 'rgba(52, 152, 219, 1)',
                    'borderWidth' => 2
                )
            )
        );
    }
    
    private function get_category_analysis_chart_data($budget_id) {
        $category_data = $this->database->get_spending_by_category($budget_id);
        return array(
            'labels' => array_column($category_data, 'category'),
            'datasets' => array(
                array(
                    'data' => array_column($category_data, 'total'),
                    'backgroundColor' => array(
                        '#3498db', '#e74c3c', '#2ecc71', '#f39c12',
                        '#9b59b6', '#1abc9c', '#34495e', '#f1c40f'
                    )
                )
            )
        );
    }
    
    private function get_spending_trends_chart_data($budget_id) {
        $trends_data = $this->database->get_spending_trends($budget_id);
        return array(
            'labels' => array_column($trends_data, 'date'),
            'datasets' => array(
                array(
                    'label' => __('מגמת הוצאות', 'budgex'),
                    'data' => array_column($trends_data, 'cumulative_spent'),
                    'borderColor' => 'rgba(231, 76, 60, 1)',
                    'backgroundColor' => 'rgba(231, 76, 60, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                )
            )
        );
    }
    
    private function get_budget_progress_chart_data($budget_id) {
        $budget_info = $this->database->get_budget($budget_id);
        $spent = $this->database->get_total_spent($budget_id);
        $remaining = max(0, $budget_info['monthly_budget'] - $spent);
        
        return array(
            'labels' => array(__('נוצל', 'budgex'), __('נותר', 'budgex')),
            'datasets' => array(
                array(
                    'data' => array($spent, $remaining),
                    'backgroundColor' => array('#e74c3c', '#2ecc71')
                )
            )
        );
    }

    // Helper methods for analysis data
    private function get_spending_patterns_analysis($budget_id) {
        return array(
            'daily_average' => $this->database->get_average_daily_spending($budget_id),
            'peak_spending_days' => $this->database->get_peak_spending_days($budget_id),
            'spending_frequency' => $this->database->get_spending_frequency($budget_id),
            'seasonal_trends' => $this->database->get_seasonal_spending_trends($budget_id)
        );
    }
    
    private function get_budget_health_analysis($budget_id) {
        $health_score = $this->calculate_budget_health_score($budget_id);
        return array(
            'health_score' => $health_score,
            'risk_factors' => $this->identify_risk_factors($budget_id),
            'recommendations' => $this->generate_budget_recommendations($budget_id),
            'alerts' => $this->get_budget_alerts($budget_id)
        );
    }
    
    private function get_budget_predictions($budget_id) {
        return array(
            'projected_month_end' => $this->database->get_projected_month_end_balance($budget_id),
            'trend_analysis' => $this->database->get_spending_trend_analysis($budget_id),
            'category_forecasts' => $this->database->get_category_spending_forecasts($budget_id)
        );
    }
    
    private function get_budget_comparisons($budget_id) {
        return array(
            'previous_months' => $this->database->get_previous_months_comparison($budget_id),
            'category_comparison' => $this->database->get_category_month_comparison($budget_id),
            'efficiency_metrics' => $this->database->get_budget_efficiency_metrics($budget_id)
        );
    }

    // Helper methods for export functionality
    private function prepare_export_data($budget_id, $date_range) {
        $date_filter = $this->parse_date_range($date_range);
        return array(
            'budget_info' => $this->database->get_budget($budget_id),
            'outcomes' => $this->database->get_outcomes_for_export($budget_id, $date_filter),
            'summary' => $this->database->get_budget_summary($budget_id, $date_filter),
            'categories' => $this->database->get_category_breakdown($budget_id, $date_filter)
        );
    }
    
    private function generate_csv_export($data, $budget_id) {
        // Implementation for CSV export
        $upload_dir = wp_upload_dir();
        $filename = 'budget_' . $budget_id . '_' . date('Y-m-d') . '.csv';
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        $file = fopen($filepath, 'w');
        
        // Add CSV headers
        fputcsv($file, array('Date', 'Description', 'Category', 'Amount', 'Type'));
        
        // Add data rows
        foreach ($data['outcomes'] as $outcome) {
            fputcsv($file, array(
                $outcome['expense_date'],
                $outcome['expense_name'],
                $outcome['category'],
                $outcome['expense_amount'],
                $outcome['expense_type']
            ));
        }
        
        fclose($file);
        
        return $upload_dir['url'] . '/' . $filename;
    }
    
    private function generate_excel_export($data, $budget_id) {
        // Simplified Excel export - would need PhpSpreadsheet for full functionality
        return $this->generate_csv_export($data, $budget_id);
    }
    
    private function generate_pdf_export($data, $budget_id) {
        // PDF export would require a PDF library like TCPDF or mPDF
        // For now, return CSV as fallback
        return $this->generate_csv_export($data, $budget_id);
    }
    
    private function parse_date_range($date_range) {
        switch ($date_range) {
            case 'this_month':
                return array(
                    'start' => date('Y-m-01'),
                    'end' => date('Y-m-t')
                );
            case 'last_month':
                return array(
                    'start' => date('Y-m-01', strtotime('last month')),
                    'end' => date('Y-m-t', strtotime('last month'))
                );
            case 'last_3_months':
                return array(
                    'start' => date('Y-m-01', strtotime('-3 months')),
                    'end' => date('Y-m-t')
                );
            case 'this_year':
                return array(
                    'start' => date('Y-01-01'),
                    'end' => date('Y-12-31')
                );
            default:
                return array(
                    'start' => date('Y-m-01'),
                    'end' => date('Y-m-t')
                );
        }
    }
    
    private function calculate_budget_health_score($budget_id) {
        $budget_info = $this->database->get_budget($budget_id);
        $spent = $this->database->get_total_spent($budget_id);
        $days_in_month = date('t');
        $current_day = date('j');
        
        $expected_spent = ($budget_info['monthly_budget'] / $days_in_month) * $current_day;
        $spending_ratio = $spent / $expected_spent;
        
        if ($spending_ratio <= 0.8) {
            return 90 + (10 * (0.8 - $spending_ratio));
        } elseif ($spending_ratio <= 1.0) {
            return 70 + (20 * (1.0 - $spending_ratio));
        } elseif ($spending_ratio <= 1.2) {
            return 50 + (20 * (1.2 - $spending_ratio));
        } else {
            return max(0, 50 - (50 * ($spending_ratio - 1.2)));
        }
    }
    
    private function identify_risk_factors($budget_id) {
        $risks = array();
        $budget_info = $this->database->get_budget($budget_id);
        $spent = $this->database->get_total_spent($budget_id);
        
        if ($spent > $budget_info['monthly_budget'] * 0.8) {
            $risks[] = __('הוצאות גבוהות - נוצלו מעל 80% מהתקציב', 'budgex');
        }
        
        $recent_spending = $this->database->get_recent_spending_trend($budget_id, 7);
        if ($recent_spending > $budget_info['monthly_budget'] * 0.1) {
            $risks[] = __('מגמת הוצאות עולה בשבוע האחרון', 'budgex');
        }
        
        return $risks;
    }
    
    private function generate_budget_recommendations($budget_id) {
        $recommendations = array();
        $top_categories = $this->database->get_top_spending_categories($budget_id, 3);
        
        foreach ($top_categories as $category) {
            if ($category['percentage'] > 30) {
                $recommendations[] = sprintf(
                    __('שקול להפחית הוצאות בקטגוריה "%s" - מהווה %d%% מההוצאות', 'budgex'),
                    $category['category'],
                    $category['percentage']
                );
            }
        }
        
        return $recommendations;
    }
    
    private function get_budget_alerts($budget_id) {
        $alerts = array();
        $budget_info = $this->database->get_budget($budget_id);
        $spent = $this->database->get_total_spent($budget_id);
        
        if ($spent > $budget_info['monthly_budget']) {
            $alerts[] = array(
                'type' => 'error',
                'message' => __('חריגה מהתקציב החודשי!', 'budgex')
            );
        } elseif ($spent > $budget_info['monthly_budget'] * 0.9) {
            $alerts[] = array(
                'type' => 'warning',
                'message' => __('התקרבות לסיום התקציב החודשי', 'budgex')
            );
        }
        
        return $alerts;
    }

    /**
     * Log errors for debugging in production
     */
    private function log_error($message, $context = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Budgex Plugin Error: ' . $message . ' Context: ' . print_r($context, true));
        }
    }

    /**
     * AJAX handler for exporting selected outcomes
     */
    public function ajax_export_selected_outcomes() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $outcome_ids = array_map('intval', $_POST['outcome_ids']);
        $export_format = sanitize_text_field($_POST['export_format'] ?? 'csv');
        $user_id = get_current_user_id();

        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לצפות בתקציב זה', 'budgex')));
        }

        if (empty($outcome_ids)) {
            wp_send_json_error(array('message' => __('לא נבחרו פריטים לייצוא', 'budgex')));
        }

        try {
            $budget = $this->database->get_budget($budget_id);
            $outcomes = $this->database->get_outcomes_by_ids($outcome_ids);
            
            $filename = 'budgex_selected_outcomes_' . date('Y-m-d_H-i-s');
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['path'] . '/' . $filename;
            
            if ($export_format === 'csv') {
                $file_path .= '.csv';
                $output = fopen($file_path, 'w');
                
                // Add BOM for Hebrew support
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV headers
                fputcsv($output, array(
                    __('תאריך', 'budgex'),
                    __('תיאור', 'budgex'),
                    __('קטגוריה', 'budgex'),
                    __('סכום', 'budgex'),
                    __('מטבע', 'budgex')
                ));
                
                foreach ($outcomes as $outcome) {
                    fputcsv($output, array(
                        $outcome->date,
                        $outcome->description,
                        $outcome->category ?: '',
                        $outcome->amount,
                        $budget->currency
                    ));
                }
                
                fclose($output);
            } else {
                wp_send_json_error(array('message' => __('פורמט ייצוא לא נתמך', 'budgex')));
            }
            
            $file_url = $upload_dir['url'] . '/' . basename($file_path);
            
            wp_send_json_success(array(
                'file_url' => $file_url,
                'filename' => basename($file_path),
                'message' => __('הקובץ נוצר בהצלחה', 'budgex')
            ));
            
        } catch (Exception $e) {
            $this->log_error('Failed to export selected outcomes: ' . $e->getMessage(), array(
                'budget_id' => $budget_id,
                'outcome_ids' => $outcome_ids
            ));
            wp_send_json_error(array('message' => __('שגיאה בייצוא הקובץ', 'budgex')));
        }
    }

    /**
     * AJAX handler for updating category of selected outcomes
     */
    public function ajax_update_outcomes_category() {
        check_ajax_referer('budgex_public_nonce', 'nonce');
        
        $budget_id = intval($_POST['budget_id']);
        $outcome_ids = array_map('intval', $_POST['outcome_ids']);
        $category = sanitize_text_field($_POST['category']);
        $user_id = get_current_user_id();

        if (!$this->permissions->can_edit_budget($budget_id, $user_id)) {
            wp_send_json_error(array('message' => __('אין הרשאה לערוך תקציב זה', 'budgex')));
        }

        if (empty($outcome_ids)) {
            wp_send_json_error(array('message' => __('לא נבחרו פריטים לעדכון', 'budgex')));
        }

        if (empty($category)) {
            wp_send_json_error(array('message' => __('נא להכניס שם קטגוריה', 'budgex')));
        }

        try {
            $updated_count = 0;
            foreach ($outcome_ids as $outcome_id) {
                if ($this->database->update_outcome_category($outcome_id, $category)) {
                    $updated_count++;
                }
            }
            
            wp_send_json_success(array(
                'updated_count' => $updated_count,
                'message' => sprintf(__('עודכנו %d פריטים לקטגוריה "%s"', 'budgex'), $updated_count, $category)
            ));
            
        } catch (Exception $e) {
            $this->log_error('Failed to update outcomes category: ' . $e->getMessage(), array(
                'budget_id' => $budget_id,
                'outcome_ids' => $outcome_ids,
                'category' => $category
            ));
            wp_send_json_error(array('message' => __('שגיאה בעדכון הקטגוריה', 'budgex')));
        }
    }

    /**
     * Force enqueue enhanced assets when needed
     */
    private function force_enqueue_enhanced_assets() {
        // Enqueue enhanced CSS if not already done
        if (!wp_style_is('budgex-enhanced-budget', 'enqueued')) {
            wp_enqueue_style('budgex-enhanced-budget', BUDGEX_URL . 'public/css/budgex-enhanced-budget.css', array('budgex-public'), BUDGEX_VERSION);
        }
        
        // Enqueue Chart.js and enhanced JS if not already done
        if (!wp_script_is('chart-js', 'enqueued')) {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
        }
        
        if (!wp_script_is('budgex-enhanced-budget', 'enqueued')) {
            wp_enqueue_script('budgex-enhanced-budget', BUDGEX_URL . 'public/js/budgex-enhanced-budget.js', 
                ['jquery', 'budgex-public', 'chart-js'], BUDGEX_VERSION, true);
        }
    }
}
?>