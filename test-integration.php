<?php
/**
 * Budgex Integration Test Script
 * 
 * This script tests all the main functionality of the Budgex plugin
 * Run this script in a WordPress environment to verify all features work correctly
 */

// Simulate WordPress environment check
if (!defined('ABSPATH')) {
    echo "ERROR: This script must be run within WordPress environment\n";
    exit;
}

class Budgex_Integration_Test {
    private $database;
    private $permissions;
    private $public_class;
    private $test_user_id;
    
    public function __construct() {
        echo "=== BUDGEX INTEGRATION TEST ===\n";
        echo "Testing plugin functionality...\n\n";
        
        // Initialize classes
        $this->database = new Budgex_Database();
        $this->permissions = new Budgex_Permissions();
        $this->public_class = new Budgex_Public();
        
        // Create test user
        $this->test_user_id = $this->create_test_user();
    }
    
    public function run_all_tests() {
        $this->test_database_connectivity();
        $this->test_budget_creation();
        $this->test_outcome_management();
        $this->test_permission_system();
        $this->test_ajax_handlers();
        $this->test_calculations();
        $this->test_invite_system();
        
        echo "\n=== TEST COMPLETE ===\n";
        echo "All tests completed. Check results above.\n";
    }
    
    private function create_test_user() {
        // This would normally create a test user
        // For now, we'll use user ID 1 if available
        return 1;
    }
    
    private function test_database_connectivity() {
        echo "1. Testing Database Connectivity...\n";
        
        try {
            global $wpdb;
            $result = $wpdb->get_var("SELECT 1");
            echo "   ✓ Database connection: OK\n";
            
            // Test if our tables exist
            $tables = [
                'budgex_budgets',
                'budgex_outcomes', 
                'budgex_invitations',
                'budgex_budget_shares'
            ];
            
            foreach ($tables as $table) {
                $table_name = $wpdb->prefix . $table;
                $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
                if ($exists) {
                    echo "   ✓ Table $table: EXISTS\n";
                } else {
                    echo "   ✗ Table $table: MISSING\n";
                }
            }
            
        } catch (Exception $e) {
            echo "   ✗ Database error: " . $e->getMessage() . "\n";
        }
    }
    
    private function test_budget_creation() {
        echo "\n2. Testing Budget Creation...\n";
        
        try {
            // Test budget creation method
            $budget_id = $this->database->add_budget(
                $this->test_user_id,
                'Test Budget',
                5000.00,
                'ILS',
                date('Y-m-d')
            );
            
            if ($budget_id) {
                echo "   ✓ Budget creation: SUCCESS (ID: $budget_id)\n";
                
                // Test budget retrieval
                $budget = $this->database->get_budget($budget_id);
                if ($budget) {
                    echo "   ✓ Budget retrieval: SUCCESS\n";
                } else {
                    echo "   ✗ Budget retrieval: FAILED\n";
                }
            } else {
                echo "   ✗ Budget creation: FAILED\n";
            }
            
        } catch (Exception $e) {
            echo "   ✗ Budget creation error: " . $e->getMessage() . "\n";
        }
    }
    
    private function test_outcome_management() {
        echo "\n3. Testing Outcome Management...\n";
        
        try {
            // Get a test budget
            $budgets = $this->database->get_user_budgets($this->test_user_id);
            if (empty($budgets)) {
                echo "   ✗ No budgets found for testing outcomes\n";
                return;
            }
            
            $budget_id = $budgets[0]['id'];
            
            // Test outcome addition
            $outcome_id = $this->database->add_outcome(
                $budget_id,
                150.50,
                'Test expense for grocery shopping',
                'Groceries',
                date('Y-m-d')
            );
            
            if ($outcome_id) {
                echo "   ✓ Outcome creation: SUCCESS (ID: $outcome_id)\n";
                
                // Test outcome retrieval
                $outcomes = $this->database->get_budget_outcomes($budget_id);
                if (count($outcomes) > 0) {
                    echo "   ✓ Outcome retrieval: SUCCESS (" . count($outcomes) . " outcomes)\n";
                } else {
                    echo "   ✗ Outcome retrieval: FAILED\n";
                }
            } else {
                echo "   ✗ Outcome creation: FAILED\n";
            }
            
        } catch (Exception $e) {
            echo "   ✗ Outcome management error: " . $e->getMessage() . "\n";
        }
    }
    
    private function test_permission_system() {
        echo "\n4. Testing Permission System...\n";
        
        try {
            $budgets = $this->database->get_user_budgets($this->test_user_id);
            if (empty($budgets)) {
                echo "   ✗ No budgets found for testing permissions\n";
                return;
            }
            
            $budget_id = $budgets[0]['id'];
            
            // Test owner permissions
            $can_view = $this->permissions->can_view_budget($budget_id, $this->test_user_id);
            $can_edit = $this->permissions->can_edit_budget($budget_id, $this->test_user_id);
            $can_add_outcomes = $this->permissions->can_add_outcomes($budget_id, $this->test_user_id);
            
            echo "   ✓ Owner can view: " . ($can_view ? "YES" : "NO") . "\n";
            echo "   ✓ Owner can edit: " . ($can_edit ? "YES" : "NO") . "\n";
            echo "   ✓ Owner can add outcomes: " . ($can_add_outcomes ? "YES" : "NO") . "\n";
            
            // Test role-based permissions
            $user_role = $this->permissions->get_user_role_on_budget($budget_id, $this->test_user_id);
            echo "   ✓ User role on budget: " . ($user_role ?: "None") . "\n";
            
        } catch (Exception $e) {
            echo "   ✗ Permission system error: " . $e->getMessage() . "\n";
        }
    }
    
    private function test_ajax_handlers() {
        echo "\n5. Testing AJAX Handler Registration...\n";
        
        $ajax_actions = [
            'budgex_add_outcome',
            'budgex_edit_outcome', 
            'budgex_delete_outcome',
            'budgex_get_dashboard_stats',
            'budgex_get_user_budgets',
            'budgex_get_outcomes',
            'budgex_send_invitation',
            'budgex_accept_invitation'
        ];
        
        foreach ($ajax_actions as $action) {
            $registered = has_action("wp_ajax_$action");
            echo "   " . ($registered ? "✓" : "✗") . " AJAX handler '$action': " . ($registered ? "REGISTERED" : "NOT REGISTERED") . "\n";
        }
    }
    
    private function test_calculations() {
        echo "\n6. Testing Budget Calculations...\n";
        
        try {
            $budgets = $this->database->get_user_budgets($this->test_user_id);
            if (empty($budgets)) {
                echo "   ✗ No budgets found for testing calculations\n";
                return;
            }
            
            $budget_id = $budgets[0]['id'];
            
            // Test budget calculation
            $calculation = Budgex_Budget_Calculator::calculate_remaining_budget($budget_id);
            
            if ($calculation) {
                echo "   ✓ Budget calculation: SUCCESS\n";
                echo "     - Total spent: " . ($calculation['total_spent'] ?? 'N/A') . "\n";
                echo "     - Remaining: " . ($calculation['remaining'] ?? 'N/A') . "\n";
                echo "     - Percentage used: " . ($calculation['percentage_used'] ?? 'N/A') . "%\n";
            } else {
                echo "   ✗ Budget calculation: FAILED\n";
            }
            
            // Test monthly breakdown
            $breakdown = Budgex_Budget_Calculator::get_monthly_breakdown($budget_id);
            if ($breakdown) {
                echo "   ✓ Monthly breakdown: SUCCESS\n";
            } else {
                echo "   ✗ Monthly breakdown: FAILED\n";
            }
            
        } catch (Exception $e) {
            echo "   ✗ Calculation error: " . $e->getMessage() . "\n";
        }
    }
    
    private function test_invite_system() {
        echo "\n7. Testing Invitation System...\n";
        
        try {
            $budgets = $this->database->get_user_budgets($this->test_user_id);
            if (empty($budgets)) {
                echo "   ✗ No budgets found for testing invitations\n";
                return;
            }
            
            $budget_id = $budgets[0]['id'];
            
            // Test invitation creation
            $invitation_id = $this->database->create_invitation(
                $budget_id,
                $this->test_user_id,
                2, // Assume user ID 2 exists
                'viewer'
            );
            
            if ($invitation_id) {
                echo "   ✓ Invitation creation: SUCCESS (ID: $invitation_id)\n";
                
                // Test invitation retrieval
                $invitation = $this->database->get_invitation($invitation_id);
                if ($invitation) {
                    echo "   ✓ Invitation retrieval: SUCCESS\n";
                } else {
                    echo "   ✗ Invitation retrieval: FAILED\n";
                }
            } else {
                echo "   ✗ Invitation creation: FAILED\n";
            }
            
        } catch (Exception $e) {
            echo "   ✗ Invitation system error: " . $e->getMessage() . "\n";
        }
    }
}

// Run tests if this file is accessed directly (for testing)
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    if (defined('ABSPATH')) {
        $test = new Budgex_Integration_Test();
        $test->run_all_tests();
    } else {
        echo "This test script must be run within WordPress.\n";
        echo "Copy this file to your WordPress root and access via browser or WP-CLI.\n";
    }
}
