<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Budgex_Admin {
    private $database;
    private $permissions;
    private $budgex;

    public function __construct() {
        $this->database = new Budgex_Database();
        $this->permissions = new Budgex_Permissions();
        $this->budgex = new Budgex();
        
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'handle_forms' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'Budgex - ניהול תקציב', 'budgex' ),
            __( 'Budgex', 'budgex' ),
            'read',
            'budgex',
            array( $this, 'display_admin_page' ),
            'dashicons-chart-line',
            6
        );

        add_submenu_page(
            'budgex',
            __( 'תקציב חדש', 'budgex' ),
            __( 'תקציב חדש', 'budgex' ),
            'read',
            'budgex-new',
            array( $this, 'display_new_budget_page' )
        );

        add_submenu_page(
            'budgex',
            __( 'צפייה בתקציב', 'budgex' ),
            __( 'צפייה בתקציב', 'budgex' ),
            'read',
            'budgex-view',
            array( $this, 'display_budget_view_page' )
        );

        // Add navigation fix tool for administrators
        if (current_user_can('manage_options')) {
            add_submenu_page(
                'budgex',
                __( 'Fix Navigation', 'budgex' ),
                __( 'Fix Navigation', 'budgex' ),
                'manage_options',
                'budgex-fix-navigation',
                array( $this, 'display_fix_navigation_page' )
            );
        }
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'budgex') !== false) {
            wp_enqueue_style( 'budgex-admin', BUDGEX_URL . 'admin/css/budgex-admin.css', array(), BUDGEX_VERSION );
            wp_enqueue_script( 'budgex-admin', BUDGEX_URL . 'admin/js/budgex-admin.js', array('jquery'), BUDGEX_VERSION, true );
            
            wp_localize_script( 'budgex-admin', 'budgex_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'budgex_nonce' ),
                'strings' => array(
                    'confirm_delete' => __( 'האם אתה בטוח שברצונך למחוק?', 'budgex' ),
                    'loading' => __( 'טוען...', 'budgex' ),
                    'error' => __( 'שגיאה', 'budgex' ),
                    'success' => __( 'הצלחה', 'budgex' )
                )
            ));
        }
    }

    public function display_admin_page() {
        $user_id = get_current_user_id();
        $budgets = $this->database->get_user_budgets($user_id);
        include_once plugin_dir_path( __FILE__ ) . 'partials/budgex-admin-display.php';
    }

    public function display_new_budget_page() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/budgex-budget-form.php';
    }

    public function display_budget_view_page() {
        $budget_id = isset($_GET['budget_id']) ? intval($_GET['budget_id']) : 0;
        $user_id = get_current_user_id();
        
        if (!$budget_id) {
            echo '<div class="error"><p>' . __('לא נבחר תקציב', 'budgex') . '</p></div>';
            return;
        }
        
        if (!$this->permissions->can_view_budget($budget_id, $user_id)) {
            echo '<div class="error"><p>' . __('אין הרשאה לצפות בתקציב זה', 'budgex') . '</p></div>';
            return;
        }
        
        $budget_summary = $this->budgex->get_budget_summary($budget_id, $user_id);
        include_once plugin_dir_path( __FILE__ ) . 'partials/budgex-budget-view.php';
    }

    public function handle_forms() {
        // Handle new budget creation
        if ( isset( $_POST['budgex_create_budget'] ) && wp_verify_nonce( $_POST['budgex_nonce'], 'budgex_create_budget' ) ) {
            $this->handle_create_budget();
        }

        // Handle budget update
        if ( isset( $_POST['budgex_update_budget'] ) && wp_verify_nonce( $_POST['budgex_nonce'], 'budgex_update_budget' ) ) {
            $this->handle_update_budget();
        }

        // Handle outcome addition
        if ( isset( $_POST['budgex_add_outcome'] ) && wp_verify_nonce( $_POST['budgex_nonce'], 'budgex_add_outcome' ) ) {
            $this->handle_add_outcome();
        }

        // Handle additional budget
        if ( isset( $_POST['budgex_add_additional_budget'] ) && wp_verify_nonce( $_POST['budgex_nonce'], 'budgex_add_additional_budget' ) ) {
            $this->handle_add_additional_budget();
        }

        // Handle user invitation
        if ( isset( $_POST['budgex_invite_user'] ) && wp_verify_nonce( $_POST['budgex_nonce'], 'budgex_invite_user' ) ) {
            $this->handle_invite_user();
        }
    }

    private function handle_create_budget() {
        $user_id = get_current_user_id();
        $budget_name = sanitize_text_field( $_POST['budget_name'] );
        $monthly_budget = floatval( $_POST['monthly_budget'] );
        $currency = sanitize_text_field( $_POST['currency'] );
        $start_date = sanitize_text_field( $_POST['start_date'] );

        if ( empty( $budget_name ) || $monthly_budget <= 0 || empty( $start_date ) ) {
            add_settings_error( 'budgex', 'invalid_data', __( 'נא למלא את כל השדות הנדרשים', 'budgex' ), 'error' );
            return;
        }

        $result = $this->budgex->create_budget( $user_id, $budget_name, $monthly_budget, $currency, $start_date );
        
        if ( $result['success'] ) {
            add_settings_error( 'budgex', 'budget_created', $result['message'], 'success' );
            wp_redirect( admin_url( 'admin.php?page=budgex-view&budget_id=' . $result['budget_id'] ) );
            exit;
        } else {
            add_settings_error( 'budgex', 'budget_error', $result['message'], 'error' );
        }
    }

    private function handle_add_outcome() {
        $budget_id = intval( $_POST['budget_id'] );
        $amount = floatval( $_POST['outcome_amount'] );
        $description = sanitize_textarea_field( $_POST['outcome_description'] );
        $outcome_name = sanitize_text_field( $_POST['outcome_name'] );
        $outcome_date = sanitize_text_field( $_POST['outcome_date'] );
        $user_id = get_current_user_id();

        if ( !$this->permissions->can_add_outcomes( $budget_id, $user_id ) ) {
            add_settings_error( 'budgex', 'no_permission', __( 'אין הרשאה להוסיף הוצאות לתקציב זה', 'budgex' ), 'error' );
            return;
        }

        if ( $amount <= 0 || empty( $description ) || empty( $outcome_name ) ) {
            add_settings_error( 'budgex', 'invalid_outcome', __( 'נא למלא את כל שדות ההוצאה', 'budgex' ), 'error' );
            return;
        }

        $result = $this->database->add_outcome( $budget_id, $amount, $description, $outcome_name, $outcome_date );
        
        if ( $result ) {
            add_settings_error( 'budgex', 'outcome_added', __( 'ההוצאה נוספה בהצלחה', 'budgex' ), 'success' );
        } else {
            add_settings_error( 'budgex', 'outcome_error', __( 'שגיאה בהוספת ההוצאה', 'budgex' ), 'error' );
        }
    }

    private function handle_add_additional_budget() {
        $budget_id = intval( $_POST['budget_id'] );
        $amount = floatval( $_POST['additional_amount'] );
        $user_id = get_current_user_id();

        if ( !$this->permissions->can_edit_budget( $budget_id, $user_id ) ) {
            add_settings_error( 'budgex', 'no_permission', __( 'אין הרשאה לערוך תקציב זה', 'budgex' ), 'error' );
            return;
        }

        if ( $amount <= 0 ) {
            add_settings_error( 'budgex', 'invalid_amount', __( 'נא להזין סכום תקף', 'budgex' ), 'error' );
            return;
        }

        $result = $this->database->add_additional_budget( $budget_id, $amount );
        
        if ( $result ) {
            add_settings_error( 'budgex', 'budget_added', __( 'התקציב הנוסף נוסף בהצלחה', 'budgex' ), 'success' );
        } else {
            add_settings_error( 'budgex', 'budget_error', __( 'שגיאה בהוספת התקציב הנוסף', 'budgex' ), 'error' );
        }
    }

    private function handle_invite_user() {
        $budget_id = intval( $_POST['budget_id'] );
        $user_email = sanitize_email( $_POST['invite_email'] );
        $role = sanitize_text_field( $_POST['invite_role'] );
        $user_id = get_current_user_id();

        if ( !$this->permissions->can_invite_users( $budget_id, $user_id ) ) {
            add_settings_error( 'budgex', 'no_permission', __( 'אין הרשאה להזמין משתמשים לתקציב זה', 'budgex' ), 'error' );
            return;
        }

        $invited_user = get_user_by( 'email', $user_email );
        if ( !$invited_user ) {
            add_settings_error( 'budgex', 'user_not_found', __( 'משתמש לא נמצא', 'budgex' ), 'error' );
            return;
        }

        $result = $this->database->create_invitation( $budget_id, $user_id, $invited_user->ID, $role );
        
        if ( $result ) {
            add_settings_error( 'budgex', 'invitation_sent', __( 'ההזמנה נשלחה בהצלחה', 'budgex' ), 'success' );
        } else {
            add_settings_error( 'budgex', 'invitation_error', __( 'שגיאה בשליחת ההזמנה', 'budgex' ), 'error' );
        }
    }

    public function show_admin_notices() {
        $screen = get_current_screen();
        if ( strpos( $screen->id, 'budgex' ) !== false ) {
            settings_errors( 'budgex' );
        }
    }

    public function display_fix_navigation_page() {
        include_once BUDGEX_DIR . 'admin/fix-navigation.php';
    }
}
?>