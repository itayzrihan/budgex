<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Budgex_Permissions {
    private $database;

    public function __construct() {
        $this->database = new Budgex_Database();
    }

    public function can_view_budget($budget_id, $user_id) {
        $budget = $this->database->get_budget($budget_id);
        
        if (!$budget) {
            return false;
        }
        
        // Owner can always view
        if ($budget->user_id == $user_id) {
            return true;
        }
        
        // Check if user has been shared the budget
        return $this->is_budget_shared_with_user($budget_id, $user_id);
    }

    public function can_edit_budget($budget_id, $user_id) {
        $budget = $this->database->get_budget($budget_id);
        
        if (!$budget) {
            return false;
        }
        
        // Owner can always edit
        if ($budget->user_id == $user_id) {
            return true;
        }
        
        // Check if user has admin role on this budget
        return $this->has_budget_role($budget_id, $user_id, 'admin');
    }

    public function can_delete_budget($budget_id, $user_id) {
        $budget = $this->database->get_budget($budget_id);
        
        if (!$budget) {
            return false;
        }
        
        // Only owner can delete
        return $budget->user_id == $user_id;
    }

    public function can_invite_users($budget_id, $user_id) {
        return $this->can_edit_budget($budget_id, $user_id);
    }

    public function can_add_outcomes($budget_id, $user_id) {
        $budget = $this->database->get_budget($budget_id);
        
        if (!$budget) {
            return false;
        }
        
        // Owner can always add outcomes
        if ($budget->user_id == $user_id) {
            return true;
        }
        
        // Admin users can add outcomes
        return $this->has_budget_role($budget_id, $user_id, 'admin');
    }

    public function can_add_budget($user_id) {
        // Any registered user can create budgets
        return is_user_logged_in() && $user_id > 0;
    }

    private function is_budget_shared_with_user($budget_id, $user_id) {
        global $wpdb;
        $shares_table = $wpdb->prefix . 'budgex_budget_shares';
        
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$shares_table} WHERE budget_id = %d AND user_id = %d",
                $budget_id,
                $user_id
            )
        );
        
        return $result > 0;
    }

    private function has_budget_role($budget_id, $user_id, $required_role = 'viewer') {
        global $wpdb;
        $shares_table = $wpdb->prefix . 'budgex_budget_shares';
        
        $user_role = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT role FROM {$shares_table} WHERE budget_id = %d AND user_id = %d",
                $budget_id,
                $user_id
            )
        );
        
        if (!$user_role) {
            return false;
        }
        
        // Role hierarchy: admin > viewer
        $role_hierarchy = array('viewer' => 1, 'admin' => 2);
        
        return $role_hierarchy[$user_role] >= $role_hierarchy[$required_role];
    }

    public function get_user_role_on_budget($budget_id, $user_id) {
        $budget = $this->database->get_budget($budget_id);
        
        if (!$budget) {
            return false;
        }
        
        // Check if owner
        if ($budget->user_id == $user_id) {
            return 'owner';
        }
        
        global $wpdb;
        $shares_table = $wpdb->prefix . 'budgex_budget_shares';
        
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT role FROM {$shares_table} WHERE budget_id = %d AND user_id = %d",
                $budget_id,
                $user_id
            )
        );
    }

    public function get_budget_users($budget_id) {
        global $wpdb;
        $shares_table = $wpdb->prefix . 'budgex_budget_shares';
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT s.user_id, s.role, u.display_name, u.user_email 
                 FROM {$shares_table} s 
                 INNER JOIN {$wpdb->users} u ON s.user_id = u.ID 
                 WHERE s.budget_id = %d",
                $budget_id
            )
        );
    }

    public function can_edit_outcomes($budget_id, $user_id) {
        // Only admin and editor roles can edit outcomes
        $role = $this->get_user_role_on_budget($budget_id, $user_id);
        return in_array($role, ['admin', 'editor', 'owner']);
    }

    public function can_delete_outcomes($budget_id, $user_id) {
        // Only admin and editor roles can delete outcomes
        $role = $this->get_user_role_on_budget($budget_id, $user_id);
        return in_array($role, ['admin', 'editor', 'owner']);
    }

    public function is_budget_owner($budget_id, $user_id) {
        $budget = $this->database->get_budget($budget_id);
        
        if (!$budget) {
            return false;
        }
        
        return $budget['user_id'] == $user_id;
    }
}
?>