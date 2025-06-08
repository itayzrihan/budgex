<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Budgex_Database {
    private $wpdb;
    private $budgets_table;
    private $outcomes_table;
    private $invitations_table;
    private $budget_shares_table;
    private $budget_adjustments_table;
    private $future_expenses_table;
    private $recurring_expenses_table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->budgets_table = $wpdb->prefix . 'budgex_budgets';
        $this->outcomes_table = $wpdb->prefix . 'budgex_outcomes';
        $this->invitations_table = $wpdb->prefix . 'budgex_invitations';
        $this->budget_shares_table = $wpdb->prefix . 'budgex_budget_shares';
        $this->budget_adjustments_table = $wpdb->prefix . 'budgex_budget_adjustments';
        $this->future_expenses_table = $wpdb->prefix . 'budgex_future_expenses';
        $this->recurring_expenses_table = $wpdb->prefix . 'budgex_recurring_expenses';
    }

    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();

        // Budgets table
        $sql_budgets = "CREATE TABLE IF NOT EXISTS {$this->budgets_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            budget_name varchar(255) NOT NULL,
            monthly_budget decimal(15,2) NOT NULL,
            currency varchar(3) NOT NULL DEFAULT 'ILS',
            start_date date NOT NULL,
            additional_budget decimal(15,2) DEFAULT 0,
            is_public tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        // Outcomes table
        $sql_outcomes = "CREATE TABLE IF NOT EXISTS {$this->outcomes_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            budget_id mediumint(9) NOT NULL,
            amount decimal(15,2) NOT NULL,
            description text NOT NULL,
            outcome_name varchar(255) NOT NULL,
            outcome_date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY budget_id (budget_id),
            FOREIGN KEY (budget_id) REFERENCES {$this->budgets_table}(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Invitations table
        $sql_invitations = "CREATE TABLE IF NOT EXISTS {$this->invitations_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            budget_id mediumint(9) NOT NULL,
            inviter_id bigint(20) NOT NULL,
            invited_user_id bigint(20) NOT NULL,
            role varchar(20) NOT NULL DEFAULT 'viewer',
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY budget_id (budget_id),
            KEY invited_user_id (invited_user_id),
            FOREIGN KEY (budget_id) REFERENCES {$this->budgets_table}(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Budget shares table
        $sql_shares = "CREATE TABLE IF NOT EXISTS {$this->budget_shares_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            budget_id mediumint(9) NOT NULL,
            user_id bigint(20) NOT NULL,
            role varchar(20) NOT NULL DEFAULT 'viewer',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY budget_user (budget_id, user_id),
            KEY budget_id (budget_id),
            KEY user_id (user_id),
            FOREIGN KEY (budget_id) REFERENCES {$this->budgets_table}(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Budget adjustments table for tracking monthly budget changes
        $sql_adjustments = "CREATE TABLE IF NOT EXISTS {$this->budget_adjustments_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            budget_id mediumint(9) NOT NULL,
            new_monthly_amount decimal(15,2) NOT NULL,
            effective_date date NOT NULL,
            reason varchar(500) DEFAULT '',
            created_by bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY budget_id (budget_id),
            KEY effective_date (effective_date),
            FOREIGN KEY (budget_id) REFERENCES {$this->budgets_table}(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Future expenses table for one-time future expenses
        $sql_future_expenses = "CREATE TABLE IF NOT EXISTS {$this->future_expenses_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            budget_id mediumint(9) NOT NULL,
            amount decimal(15,2) NOT NULL,
            expense_name varchar(255) NOT NULL,
            description text,
            expected_date date NOT NULL,
            category varchar(100) DEFAULT '',
            is_confirmed tinyint(1) DEFAULT 0,
            created_by bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY budget_id (budget_id),
            KEY expected_date (expected_date),
            KEY is_confirmed (is_confirmed),
            FOREIGN KEY (budget_id) REFERENCES {$this->budgets_table}(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Recurring expenses table for repeated expenses
        $sql_recurring_expenses = "CREATE TABLE IF NOT EXISTS {$this->recurring_expenses_table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            budget_id mediumint(9) NOT NULL,
            amount decimal(15,2) NOT NULL,
            expense_name varchar(255) NOT NULL,
            description text,
            start_date date NOT NULL,
            end_date date DEFAULT NULL,
            frequency varchar(20) NOT NULL DEFAULT 'monthly',
            frequency_interval int DEFAULT 1,
            category varchar(100) DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            next_occurrence date NOT NULL,
            last_generated date DEFAULT NULL,
            created_by bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY budget_id (budget_id),
            KEY next_occurrence (next_occurrence),
            KEY is_active (is_active),
            KEY frequency (frequency),
            FOREIGN KEY (budget_id) REFERENCES {$this->budgets_table}(id) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_budgets);
        dbDelta($sql_outcomes);
        dbDelta($sql_invitations);
        dbDelta($sql_shares);
        dbDelta($sql_adjustments);
        dbDelta($sql_future_expenses);
        dbDelta($sql_recurring_expenses);
    }

    public function add_budget($user_id, $budget_name, $monthly_budget, $currency, $start_date) {
        $result = $this->wpdb->insert(
            $this->budgets_table,
            array(
                'user_id' => $user_id,
                'budget_name' => sanitize_text_field($budget_name),
                'monthly_budget' => floatval($monthly_budget),
                'currency' => sanitize_text_field($currency),
                'start_date' => sanitize_text_field($start_date),
            ),
            array('%d', '%s', '%f', '%s', '%s')
        );
        
        if ($result !== false) {
            return $this->wpdb->insert_id;
        }
        
        return false;
    }

    public function get_user_budgets($user_id) {
        $owned_budgets = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->budgets_table} WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            )
        );

        $shared_budgets = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT b.*, s.role as user_role FROM {$this->budgets_table} b 
                 INNER JOIN {$this->budget_shares_table} s ON b.id = s.budget_id 
                 WHERE s.user_id = %d ORDER BY b.created_at DESC",
                $user_id
            )
        );

        return array_merge($owned_budgets, $shared_budgets);
    }

    public function get_budget($budget_id, $user_id = null) {
        $sql = "SELECT * FROM {$this->budgets_table} WHERE id = %d";
        $params = array($budget_id);

        if ($user_id) {
            $sql .= " AND (user_id = %d OR id IN (SELECT budget_id FROM {$this->budget_shares_table} WHERE user_id = %d))";
            $params[] = $user_id;
            $params[] = $user_id;
        }

        return $this->wpdb->get_row($this->wpdb->prepare($sql, $params));
    }

    public function add_outcome($budget_id, $amount, $description, $outcome_name, $outcome_date = null) {
        if (!$outcome_date) {
            $outcome_date = current_time('Y-m-d');
        }

        return $this->wpdb->insert(
            $this->outcomes_table,
            array(
                'budget_id' => intval($budget_id),
                'amount' => floatval($amount),
                'description' => sanitize_textarea_field($description),
                'outcome_name' => sanitize_text_field($outcome_name),
                'outcome_date' => sanitize_text_field($outcome_date),
            ),
            array('%d', '%f', '%s', '%s', '%s')
        );
    }

    public function get_budget_outcomes($budget_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->outcomes_table} WHERE budget_id = %d ORDER BY outcome_date DESC",
                $budget_id
            )
        );
    }

    public function add_budget_share($budget_id, $user_id, $role = 'viewer') {
        return $this->wpdb->insert(
            $this->budget_shares_table,
            array(
                'budget_id' => intval($budget_id),
                'user_id' => intval($user_id),
                'role' => sanitize_text_field($role),
            ),
            array('%d', '%d', '%s')
        );
    }

    public function create_invitation($budget_id, $inviter_id, $invited_user_id, $role = 'viewer') {
        return $this->wpdb->insert(
            $this->invitations_table,
            array(
                'budget_id' => intval($budget_id),
                'inviter_id' => intval($inviter_id),
                'invited_user_id' => intval($invited_user_id),
                'role' => sanitize_text_field($role),
                'status' => 'pending',
            ),
            array('%d', '%d', '%d', '%s', '%s')
        );
    }

    public function accept_invitation($invitation_id) {
        $invitation = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->invitations_table} WHERE id = %d",
                $invitation_id
            )
        );

        if ($invitation) {
            $this->add_budget_share($invitation->budget_id, $invitation->invited_user_id, $invitation->role);
            
            return $this->wpdb->update(
                $this->invitations_table,
                array('status' => 'accepted'),
                array('id' => $invitation_id),
                array('%s'),
                array('%d')
            );
        }
        return false;
    }

    public function update_budget($budget_id, $data) {
        $allowed_fields = array('budget_name', 'monthly_budget', 'currency', 'start_date', 'is_public');
        $update_data = array();
        $formats = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $update_data[$key] = $value;
                $formats[] = is_numeric($value) ? '%f' : '%s';
            }
        }

        if (!empty($update_data)) {
            return $this->wpdb->update(
                $this->budgets_table,
                $update_data,
                array('id' => $budget_id),
                $formats,
                array('%d')
            );
        }
        return false;
    }

    public function delete_budget($budget_id) {
        return $this->wpdb->delete(
            $this->budgets_table,
            array('id' => $budget_id),
            array('%d')
        );
    }

    public function delete_outcome($outcome_id, $budget_id = null) {
        if ($budget_id) {
            // Verify outcome belongs to budget
            $outcome = $this->wpdb->get_row($this->wpdb->prepare(
                "SELECT * FROM {$this->outcomes_table} WHERE id = %d AND budget_id = %d",
                $outcome_id, $budget_id
            ));
            
            if (!$outcome) {
                return false;
            }
        }
        
        return $this->wpdb->delete(
            $this->outcomes_table,
            array('id' => $outcome_id),
            array('%d')
        );
    }

    public function get_total_outcomes($budget_id) {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT SUM(amount) FROM {$this->outcomes_table} WHERE budget_id = %d",
                $budget_id
            )
        ) ?: 0;
    }

    public function update_outcome($outcome_id, $amount, $description, $outcome_name, $outcome_date) {
        return $this->wpdb->update(
            $this->outcomes_table,
            array(
                'amount' => $amount,
                'description' => $description,
                'outcome_name' => $outcome_name,
                'outcome_date' => $outcome_date
            ),
            array('id' => $outcome_id),
            array('%f', '%s', '%s', '%s'),
            array('%d')
        );
    }

    public function get_outcome($outcome_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->outcomes_table} WHERE id = %d",
                $outcome_id
            ),
            ARRAY_A
        );
    }

    public function get_invitation($invitation_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->invitations_table} WHERE id = %d",
                $invitation_id
            ),
            ARRAY_A
        );
    }

    public function get_user_invitations($user_id, $status = 'pending') {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT i.*, b.budget_name 
                 FROM {$this->invitations_table} i 
                 JOIN {$this->budgets_table} b ON i.budget_id = b.id 
                 WHERE i.invited_user_id = %d AND i.status = %s",
                $user_id, $status
            ),
            ARRAY_A
        );
    }

    public function get_budget_permissions($budget_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT s.*, u.display_name, u.user_email 
                 FROM {$this->budget_shares_table} s 
                 JOIN {$this->wpdb->users} u ON s.user_id = u.ID 
                 WHERE s.budget_id = %d",
                $budget_id
            ),
            ARRAY_A
        );
    }

    public function get_budget_invitations($budget_id, $status = 'pending') {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT i.*, u.display_name, u.user_email 
                 FROM {$this->invitations_table} i 
                 JOIN {$this->wpdb->users} u ON i.invited_user_id = u.ID 
                 WHERE i.budget_id = %d AND i.status = %s",
                $budget_id, $status
            ),
            ARRAY_A
        );
    }

    /**
     * Update budget details (name and description)
     */
    public function update_budget_details($budget_id, $budget_name, $budget_description = '') {
        return $this->wpdb->update(
            $this->budgets_table,
            array(
                'budget_name' => sanitize_text_field($budget_name),
                'description' => sanitize_textarea_field($budget_description)
            ),
            array('id' => intval($budget_id)),
            array('%s', '%s'),
            array('%d')
        );
    }

    /**
     * Change budget start date
     */
    public function change_start_date($budget_id, $new_start_date) {
        return $this->wpdb->update(
            $this->budgets_table,
            array('start_date' => sanitize_text_field($new_start_date)),
            array('id' => intval($budget_id)),
            array('%s'),
            array('%d')
        );
    }

    /**
     * Update user role in budget
     */
    public function update_user_role($budget_id, $user_id, $new_role) {
        return $this->wpdb->update(
            $this->budget_shares_table,
            array('role' => sanitize_text_field($new_role)),
            array(
                'budget_id' => intval($budget_id),
                'user_id' => intval($user_id)
            ),
            array('%s'),
            array('%d', '%d')
        );
    }

    /**
     * Remove user from budget
     */
    public function remove_user_from_budget($budget_id, $user_id) {
        return $this->wpdb->delete(
            $this->budget_shares_table,
            array(
                'budget_id' => intval($budget_id),
                'user_id' => intval($user_id)
            ),
            array('%d', '%d')
        );
    }

    /**
     * Resend invitation (update timestamp)
     */
    public function resend_invitation($invitation_id) {
        return $this->wpdb->update(
            $this->invitations_table,
            array('created_at' => current_time('mysql')),
            array('id' => intval($invitation_id)),
            array('%s'),
            array('%d')
        );
    }

    /**
     * Cancel invitation
     */
    public function cancel_invitation($invitation_id) {
        return $this->wpdb->delete(
            $this->invitations_table,
            array('id' => intval($invitation_id)),
            array('%d')
        );
    }

    /**
     * Create budget (wrapper method for frontend)
     */
    public function create_budget($user_id, $budget_name, $monthly_budget, $currency, $start_date) {
        return $this->add_budget($user_id, $budget_name, $monthly_budget, $currency, $start_date);
    }

    /**
     * Get outcomes for a budget (alias for get_budget_outcomes)
     */
    public function get_outcomes($budget_id) {
        return $this->get_budget_outcomes($budget_id);
    }

    /**
     * Get pending invitations for a user
     */
    public function get_pending_invitations($user_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT i.*, b.budget_name, u.display_name as inviter_name
                 FROM {$this->invitations_table} i 
                 JOIN {$this->budgets_table} b ON i.budget_id = b.id 
                 JOIN {$this->wpdb->users} u ON i.inviter_id = u.ID
                 WHERE i.invited_user_id = %d AND i.status = 'pending'
                 ORDER BY i.created_at DESC",
                $user_id
            )
        );
    }

    /**
     * Get shared budgets for a user
     */
    public function get_shared_budgets($user_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT b.*, s.role as user_role 
                 FROM {$this->budgets_table} b 
                 INNER JOIN {$this->budget_shares_table} s ON b.id = s.budget_id 
                 WHERE s.user_id = %d 
                 ORDER BY b.created_at DESC",
                $user_id
            )
        );
    }

    /**
     * Add additional budget amount with description support
     */
    public function add_additional_budget($budget_id, $amount, $description = '') {
        // For now, we'll just add to the additional_budget field
        // In the future, we might want to track these additions separately
        return $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->budgets_table} 
                 SET additional_budget = additional_budget + %f 
                 WHERE id = %d",
                floatval($amount),
                intval($budget_id)
            )
        );
    }

    /**
     * Add budget adjustment (monthly budget increase)
     */
    public function add_budget_adjustment($budget_id, $new_monthly_amount, $effective_date, $reason = '', $created_by = null) {
        if (!$created_by) {
            $created_by = get_current_user_id();
        }

        return $this->wpdb->insert(
            $this->budget_adjustments_table,
            array(
                'budget_id' => intval($budget_id),
                'new_monthly_amount' => floatval($new_monthly_amount),
                'effective_date' => sanitize_text_field($effective_date),
                'reason' => sanitize_text_field($reason),
                'created_by' => intval($created_by)
            ),
            array('%d', '%f', '%s', '%s', '%d')
        );
    }

    /**
     * Get budget adjustments for a specific budget
     */
    public function get_budget_adjustments($budget_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT ba.*, u.display_name as created_by_name
                 FROM {$this->budget_adjustments_table} ba
                 LEFT JOIN {$this->wpdb->users} u ON ba.created_by = u.ID
                 WHERE ba.budget_id = %d
                 ORDER BY ba.effective_date DESC",
                $budget_id
            ),
            ARRAY_A
        );
    }

    /**
     * Get effective monthly budget amount for a specific date
     */
    public function get_monthly_budget_for_date($budget_id, $date) {
        $budget = $this->get_budget($budget_id);
        if (!$budget) {
            return 0;
        }

        // Get the most recent adjustment before or on the given date
        $adjustment = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT new_monthly_amount 
                 FROM {$this->budget_adjustments_table} 
                 WHERE budget_id = %d AND effective_date <= %s 
                 ORDER BY effective_date DESC 
                 LIMIT 1",
                $budget_id,
                $date
            ),
            ARRAY_A
        );

        // Return adjusted amount if exists, otherwise return original budget
        return $adjustment ? floatval($adjustment['new_monthly_amount']) : floatval($budget->monthly_budget);
    }

    /**
     * FUTURE EXPENSES METHODS
     */

    /**
     * Add future expense
     */
    public function add_future_expense($budget_id, $amount, $expense_name, $description, $expected_date, $category = '', $created_by = null) {
        if (!$created_by) {
            $created_by = get_current_user_id();
        }

        return $this->wpdb->insert(
            $this->future_expenses_table,
            array(
                'budget_id' => intval($budget_id),
                'amount' => floatval($amount),
                'expense_name' => sanitize_text_field($expense_name),
                'description' => sanitize_textarea_field($description),
                'expected_date' => sanitize_text_field($expected_date),
                'category' => sanitize_text_field($category),
                'created_by' => intval($created_by)
            ),
            array('%d', '%f', '%s', '%s', '%s', '%s', '%d')
        );
    }

    /**
     * Get future expenses for a budget
     */
    public function get_future_expenses($budget_id, $from_date = null, $to_date = null) {
        $sql = "SELECT fe.*, u.display_name as created_by_name
                FROM {$this->future_expenses_table} fe
                LEFT JOIN {$this->wpdb->users} u ON fe.created_by = u.ID
                WHERE fe.budget_id = %d";
        
        $params = array($budget_id);
        
        if ($from_date) {
            $sql .= " AND fe.expected_date >= %s";
            $params[] = $from_date;
        }
        
        if ($to_date) {
            $sql .= " AND fe.expected_date <= %s";
            $params[] = $to_date;
        }
        
        $sql .= " ORDER BY fe.expected_date ASC";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $params),
            ARRAY_A
        );
    }

    /**
     * Update future expense
     */
    public function update_future_expense($expense_id, $amount, $expense_name, $description, $expected_date, $category = '') {
        return $this->wpdb->update(
            $this->future_expenses_table,
            array(
                'amount' => floatval($amount),
                'expense_name' => sanitize_text_field($expense_name),
                'description' => sanitize_textarea_field($description),
                'expected_date' => sanitize_text_field($expected_date),
                'category' => sanitize_text_field($category)
            ),
            array('id' => intval($expense_id)),
            array('%f', '%s', '%s', '%s', '%s'),
            array('%d')
        );
    }

    /**
     * Delete future expense
     */
    public function delete_future_expense($expense_id) {
        return $this->wpdb->delete(
            $this->future_expenses_table,
            array('id' => intval($expense_id)),
            array('%d')
        );
    }

    /**
     * Confirm future expense (convert to actual outcome)
     */
    public function confirm_future_expense($expense_id) {
        $expense = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->future_expenses_table} WHERE id = %d",
                $expense_id
            ),
            ARRAY_A
        );

        if (!$expense) {
            return false;
        }

        // Add as actual outcome
        $outcome_id = $this->add_outcome(
            $expense['budget_id'],
            $expense['amount'],
            $expense['description'],
            $expense['expense_name'],
            $expense['expected_date']
        );

        if ($outcome_id) {
            // Mark as confirmed
            $this->wpdb->update(
                $this->future_expenses_table,
                array('is_confirmed' => 1),
                array('id' => $expense_id),
                array('%d'),
                array('%d')
            );
            return $outcome_id;
        }

        return false;
    }

    /**
     * RECURRING EXPENSES METHODS
     */

    /**
     * Add recurring expense
     */
    public function add_recurring_expense($budget_id, $amount, $expense_name, $description, $start_date, $end_date, $frequency, $frequency_interval = 1, $category = '', $created_by = null) {
        if (!$created_by) {
            $created_by = get_current_user_id();
        }

        // Calculate next occurrence
        $next_occurrence = $this->calculate_next_occurrence($start_date, $frequency, $frequency_interval);

        return $this->wpdb->insert(
            $this->recurring_expenses_table,
            array(
                'budget_id' => intval($budget_id),
                'amount' => floatval($amount),
                'expense_name' => sanitize_text_field($expense_name),
                'description' => sanitize_textarea_field($description),
                'start_date' => sanitize_text_field($start_date),
                'end_date' => $end_date ? sanitize_text_field($end_date) : null,
                'frequency' => sanitize_text_field($frequency),
                'frequency_interval' => intval($frequency_interval),
                'category' => sanitize_text_field($category),
                'next_occurrence' => $next_occurrence,
                'created_by' => intval($created_by)
            ),
            array('%d', '%f', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d')
        );
    }

    /**
     * Get recurring expenses for a budget
     */
    public function get_recurring_expenses($budget_id, $active_only = true) {
        $sql = "SELECT re.*, u.display_name as created_by_name
                FROM {$this->recurring_expenses_table} re
                LEFT JOIN {$this->wpdb->users} u ON re.created_by = u.ID
                WHERE re.budget_id = %d";
        
        $params = array($budget_id);
        
        if ($active_only) {
            $sql .= " AND re.is_active = 1";
        }
        
        $sql .= " ORDER BY re.next_occurrence ASC";
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $params),
            ARRAY_A
        );
    }

    /**
     * Update recurring expense
     */
    public function update_recurring_expense($expense_id, $amount, $expense_name, $description, $start_date, $end_date, $frequency, $frequency_interval = 1, $category = '') {
        // Calculate new next occurrence
        $next_occurrence = $this->calculate_next_occurrence($start_date, $frequency, $frequency_interval);

        return $this->wpdb->update(
            $this->recurring_expenses_table,
            array(
                'amount' => floatval($amount),
                'expense_name' => sanitize_text_field($expense_name),
                'description' => sanitize_textarea_field($description),
                'start_date' => sanitize_text_field($start_date),
                'end_date' => $end_date ? sanitize_text_field($end_date) : null,
                'frequency' => sanitize_text_field($frequency),
                'frequency_interval' => intval($frequency_interval),
                'category' => sanitize_text_field($category),
                'next_occurrence' => $next_occurrence
            ),
            array('id' => intval($expense_id)),
            array('%f', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s'),
            array('%d')
        );
    }

    /**
     * Delete recurring expense
     */
    public function delete_recurring_expense($expense_id) {
        return $this->wpdb->delete(
            $this->recurring_expenses_table,
            array('id' => intval($expense_id)),
            array('%d')
        );
    }

    /**
     * Toggle recurring expense active status
     */
    public function toggle_recurring_expense($expense_id, $is_active) {
        return $this->wpdb->update(
            $this->recurring_expenses_table,
            array('is_active' => $is_active ? 1 : 0),
            array('id' => intval($expense_id)),
            array('%d'),
            array('%d')
        );
    }

    /**
     * Generate future occurrences for recurring expenses
     */
    public function get_recurring_expense_occurrences($budget_id, $from_date, $to_date) {
        $recurring_expenses = $this->get_recurring_expenses($budget_id, true);
        $occurrences = array();

        foreach ($recurring_expenses as $expense) {
            $current_date = new DateTime($expense['start_date']);
            $end_date = $expense['end_date'] ? new DateTime($expense['end_date']) : new DateTime($to_date);
            $to_date_obj = new DateTime($to_date);
            $from_date_obj = new DateTime($from_date);

            // Generate occurrences within the date range
            while ($current_date <= $end_date && $current_date <= $to_date_obj) {
                if ($current_date >= $from_date_obj) {
                    $occurrences[] = array(
                        'id' => $expense['id'],
                        'budget_id' => $expense['budget_id'],
                        'amount' => $expense['amount'],
                        'expense_name' => $expense['expense_name'],
                        'description' => $expense['description'],
                        'category' => $expense['category'],
                        'occurrence_date' => $current_date->format('Y-m-d'),
                        'type' => 'recurring',
                        'frequency' => $expense['frequency'],
                        'is_active' => $expense['is_active']
                    );
                }

                // Calculate next occurrence
                $this->advance_date_by_frequency($current_date, $expense['frequency'], $expense['frequency_interval']);
            }
        }

        // Sort by date
        usort($occurrences, function($a, $b) {
            return strcmp($a['occurrence_date'], $b['occurrence_date']);
        });

        return $occurrences;
    }

    /**
     * Calculate projected balance for future dates including all expenses
     */
    public function get_projected_balance($budget_id, $target_date) {
        // Get current balance
        $current_balance = $this->get_current_balance($budget_id);
        
        // Get future one-time expenses
        $future_expenses = $this->get_future_expenses($budget_id, date('Y-m-d'), $target_date);
        $future_total = array_sum(array_column($future_expenses, 'amount'));
        
        // Get recurring expenses for the period
        $recurring_occurrences = $this->get_recurring_expense_occurrences($budget_id, date('Y-m-d'), $target_date);
        $recurring_total = array_sum(array_column($recurring_occurrences, 'amount'));
        
        // Calculate additional budget until target date
        $budget = $this->get_budget($budget_id);
        $start_date = new DateTime($budget->start_date);
        $current_date = new DateTime();
        $target_date_obj = new DateTime($target_date);
        
        $additional_budget = 0;
        $temp_date = clone $current_date;
        
        while ($temp_date <= $target_date_obj) {
            $monthly_budget = $this->get_monthly_budget_for_date($budget_id, $temp_date->format('Y-m-d'));
            $additional_budget += $monthly_budget;
            $temp_date->modify('+1 month');
        }
        
        return array(
            'current_balance' => $current_balance,
            'additional_budget' => $additional_budget,
            'future_expenses' => $future_total,
            'recurring_expenses' => $recurring_total,
            'projected_balance' => $current_balance + $additional_budget - $future_total - $recurring_total,
            'target_date' => $target_date
        );
    }

    /**
     * Get current balance (total budget - total spent)
     */
    public function get_current_balance($budget_id) {
        $budget = $this->get_budget($budget_id);
        if (!$budget) {
            return 0;
        }

        // Calculate total budget with adjustments
        $calculator = new Budgex_Budget_Calculator();
        $calculation = $calculator->calculate_total_budget_with_adjustments(
            $budget_id, 
            $budget->start_date, 
            $budget->additional_budget
        );
        
        // Get total spent outcomes
        $total_spent = $this->get_total_outcomes($budget_id);
        
        return $calculation['total_budget'] - $total_spent;
    }

    /**
     * ENHANCED BUDGET PAGE METHODS
     */

    /**
     * Update budget settings
     */
    public function update_budget_settings($budget_id, $settings) {
        $update_data = array();
        
        if (isset($settings['budget_name'])) {
            $update_data['budget_name'] = $settings['budget_name'];
        }
        if (isset($settings['currency'])) {
            $update_data['currency'] = $settings['currency'];
        }
        
        if (!empty($update_data)) {
            $update_data['updated_at'] = current_time('mysql');
            return $this->wpdb->update(
                $this->budgets_table,
                $update_data,
                array('id' => $budget_id),
                array('%s', '%s', '%s'),
                array('%d')
            );
        }
        
        return true;
    }

    /**
     * Search outcomes by description or name
     */
    public function search_outcomes($budget_id, $search_term) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             AND (expense_name LIKE %s OR description LIKE %s)
             ORDER BY expense_date DESC",
            $budget_id,
            '%' . $this->wpdb->esc_like($search_term) . '%',
            '%' . $this->wpdb->esc_like($search_term) . '%'
        ));
    }

    /**
     * Filter outcomes by various criteria
     */
    public function filter_outcomes($budget_id, $filters) {
        $where_conditions = array("budget_id = %d");
        $params = array($budget_id);
        
        if (!empty($filters['category'])) {
            $where_conditions[] = "category = %s";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['date_from'])) {
            $where_conditions[] = "expense_date >= %s";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where_conditions[] = "expense_date <= %s";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['amount_min'])) {
            $where_conditions[] = "expense_amount >= %f";
            $params[] = $filters['amount_min'];
        }
        
        if (!empty($filters['amount_max'])) {
            $where_conditions[] = "expense_amount <= %f";
            $params[] = $filters['amount_max'];
        }
        
        if (!empty($filters['type'])) {
            $where_conditions[] = "expense_type = %s";
            $params[] = $filters['type'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->outcomes_table} WHERE {$where_clause} ORDER BY expense_date DESC",
            ...$params
        ));
    }

    /**
     * Get total spent today
     */
    public function get_total_spent_today($budget_id) {
        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COALESCE(SUM(expense_amount), 0) FROM {$this->outcomes_table} 
             WHERE budget_id = %d AND DATE(expense_date) = CURDATE()",
            $budget_id
        ));
    }

    /**
     * Get total spent this week
     */
    public function get_total_spent_this_week($budget_id) {
        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COALESCE(SUM(expense_amount), 0) FROM {$this->outcomes_table} 
             WHERE budget_id = %d AND YEARWEEK(expense_date) = YEARWEEK(CURDATE())",
            $budget_id
        ));
    }

    /**
     * Get total spent this month
     */
    public function get_total_spent_this_month($budget_id) {
        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COALESCE(SUM(expense_amount), 0) FROM {$this->outcomes_table} 
             WHERE budget_id = %d AND YEAR(expense_date) = YEAR(CURDATE()) AND MONTH(expense_date) = MONTH(CURDATE())",
            $budget_id
        ));
    }

    /**
     * Get remaining budget
     */
    public function get_remaining_budget($budget_id) {
        $budget = $this->get_budget($budget_id);
        $total_spent = $this->get_total_spent_this_month($budget_id);
        return max(0, ($budget['monthly_budget'] + $budget['additional_budget']) - $total_spent);
    }

    /**
     * Get average daily spending
     */
    public function get_average_daily_spending($budget_id) {
        $total_spent = $this->get_total_spent_this_month($budget_id);
        $days_in_month = date('j'); // Current day of month
        return $days_in_month > 0 ? $total_spent / $days_in_month : 0;
    }

    /**
     * Get top spending categories
     */
    public function get_top_spending_categories($budget_id, $limit = 5) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT category, SUM(expense_amount) as total,
                    (SUM(expense_amount) / (SELECT SUM(expense_amount) FROM {$this->outcomes_table} WHERE budget_id = %d) * 100) as percentage
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d AND category IS NOT NULL AND category != ''
             GROUP BY category 
             ORDER BY total DESC 
             LIMIT %d",
            $budget_id, $budget_id, $limit
        ));
    }

    /**
     * Get recent outcomes
     */
    public function get_recent_outcomes($budget_id, $limit = 5) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             ORDER BY expense_date DESC, id DESC 
             LIMIT %d",
            $budget_id, $limit
        ));
    }

    /**
     * Get monthly spending breakdown for charts
     */
    public function get_monthly_spending_breakdown($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT DATE_FORMAT(expense_date, '%Y-%m') as month, 
                    SUM(expense_amount) as total_spent,
                    COUNT(*) as transaction_count
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY DATE_FORMAT(expense_date, '%Y-%m') 
             ORDER BY month DESC 
             LIMIT 12",
            $budget_id
        ));
    }

    /**
     * Get spending by category for charts
     */
    public function get_spending_by_category($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT COALESCE(category, 'אחר') as category, SUM(expense_amount) as total
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY category 
             ORDER BY total DESC",
            $budget_id
        ));
    }

    /**
     * Get spending trends for charts
     */
    public function get_spending_trends($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT expense_date as date,
                    SUM(SUM(expense_amount)) OVER (ORDER BY expense_date) as cumulative_spent
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY expense_date 
             ORDER BY expense_date",
            $budget_id
        ));
    }

    /**
     * Get total spent for budget
     */
    public function get_total_spent($budget_id) {
        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COALESCE(SUM(expense_amount), 0) FROM {$this->outcomes_table} WHERE budget_id = %d",
            $budget_id
        ));
    }

    /**
     * Get outcomes for export with date filtering
     */
    public function get_outcomes_for_export($budget_id, $date_filter) {
        $where_conditions = array("budget_id = %d");
        $params = array($budget_id);
        
        if (!empty($date_filter['start'])) {
            $where_conditions[] = "expense_date >= %s";
            $params[] = $date_filter['start'];
        }
        
        if (!empty($date_filter['end'])) {
            $where_conditions[] = "expense_date <= %s";
            $params[] = $date_filter['end'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->outcomes_table} WHERE {$where_clause} ORDER BY expense_date DESC",
            ...$params
        ));
    }

    /**
     * Get budget summary for date range
     */
    public function get_budget_summary($budget_id, $date_filter) {
        $where_conditions = array("budget_id = %d");
        $params = array($budget_id);
        
        if (!empty($date_filter['start'])) {
            $where_conditions[] = "expense_date >= %s";
            $params[] = $date_filter['start'];
        }
        
        if (!empty($date_filter['end'])) {
            $where_conditions[] = "expense_date <= %s";
            $params[] = $date_filter['end'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_transactions,
                SUM(expense_amount) as total_spent,
                AVG(expense_amount) as average_transaction,
                MAX(expense_amount) as highest_transaction,
                MIN(expense_amount) as lowest_transaction
             FROM {$this->outcomes_table} WHERE {$where_clause}",
            ...$params
        ));
    }

    /**
     * Get category breakdown for date range
     */
    public function get_category_breakdown($budget_id, $date_filter) {
        $where_conditions = array("budget_id = %d");
        $params = array($budget_id);
        
        if (!empty($date_filter['start'])) {
            $where_conditions[] = "expense_date >= %s";
            $params[] = $date_filter['start'];
        }
        
        if (!empty($date_filter['end'])) {
            $where_conditions[] = "expense_date <= %s";
            $params[] = $date_filter['end'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT 
                COALESCE(category, 'אחר') as category,
                SUM(expense_amount) as total,
                COUNT(*) as transaction_count
             FROM {$this->outcomes_table} WHERE {$where_clause}
             GROUP BY category 
             ORDER BY total DESC",
            ...$params
        ));
    }

    /**
     * Get peak spending days
     */
    public function get_peak_spending_days($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT DAYOFWEEK(expense_date) as day_of_week, 
                    SUM(expense_amount) as total_spent,
                    COUNT(*) as transaction_count
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY DAYOFWEEK(expense_date) 
             ORDER BY total_spent DESC",
            $budget_id
        ));
    }

    /**
     * Get spending frequency analysis
     */
    public function get_spending_frequency($budget_id) {
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT 
                COUNT(DISTINCT DATE(expense_date)) as active_days,
                COUNT(*) as total_transactions,
                AVG(daily_totals.daily_amount) as avg_daily_spending
             FROM (
                 SELECT DATE(expense_date) as date, SUM(expense_amount) as daily_amount
                 FROM {$this->outcomes_table} 
                 WHERE budget_id = %d 
                 GROUP BY DATE(expense_date)
             ) as daily_totals",
            $budget_id
        ));
    }

    /**
     * Get seasonal spending trends
     */
    public function get_seasonal_spending_trends($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT 
                MONTH(expense_date) as month,
                SUM(expense_amount) as total_spent,
                COUNT(*) as transaction_count
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY MONTH(expense_date) 
             ORDER BY month",
            $budget_id
        ));
    }

    /**
     * Get projected month end balance
     */
    public function get_projected_month_end_balance($budget_id) {
        $budget = $this->get_budget($budget_id);
        $current_spent = $this->get_total_spent_this_month($budget_id);
        $avg_daily = $this->get_average_daily_spending($budget_id);
        $days_remaining = date('t') - date('j');
        
        $projected_additional_spending = $avg_daily * $days_remaining;
        $projected_total = $current_spent + $projected_additional_spending;
        $total_budget = $budget['monthly_budget'] + $budget['additional_budget'];
        
        return array(
            'projected_total_spending' => $projected_total,
            'projected_remaining' => max(0, $total_budget - $projected_total),
            'projection_accuracy' => $this->calculate_projection_accuracy($budget_id)
        );
    }

    /**
     * Get spending trend analysis
     */
    public function get_spending_trend_analysis($budget_id) {
        $recent_week = $this->get_total_spent_this_week($budget_id);
        $previous_week = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COALESCE(SUM(expense_amount), 0) FROM {$this->outcomes_table} 
             WHERE budget_id = %d AND YEARWEEK(expense_date) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK)",
            $budget_id
        ));
        
        $trend = 'stable';
        $change_percentage = 0;
        
        if ($previous_week > 0) {
            $change_percentage = (($recent_week - $previous_week) / $previous_week) * 100;
            if ($change_percentage > 10) {
                $trend = 'increasing';
            } elseif ($change_percentage < -10) {
                $trend = 'decreasing';
            }
        }
        
        return array(
            'trend' => $trend,
            'change_percentage' => $change_percentage,
            'recent_week_spending' => $recent_week,
            'previous_week_spending' => $previous_week
        );
    }

    /**
     * Get category spending forecasts
     */
    public function get_category_spending_forecasts($budget_id) {
        $categories = $this->get_spending_by_category($budget_id);
        $forecasts = array();
        
        foreach ($categories as $category) {
            $avg_monthly = $this->wpdb->get_var($this->wpdb->prepare(
                "SELECT AVG(monthly_total) FROM (
                    SELECT SUM(expense_amount) as monthly_total
                    FROM {$this->outcomes_table} 
                    WHERE budget_id = %d AND category = %s
                    GROUP BY YEAR(expense_date), MONTH(expense_date)
                ) as monthly_data",
                $budget_id, $category->category
            ));
            
            $forecasts[] = array(
                'category' => $category->category,
                'current_spending' => $category->total,
                'predicted_monthly' => $avg_monthly,
                'trend' => $avg_monthly > $category->total ? 'increasing' : 'decreasing'
            );
        }
        
        return $forecasts;
    }

    /**
     * Get previous months comparison
     */
    public function get_previous_months_comparison($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT 
                DATE_FORMAT(expense_date, '%Y-%m') as month,
                SUM(expense_amount) as total_spent,
                COUNT(*) as transaction_count
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY DATE_FORMAT(expense_date, '%Y-%m') 
             ORDER BY month DESC 
             LIMIT 6",
            $budget_id
        ));
    }

    /**
     * Get category month comparison
     */
    public function get_category_month_comparison($budget_id) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT 
                category,
                SUM(CASE WHEN YEAR(expense_date) = YEAR(CURDATE()) AND MONTH(expense_date) = MONTH(CURDATE()) THEN expense_amount ELSE 0 END) as current_month,
                SUM(CASE WHEN YEAR(expense_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) AND MONTH(expense_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN expense_amount ELSE 0 END) as previous_month
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY category 
             HAVING current_month > 0 OR previous_month > 0
             ORDER BY current_month DESC",
            $budget_id
        ));
    }

    /**
     * Get budget efficiency metrics
     */
    public function get_budget_efficiency_metrics($budget_id) {
        $budget = $this->get_budget($budget_id);
        $current_spent = $this->get_total_spent_this_month($budget_id);
        $days_passed = date('j');
        $days_in_month = date('t');
        
        $expected_spending = ($budget['monthly_budget'] / $days_in_month) * $days_passed;
        $efficiency_ratio = $expected_spending > 0 ? ($current_spent / $expected_spending) : 0;
        
        return array(
            'efficiency_ratio' => $efficiency_ratio,
            'days_passed' => $days_passed,
            'days_remaining' => $days_in_month - $days_passed,
            'budget_utilization' => ($current_spent / ($budget['monthly_budget'] + $budget['additional_budget'])) * 100,
            'daily_target' => $budget['monthly_budget'] / $days_in_month,
            'actual_daily_average' => $this->get_average_daily_spending($budget_id)
        );
    }

    /**
     * Get recent spending trend for risk analysis
     */
    public function get_recent_spending_trend($budget_id, $days = 7) {
        return $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COALESCE(SUM(expense_amount), 0) FROM {$this->outcomes_table} 
             WHERE budget_id = %d AND expense_date >= DATE_SUB(CURDATE(), INTERVAL %d DAY)",
            $budget_id, $days
        ));
    }

    /**
     * Calculate projection accuracy based on historical data
     */
    private function calculate_projection_accuracy($budget_id) {
        // Simple accuracy calculation - can be enhanced
        $historical_months = $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT 
                DATE_FORMAT(expense_date, '%Y-%m') as month,
                SUM(expense_amount) as actual_spent
             FROM {$this->outcomes_table} 
             WHERE budget_id = %d 
             GROUP BY DATE_FORMAT(expense_date, '%Y-%m') 
             ORDER BY month DESC 
             LIMIT 3",
            $budget_id
        ));
        
        if (count($historical_months) >= 2) {
            $variance = 0;
            for ($i = 0; $i < count($historical_months) - 1; $i++) {
                $variance += abs($historical_months[$i]->actual_spent - $historical_months[$i + 1]->actual_spent);
            }
            $avg_variance = $variance / (count($historical_months) - 1);
            
            // Return accuracy as percentage (lower variance = higher accuracy)
            $budget = $this->get_budget($budget_id);
            $accuracy = max(0, 100 - (($avg_variance / $budget['monthly_budget']) * 100));
            return min(100, $accuracy);
        }
        
        return 75; // Default accuracy when insufficient data
    }

    /**
     * UTILITY METHODS
     */

    /**
     * Calculate next occurrence based on frequency
     */
    private function calculate_next_occurrence($start_date, $frequency, $interval = 1) {
        $date = new DateTime($start_date);
        $this->advance_date_by_frequency($date, $frequency, $interval);
        return $date->format('Y-m-d');
    }

    /**
     * Advance date by frequency
     */
    private function advance_date_by_frequency(&$date, $frequency, $interval = 1) {
        switch ($frequency) {
            case 'daily':
                $date->modify("+{$interval} days");
                break;
            case 'weekly':
                $date->modify("+{$interval} weeks");
                break;
            case 'monthly':
                $date->modify("+{$interval} months");
                break;
            case 'quarterly':
                $months = $interval * 3;
                $date->modify("+{$months} months");
                break;
            case 'yearly':
                $date->modify("+{$interval} years");
                break;
        }
    }

    /**
     * Log database errors for debugging
     */
    private function log_db_error($operation, $error) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Budgex DB Error in {$operation}: " . $error);
        }
    }

    /**
     * Get outcomes by IDs
     */
    public function get_outcomes_by_ids($outcome_ids) {
        global $wpdb;
        
        if (empty($outcome_ids)) {
            return array();
        }
        
        $placeholders = implode(',', array_fill(0, count($outcome_ids), '%d'));
        
        $query = $wpdb->prepare("
            SELECT o.*, u.display_name as user_name 
            FROM {$this->outcomes_table} o
            LEFT JOIN {$wpdb->users} u ON o.user_id = u.ID
            WHERE o.id IN ($placeholders)
            ORDER BY o.date DESC
        ", $outcome_ids);
        
        return $wpdb->get_results($query);
    }

    /**
     * Update outcome category
     */
    public function update_outcome_category($outcome_id, $category) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->outcomes_table,
            array('category' => $category),
            array('id' => $outcome_id),
            array('%s'),
            array('%d')
        );
        
        if ($result === false) {
            $this->log_db_error('update_outcome_category', $wpdb->last_error);
            return false;
        }
        
        return true;
    }
}
?>