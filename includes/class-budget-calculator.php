<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Budgex_Budget_Calculator {

    public static function calculate_total_budget($start_date, $monthly_budget, $additional_budget = 0, $budget_id = null) {
        $start = new DateTime($start_date);
        $current = new DateTime();
        
        // If we have budget_id, calculate using variable monthly budgets
        if ($budget_id) {
            $database = new Budgex_Database();
            return self::calculate_total_budget_with_adjustments($budget_id, $start_date, $additional_budget);
        }
        
        // Fallback to original calculation for backward compatibility
        $interval = $start->diff($current);
        $months_passed = ($interval->y * 12) + $interval->m;
        
        // Add partial month if we're past the start day of the month
        if ($current->format('d') >= $start->format('d')) {
            $months_passed++;
        }
        
        $total_monthly_budget = $months_passed * $monthly_budget;
        $total_budget = $total_monthly_budget + $additional_budget;
        
        return array(
            'total_budget' => $total_budget,
            'monthly_budget' => $total_monthly_budget,
            'additional_budget' => $additional_budget,
            'months_passed' => $months_passed
        );
    }

    /**
     * Calculate total budget considering monthly budget adjustments
     */
    public static function calculate_total_budget_with_adjustments($budget_id, $start_date, $additional_budget = 0) {
        $database = new Budgex_Database();
        $start = new DateTime($start_date);
        $current = new DateTime();
        
        $total_monthly_budget = 0;
        $months_passed = 0;
        
        // Iterate through each month from start to current
        $temp_date = clone $start;
        
        while ($temp_date <= $current) {
            $month_start = clone $temp_date;
            $month_end = clone $temp_date;
            $month_end->modify('last day of this month');
            
            if ($month_end > $current) {
                $month_end = clone $current;
            }
            
            // Get the monthly budget amount for this specific month
            $monthly_amount = $database->get_monthly_budget_for_date($budget_id, $month_start->format('Y-m-d'));
            
            // Calculate fraction of month if it's the current month
            if ($month_end->format('Y-m') === $current->format('Y-m')) {
                $days_in_month = $month_start->format('t');
                $days_passed = $current->format('d');
                $fraction = min($days_passed / $days_in_month, 1);
                $total_monthly_budget += $monthly_amount * $fraction;
                $months_passed += $fraction;
            } else {
                $total_monthly_budget += $monthly_amount;
                $months_passed++;
            }
            
            $temp_date->modify('+1 month')->modify('first day of this month');
        }
        
        $total_budget = $total_monthly_budget + $additional_budget;
        
        return array(
            'total_budget' => $total_budget,
            'monthly_budget' => $total_monthly_budget,
            'additional_budget' => $additional_budget,
            'months_passed' => $months_passed
        );
    }

    public static function calculate_remaining_budget($budget_id) {
        $database = new Budgex_Database();
        $budget = $database->get_budget($budget_id);
        
        if (!$budget) {
            return array(
                'total_available' => 0,
                'total_spent' => 0,
                'remaining' => 0,
                'percentage_used' => 0
            );
        }
        
        $total_budget_data = self::calculate_total_budget_with_adjustments(
            $budget_id, 
            $budget->start_date, 
            $budget->additional_budget
        );
        
        $total_outcomes = $database->get_total_outcomes($budget_id);
        
        return array(
            'total_available' => $total_budget_data['total_budget'],
            'total_spent' => $total_outcomes,
            'remaining' => $total_budget_data['total_budget'] - $total_outcomes,
            'percentage_used' => $total_budget_data['total_budget'] > 0 ? 
                               ($total_outcomes / $total_budget_data['total_budget']) * 100 : 0,
            'budget_details' => $total_budget_data
        );
    }

    public static function get_monthly_breakdown($budget_id) {
        $database = new Budgex_Database();
        $budget = $database->get_budget($budget_id);
        
        if (!$budget) {
            return array();
        }
        
        $start_date = new DateTime($budget->start_date);
        $current_date = new DateTime();
        $breakdown = array();
        
        $temp_date = clone $start_date;
        $month_counter = 1;
        
        while ($temp_date <= $current_date) {
            $month_start = clone $temp_date;
            $month_end = clone $temp_date;
            $month_end->modify('last day of this month');
            
            if ($month_end > $current_date) {
                $month_end = clone $current_date;
            }
            
            // Get the monthly budget amount for this specific month
            $monthly_budget_amount = $database->get_monthly_budget_for_date($budget_id, $month_start->format('Y-m-d'));
            
            $breakdown[] = array(
                'month' => $month_counter,
                'start_date' => $month_start->format('Y-m-d'),
                'end_date' => $month_end->format('Y-m-d'),
                'budget_added' => $monthly_budget_amount,
                'hebrew_month' => self::get_hebrew_month($month_start->format('n')),
                'year' => $month_start->format('Y')
            );
            
            $temp_date->modify('+1 month');
            $month_counter++;
        }
        
        return $breakdown;
    }

    private static function get_hebrew_month($month_number) {
        $hebrew_months = array(
            1 => 'ינואר',
            2 => 'פברואר', 
            3 => 'מרץ',
            4 => 'אפריל',
            5 => 'מאי',
            6 => 'יוני',
            7 => 'יולי',
            8 => 'אוגוסט',
            9 => 'ספטמבר',
            10 => 'אוקטובר',
            11 => 'נובמבר',
            12 => 'דצמבר'
        );
        
        return $hebrew_months[$month_number] ?? '';
    }

    public static function format_currency($amount, $currency = 'ILS') {
        $formatted = number_format($amount, 2);
        
        switch ($currency) {
            case 'ILS':
                return $formatted . ' ש"ח';
            case 'USD':
                return '$' . $formatted;
            default:
                return $formatted . ' ' . $currency;
        }
    }
}