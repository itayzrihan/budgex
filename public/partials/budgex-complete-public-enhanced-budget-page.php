<?php
/**
 * Complete Public Enhanced Budget Management Page
 * 
 * This file provides the full enhanced budget management interface for public users
 * accessing /budgexpage/{id} - a complete standalone implementation with all admin features
 * adapted for public frontend use.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Variables should be passed from the calling function
if (!isset($budget) || !isset($calculation) || !isset($user_role) || !isset($outcomes)) {
    echo '<div class="budgex-error"><p>' . __('שגיאה בטעינת נתוני התקציב', 'budgex') . '</p></div>';
    return;
}

// Initialize required classes
$calculator = new Budgex_Budget_Calculator();
$database = new Budgex_Database();

// Get comprehensive enhanced data
$shared_users = $database->get_budget_shared_users($budget->id);
$pending_invitations = $database->get_pending_invitations($budget->id);
$future_expenses = $database->get_future_expenses($budget->id);
$recurring_expenses = $database->get_recurring_expenses($budget->id);
$monthly_breakdown = $calculator->get_monthly_breakdown($budget->id);
$budget_adjustments = $database->get_budget_adjustments($budget->id);
$projected_balance = $calculator->calculate_projected_balance($budget->id);

// Calculate enhanced summary data
$total_spent = $calculation['total_spent'];
$remaining_budget = $calculation['remaining'];
$budget_percentage = $calculation['percentage'];
$additional_budget = isset($calculation['budget_details']['additional_budget']) ? $calculation['budget_details']['additional_budget'] : 0;

// Get current month stats
$current_month_spent = $calculator->get_current_month_spent($budget->id);
$spending_trend = $calculator->get_spending_trend($budget->id);
$top_categories = $calculator->get_top_spending_categories($budget->id);
?>

<div class="budgex-enhanced-budget-page budgex-public-enhanced" data-budget-id="<?php echo esc_attr($budget->id); ?>" dir="rtl">
    <!-- Enhanced Header with Comprehensive Navigation -->
    <div class="enhanced-budget-header">
        <div class="header-top">
            <div class="breadcrumb">
                <a href="<?php echo home_url('/budgex/'); ?>" class="breadcrumb-link">
                    <span class="dashicons dashicons-arrow-right-alt"></span>
                    <?php _e('התקציבים שלי', 'budgex'); ?>
                </a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current"><?php echo esc_html($budget->budget_name); ?></span>
            </div>
            
            <div class="header-actions">
                <button type="button" class="action-button secondary" id="refresh-data">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('רענן נתונים', 'budgex'); ?>
                </button>
                
                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                    <button type="button" class="action-button primary" id="quick-add-outcome">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('הוסף הוצאה', 'budgex'); ?>
                    </button>
                <?php endif; ?>
                
                <div class="dropdown">
                    <button type="button" class="action-button dropdown-toggle" id="budget-menu">
                        <span class="dashicons dashicons-menu"></span>
                        <?php _e('תפריט', 'budgex'); ?>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item" id="export-excel">
                            <span class="dashicons dashicons-download"></span>
                            <?php _e('ייצא לאקסל', 'budgex'); ?>
                        </a>
                        <a href="#" class="dropdown-item" id="print-budget">
                            <span class="dashicons dashicons-printer"></span>
                            <?php _e('הדפס דוח', 'budgex'); ?>
                        </a>
                        <?php if ($user_role === 'owner'): ?>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" id="budget-settings">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php _e('הגדרות תקציב', 'budgex'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-main">
            <div class="budget-title-section">
                <h1 class="budget-title"><?php echo esc_html($budget->budget_name); ?></h1>
                <div class="budget-meta">
                    <span class="budget-role-badge <?php echo $user_role; ?>">
                        <?php 
                        echo $user_role === 'owner' ? __('בעלים', 'budgex') : 
                            ($user_role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                        ?>
                    </span>
                    <span class="budget-date">
                        <?php printf(__('נוצר ב%s', 'budgex'), date('d/m/Y', strtotime($budget->created_at))); ?>
                    </span>
                    <?php if (count($shared_users) > 0): ?>
                        <span class="shared-indicator">
                            <span class="dashicons dashicons-groups"></span>
                            <?php printf(__('משותף עם %d', 'budgex'), count($shared_users)); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Enhanced Summary Dashboard -->
            <div class="enhanced-summary-dashboard">
                <div class="summary-cards-grid">
                    <div class="summary-card budget-card">
                        <div class="card-header">
                            <h3><?php _e('תקציב כולל', 'budgex'); ?></h3>
                            <span class="card-icon"><span class="dashicons dashicons-chart-pie"></span></span>
                        </div>
                        <div class="card-content">
                            <div class="main-value"><?php echo number_format($budget->total_budget + $additional_budget, 2); ?> ₪</div>
                            <?php if ($additional_budget > 0): ?>
                                <div class="sub-value">כולל תוספת: <?php echo number_format($additional_budget, 2); ?> ₪</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="summary-card spent-card">
                        <div class="card-header">
                            <h3><?php _e('סה"כ הוצאות', 'budgex'); ?></h3>
                            <span class="card-icon"><span class="dashicons dashicons-money-alt"></span></span>
                        </div>
                        <div class="card-content">
                            <div class="main-value"><?php echo number_format($total_spent, 2); ?> ₪</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $budget_percentage; ?>%"></div>
                            </div>
                            <div class="progress-text"><?php echo round($budget_percentage, 1); ?>% מהתקציב</div>
                        </div>
                    </div>

                    <div class="summary-card remaining-card <?php echo $remaining_budget < 0 ? 'negative' : 'positive'; ?>">
                        <div class="card-header">
                            <h3><?php _e('יתרה', 'budgex'); ?></h3>
                            <span class="card-icon">
                                <span class="dashicons dashicons-<?php echo $remaining_budget < 0 ? 'warning' : 'yes-alt'; ?>"></span>
                            </span>
                        </div>
                        <div class="card-content">
                            <div class="main-value"><?php echo number_format($remaining_budget, 2); ?> ₪</div>
                            <div class="status-text">
                                <?php 
                                if ($remaining_budget < 0) {
                                    _e('חריגה מהתקציב', 'budgex');
                                } elseif ($remaining_budget < ($budget->total_budget + $additional_budget) * 0.1) {
                                    _e('קרוב לסיום', 'budgex');
                                } else {
                                    _e('בטווח התקציב', 'budgex');
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="summary-card stats-card">
                        <div class="card-header">
                            <h3><?php _e('סטטיסטיקות', 'budgex'); ?></h3>
                            <span class="card-icon"><span class="dashicons dashicons-chart-bar"></span></span>
                        </div>
                        <div class="card-content">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo count($outcomes); ?></span>
                                    <span class="stat-label"><?php _e('הוצאות', 'budgex'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo count($future_expenses); ?></span>
                                    <span class="stat-label"><?php _e('עתידיות', 'budgex'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo count($recurring_expenses); ?></span>
                                    <span class="stat-label"><?php _e('חוזרות', 'budgex'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo count($shared_users); ?></span>
                                    <span class="stat-label"><?php _e('משתמשים', 'budgex'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Tabbed Interface -->
    <div class="budget-tabs-container">
        <div class="budget-tabs">
            <div class="tab-navigation">
                <button class="tab-button active" data-tab="overview">
                    <span class="dashicons dashicons-dashboard"></span>
                    <?php _e('סקירה כללית', 'budgex'); ?>
                </button>
                <button class="tab-button" data-tab="outcomes">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('הוצאות', 'budgex'); ?>
                    <span class="tab-count"><?php echo count($outcomes); ?></span>
                </button>
                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                    <button class="tab-button" data-tab="future-expenses">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <?php _e('הוצאות עתידיות', 'budgex'); ?>
                        <span class="tab-count"><?php echo count($future_expenses); ?></span>
                    </button>
                    <button class="tab-button" data-tab="recurring-expenses">
                        <span class="dashicons dashicons-backup"></span>
                        <?php _e('הוצאות חוזרות', 'budgex'); ?>
                        <span class="tab-count"><?php echo count($recurring_expenses); ?></span>
                    </button>
                    <button class="tab-button" data-tab="budget-management">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('ניהול תקציב', 'budgex'); ?>
                    </button>
                <?php endif; ?>
                <?php if ($user_role === 'owner'): ?>
                    <button class="tab-button" data-tab="users">
                        <span class="dashicons dashicons-groups"></span>
                        <?php _e('משתמשים', 'budgex'); ?>
                        <span class="tab-count"><?php echo count($shared_users); ?></span>
                    </button>
                <?php endif; ?>
                <button class="tab-button" data-tab="reports">
                    <span class="dashicons dashicons-chart-line"></span>
                    <?php _e('דוחות ואנליטיקה', 'budgex'); ?>
                </button>
            </div>
        </div>

        <!-- Tab Content Container -->
        <div class="tab-content-container">
            
            <!-- Overview Tab -->
            <div class="tab-content active" id="tab-overview">
                <div class="overview-layout">
                    <div class="overview-main">
                        <!-- Monthly Breakdown Chart -->
                        <div class="section-card">
                            <div class="section-header">
                                <h3><?php _e('פירוט חודשי', 'budgex'); ?></h3>
                                <div class="section-actions">
                                    <button class="action-button secondary" id="toggle-chart-type">
                                        <span class="dashicons dashicons-chart-bar"></span>
                                        <?php _e('שנה תצוגה', 'budgex'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content">
                                <div class="chart-container">
                                    <canvas id="monthly-breakdown-chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="section-card">
                            <div class="section-header">
                                <h3><?php _e('פעילות אחרונה', 'budgex'); ?></h3>
                                <a href="#" class="view-all-link" data-tab="outcomes"><?php _e('צפה בהכל', 'budgex'); ?></a>
                            </div>
                            <div class="section-content">
                                <div class="recent-outcomes-list">
                                    <?php 
                                    $recent_outcomes = array_slice($outcomes, -5);
                                    foreach ($recent_outcomes as $outcome): 
                                    ?>
                                        <div class="outcome-item">
                                            <div class="outcome-details">
                                                <span class="outcome-category"><?php echo esc_html($outcome->category); ?></span>
                                                <span class="outcome-description"><?php echo esc_html($outcome->description); ?></span>
                                                <span class="outcome-date"><?php echo date('d/m/Y', strtotime($outcome->date)); ?></span>
                                            </div>
                                            <div class="outcome-amount">
                                                <?php echo number_format($outcome->amount, 2); ?> ₪
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overview-sidebar">
                        <!-- Top Categories -->
                        <div class="sidebar-card">
                            <div class="card-header">
                                <h4><?php _e('קטגוריות מובילות', 'budgex'); ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="top-categories-list">
                                    <?php foreach ($top_categories as $category): ?>
                                        <div class="category-item">
                                            <div class="category-info">
                                                <span class="category-name"><?php echo esc_html($category->category); ?></span>
                                                <span class="category-count"><?php echo $category->count; ?> הוצאות</span>
                                            </div>
                                            <div class="category-amount">
                                                <?php echo number_format($category->total_amount, 2); ?> ₪
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <div class="sidebar-card">
                                <div class="card-header">
                                    <h4><?php _e('פעולות מהירות', 'budgex'); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="quick-actions-grid">
                                        <button class="quick-action-btn" id="quick-add-expense">
                                            <span class="dashicons dashicons-plus-alt"></span>
                                            <?php _e('הוסף הוצאה', 'budgex'); ?>
                                        </button>
                                        <button class="quick-action-btn" id="quick-add-future">
                                            <span class="dashicons dashicons-calendar-alt"></span>
                                            <?php _e('הוצאה עתידית', 'budgex'); ?>
                                        </button>
                                        <button class="quick-action-btn" id="quick-add-recurring">
                                            <span class="dashicons dashicons-backup"></span>
                                            <?php _e('הוצאה חוזרת', 'budgex'); ?>
                                        </button>
                                        <button class="quick-action-btn" id="budget-increase">
                                            <span class="dashicons dashicons-chart-line"></span>
                                            <?php _e('הגדל תקציב', 'budgex'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Outcomes Tab -->
            <div class="tab-content" id="tab-outcomes">
                <div class="outcomes-management">
                    <div class="outcomes-header">
                        <h3><?php _e('ניהול הוצאות', 'budgex'); ?></h3>
                        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <div class="outcomes-actions">
                                <button type="button" class="action-button primary" id="add-outcome-btn">
                                    <span class="dashicons dashicons-plus-alt"></span>
                                    <?php _e('הוסף הוצאה', 'budgex'); ?>
                                </button>
                                <button type="button" class="action-button secondary" id="bulk-actions-btn">
                                    <span class="dashicons dashicons-admin-tools"></span>
                                    <?php _e('פעולות מרובות', 'budgex'); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="search-filter-bar">
                        <div class="search-group">
                            <input type="text" id="outcomes-search" placeholder="<?php _e('חפש הוצאה...', 'budgex'); ?>" class="search-input">
                            <button class="search-button">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                        
                        <div class="filter-group">
                            <select id="category-filter" class="filter-select">
                                <option value=""><?php _e('כל הקטגוריות', 'budgex'); ?></option>
                                <?php 
                                $categories = array_unique(array_column($outcomes, 'category'));
                                foreach ($categories as $category): 
                                ?>
                                    <option value="<?php echo esc_attr($category); ?>"><?php echo esc_html($category); ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select id="date-filter" class="filter-select">
                                <option value=""><?php _e('כל התאריכים', 'budgex'); ?></option>
                                <option value="today"><?php _e('היום', 'budgex'); ?></option>
                                <option value="week"><?php _e('השבוע', 'budgex'); ?></option>
                                <option value="month"><?php _e('החודש', 'budgex'); ?></option>
                                <option value="custom"><?php _e('תקופה מותאמת', 'budgex'); ?></option>
                            </select>
                            
                            <button class="filter-button" id="clear-filters">
                                <span class="dashicons dashicons-dismiss"></span>
                                <?php _e('נקה', 'budgex'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Outcomes Table -->
                    <div class="outcomes-content">
                        <div class="outcomes-table-container">
                            <table class="outcomes-table" id="outcomes-table">
                                <thead>
                                    <tr>
                                        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                            <th class="checkbox-column">
                                                <input type="checkbox" id="select-all-outcomes">
                                            </th>
                                        <?php endif; ?>
                                        <th class="sortable" data-sort="date"><?php _e('תאריך', 'budgex'); ?></th>
                                        <th class="sortable" data-sort="category"><?php _e('קטגוריה', 'budgex'); ?></th>
                                        <th class="sortable" data-sort="description"><?php _e('תיאור', 'budgex'); ?></th>
                                        <th class="sortable amount-column" data-sort="amount"><?php _e('סכום', 'budgex'); ?></th>
                                        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                            <th class="actions-column"><?php _e('פעולות', 'budgex'); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($outcomes as $outcome): ?>
                                        <tr data-outcome-id="<?php echo $outcome->id; ?>" class="outcome-row">
                                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                                <td class="checkbox-column">
                                                    <input type="checkbox" name="selected_outcomes[]" value="<?php echo $outcome->id; ?>">
                                                </td>
                                            <?php endif; ?>
                                            <td class="date-column">
                                                <?php echo date('d/m/Y', strtotime($outcome->date)); ?>
                                            </td>
                                            <td class="category-column">
                                                <span class="category-badge"><?php echo esc_html($outcome->category); ?></span>
                                            </td>
                                            <td class="description-column">
                                                <?php echo esc_html($outcome->description); ?>
                                            </td>
                                            <td class="amount-column">
                                                <span class="amount-value"><?php echo number_format($outcome->amount, 2); ?> ₪</span>
                                            </td>
                                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                                <td class="actions-column">
                                                    <div class="row-actions">
                                                        <button class="action-btn edit-outcome" data-outcome-id="<?php echo $outcome->id; ?>" title="<?php _e('עריכה', 'budgex'); ?>">
                                                            <span class="dashicons dashicons-edit"></span>
                                                        </button>
                                                        <button class="action-btn delete-outcome" data-outcome-id="<?php echo $outcome->id; ?>" title="<?php _e('מחיקה', 'budgex'); ?>">
                                                            <span class="dashicons dashicons-trash"></span>
                                                        </button>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                <span><?php printf(__('מציג %d הוצאות מתוך %d', 'budgex'), count($outcomes), count($outcomes)); ?></span>
                            </div>
                            <div class="pagination-controls">
                                <!-- Pagination will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Future Expenses Tab -->
            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                <div class="tab-content" id="tab-future-expenses">
                    <div class="future-expenses-management">
                        <div class="section-header">
                            <h3><?php _e('הוצאות עתידיות', 'budgex'); ?></h3>
                            <button class="action-button primary" id="add-future-expense">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף הוצאה עתידית', 'budgex'); ?>
                            </button>
                        </div>
                        
                        <div class="future-expenses-grid">
                            <?php foreach ($future_expenses as $expense): ?>
                                <div class="future-expense-card" data-expense-id="<?php echo $expense->id; ?>">
                                    <div class="expense-header">
                                        <span class="expense-category"><?php echo esc_html($expense->category); ?></span>
                                        <span class="expense-amount"><?php echo number_format($expense->amount, 2); ?> ₪</span>
                                    </div>
                                    <div class="expense-content">
                                        <h4 class="expense-title"><?php echo esc_html($expense->description); ?></h4>
                                        <p class="expense-date">
                                            <span class="dashicons dashicons-calendar-alt"></span>
                                            <?php echo date('d/m/Y', strtotime($expense->planned_date)); ?>
                                        </p>
                                    </div>
                                    <div class="expense-actions">
                                        <button class="action-btn execute-future-expense" data-expense-id="<?php echo $expense->id; ?>" title="<?php _e('בצע עכשיו', 'budgex'); ?>">
                                            <span class="dashicons dashicons-yes"></span>
                                        </button>
                                        <button class="action-btn edit-future-expense" data-expense-id="<?php echo $expense->id; ?>" title="<?php _e('עריכה', 'budgex'); ?>">
                                            <span class="dashicons dashicons-edit"></span>
                                        </button>
                                        <button class="action-btn delete-future-expense" data-expense-id="<?php echo $expense->id; ?>" title="<?php _e('מחיקה', 'budgex'); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($future_expenses)): ?>
                                <div class="empty-state">
                                    <span class="dashicons dashicons-calendar-alt"></span>
                                    <h4><?php _e('אין הוצאות עתידיות', 'budgex'); ?></h4>
                                    <p><?php _e('הוסף הוצאות מתוכננות לעתיד כדי לנהל את התקציב שלך טוב יותר', 'budgex'); ?></p>
                                    <button class="action-button primary" id="add-first-future-expense">
                                        <?php _e('הוסף הוצאה עתידית', 'budgex'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recurring Expenses Tab -->
                <div class="tab-content" id="tab-recurring-expenses">
                    <div class="recurring-expenses-management">
                        <div class="section-header">
                            <h3><?php _e('הוצאות חוזרות', 'budgex'); ?></h3>
                            <button class="action-button primary" id="add-recurring-expense">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף הוצאה חוזרת', 'budgex'); ?>
                            </button>
                        </div>
                        
                        <div class="recurring-expenses-list">
                            <?php foreach ($recurring_expenses as $expense): ?>
                                <div class="recurring-expense-item" data-expense-id="<?php echo $expense->id; ?>">
                                    <div class="expense-info">
                                        <div class="expense-main">
                                            <h4 class="expense-title"><?php echo esc_html($expense->description); ?></h4>
                                            <span class="expense-category"><?php echo esc_html($expense->category); ?></span>
                                        </div>
                                        <div class="expense-details">
                                            <span class="expense-amount"><?php echo number_format($expense->amount, 2); ?> ₪</span>
                                            <span class="expense-frequency">
                                                <?php 
                                                $frequencies = array(
                                                    'daily' => 'יומי',
                                                    'weekly' => 'שבועי',
                                                    'monthly' => 'חודשי',
                                                    'yearly' => 'שנתי'
                                                );
                                                echo $frequencies[$expense->frequency] ?? $expense->frequency;
                                                ?>
                                            </span>
                                            <span class="next-execution">
                                                <?php _e('הבא:', 'budgex'); ?> <?php echo date('d/m/Y', strtotime($expense->next_execution)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="expense-actions">
                                        <div class="toggle-switch">
                                            <input type="checkbox" id="recurring-<?php echo $expense->id; ?>" <?php echo $expense->is_active ? 'checked' : ''; ?>>
                                            <label for="recurring-<?php echo $expense->id; ?>"></label>
                                        </div>
                                        <button class="action-btn edit-recurring-expense" data-expense-id="<?php echo $expense->id; ?>" title="<?php _e('עריכה', 'budgex'); ?>">
                                            <span class="dashicons dashicons-edit"></span>
                                        </button>
                                        <button class="action-btn delete-recurring-expense" data-expense-id="<?php echo $expense->id; ?>" title="<?php _e('מחיקה', 'budgex'); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($recurring_expenses)): ?>
                                <div class="empty-state">
                                    <span class="dashicons dashicons-backup"></span>
                                    <h4><?php _e('אין הוצאות חוזרות', 'budgex'); ?></h4>
                                    <p><?php _e('הגדר הוצאות חוזרות כמו שכר דירה, ביטוח וחשבונות קבועים', 'budgex'); ?></p>
                                    <button class="action-button primary" id="add-first-recurring-expense">
                                        <?php _e('הוסף הוצאה חוזרת', 'budgex'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Budget Management Tab -->
                <div class="tab-content" id="tab-budget-management">
                    <div class="budget-management-section">
                        <div class="section-header">
                            <h3><?php _e('ניהול תקציב', 'budgex'); ?></h3>
                            <p><?php _e('כלים מתקדמים לניהול ובקרה של התקציב שלך', 'budgex'); ?></p>
                        </div>

                        <div class="management-grid">
                            <!-- Budget Adjustment -->
                            <div class="management-card">
                                <div class="card-header">
                                    <span class="dashicons dashicons-chart-line"></span>
                                    <h4><?php _e('התאמת תקציב', 'budgex'); ?></h4>
                                </div>
                                <div class="card-content">
                                    <p><?php _e('הגדל או הקטן את התקציב הכולל', 'budgex'); ?></p>
                                    <form id="adjust-budget-form" class="inline-form">
                                        <div class="form-row">
                                            <input type="number" step="0.01" placeholder="סכום" id="budget-adjustment" required>
                                            <select id="adjustment-type">
                                                <option value="add"><?php _e('הוסף', 'budgex'); ?></option>
                                                <option value="subtract"><?php _e('הפחת', 'budgex'); ?></option>
                                            </select>
                                            <button type="submit" class="action-button primary">
                                                <?php _e('עדכן', 'budgex'); ?>
                                            </button>
                                        </div>
                                        <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                                    </form>
                                </div>
                            </div>

                            <!-- Data Export -->
                            <div class="management-card">
                                <div class="card-header">
                                    <span class="dashicons dashicons-download"></span>
                                    <h4><?php _e('ייצוא נתונים', 'budgex'); ?></h4>
                                </div>
                                <div class="card-content">
                                    <p><?php _e('ייצא את נתוני התקציב לקבצים שונים', 'budgex'); ?></p>
                                    <div class="export-buttons">
                                        <button class="action-button secondary" id="export-excel">
                                            <span class="dashicons dashicons-media-spreadsheet"></span>
                                            <?php _e('Excel', 'budgex'); ?>
                                        </button>
                                        <button class="action-button secondary" id="export-pdf">
                                            <span class="dashicons dashicons-media-document"></span>
                                            <?php _e('PDF', 'budgex'); ?>
                                        </button>
                                        <button class="action-button secondary" id="export-csv">
                                            <span class="dashicons dashicons-media-text"></span>
                                            <?php _e('CSV', 'budgex'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Categories -->
                            <div class="management-card">
                                <div class="card-header">
                                    <span class="dashicons dashicons-category"></span>
                                    <h4><?php _e('ניהול קטגוריות', 'budgex'); ?></h4>
                                </div>
                                <div class="card-content">
                                    <p><?php _e('נהל וערוך קטגוריות הוצאות', 'budgex'); ?></p>
                                    <button class="action-button secondary" id="manage-categories">
                                        <?php _e('נהל קטגוריות', 'budgex'); ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Budget Reset -->
                            <?php if ($user_role === 'owner'): ?>
                                <div class="management-card danger-card">
                                    <div class="card-header">
                                        <span class="dashicons dashicons-warning"></span>
                                        <h4><?php _e('איפוס תקציב', 'budgex'); ?></h4>
                                    </div>
                                    <div class="card-content">
                                        <p><?php _e('איפוס כל הנתונים והתחלה מחדש', 'budgex'); ?></p>
                                        <button class="action-button danger" id="reset-budget">
                                            <?php _e('איפוס תקציב', 'budgex'); ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Users Tab (Owner only) -->
            <?php if ($user_role === 'owner'): ?>
                <div class="tab-content" id="tab-users">
                    <div class="users-management">
                        <div class="section-header">
                            <h3><?php _e('ניהול משתמשים', 'budgex'); ?></h3>
                            <button class="action-button primary" id="invite-user">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הזמן משתמש', 'budgex'); ?>
                            </button>
                        </div>

                        <!-- Current Users -->
                        <div class="users-section">
                            <h4><?php _e('משתמשים משותפים', 'budgex'); ?></h4>
                            <div class="users-grid">
                                <?php foreach ($shared_users as $user): ?>
                                    <div class="user-card" data-user-id="<?php echo $user->user_id; ?>">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user->display_name, 0, 1)); ?>
                                        </div>
                                        <div class="user-info">
                                            <h5 class="user-name"><?php echo esc_html($user->display_name); ?></h5>
                                            <span class="user-email"><?php echo esc_html($user->user_email); ?></span>
                                            <span class="user-role <?php echo $user->role; ?>">
                                                <?php 
                                                $roles = array(
                                                    'admin' => 'מנהל',
                                                    'editor' => 'עורך',
                                                    'viewer' => 'צופה'
                                                );
                                                echo $roles[$user->role] ?? $user->role;
                                                ?>
                                            </span>
                                        </div>
                                        <div class="user-actions">
                                            <select class="role-selector" data-user-id="<?php echo $user->user_id; ?>">
                                                <option value="viewer" <?php selected($user->role, 'viewer'); ?>><?php _e('צופה', 'budgex'); ?></option>
                                                <option value="editor" <?php selected($user->role, 'editor'); ?>><?php _e('עורך', 'budgex'); ?></option>
                                                <option value="admin" <?php selected($user->role, 'admin'); ?>><?php _e('מנהל', 'budgex'); ?></option>
                                            </select>
                                            <button class="action-btn remove-user" data-user-id="<?php echo $user->user_id; ?>" title="<?php _e('הסר גישה', 'budgex'); ?>">
                                                <span class="dashicons dashicons-dismiss"></span>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Pending Invitations -->
                        <?php if (!empty($pending_invitations)): ?>
                            <div class="invitations-section">
                                <h4><?php _e('הזמנות ממתינות', 'budgex'); ?></h4>
                                <div class="invitations-list">
                                    <?php foreach ($pending_invitations as $invitation): ?>
                                        <div class="invitation-item" data-invitation-id="<?php echo $invitation->id; ?>">
                                            <div class="invitation-info">
                                                <span class="invitation-email"><?php echo esc_html($invitation->email); ?></span>
                                                <span class="invitation-role"><?php echo $invitation->role; ?></span>
                                                <span class="invitation-date"><?php echo date('d/m/Y', strtotime($invitation->created_at)); ?></span>
                                            </div>
                                            <div class="invitation-actions">
                                                <button class="action-btn resend-invitation" data-invitation-id="<?php echo $invitation->id; ?>" title="<?php _e('שלח שוב', 'budgex'); ?>">
                                                    <span class="dashicons dashicons-email-alt"></span>
                                                </button>
                                                <button class="action-btn cancel-invitation" data-invitation-id="<?php echo $invitation->id; ?>" title="<?php _e('בטל הזמנה', 'budgex'); ?>">
                                                    <span class="dashicons dashicons-no"></span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reports Tab -->
            <div class="tab-content" id="tab-reports">
                <div class="reports-section">
                    <div class="section-header">
                        <h3><?php _e('דוחות ואנליטיקה', 'budgex'); ?></h3>
                        <div class="report-controls">
                            <select id="report-period" class="report-select">
                                <option value="month"><?php _e('החודש הנוכחי', 'budgex'); ?></option>
                                <option value="quarter"><?php _e('הרבעון הנוכחי', 'budgex'); ?></option>
                                <option value="year"><?php _e('השנה הנוכחית', 'budgex'); ?></option>
                                <option value="custom"><?php _e('תקופה מותאמת', 'budgex'); ?></option>
                            </select>
                            <button class="action-button secondary" id="generate-report">
                                <?php _e('צור דוח', 'budgex'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="reports-grid">
                        <!-- Spending Overview -->
                        <div class="report-card">
                            <div class="card-header">
                                <h4><?php _e('סקירת הוצאות', 'budgex'); ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="chart-container">
                                    <canvas id="spending-overview-chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Category Breakdown -->
                        <div class="report-card">
                            <div class="card-header">
                                <h4><?php _e('פירוט לפי קטגוריות', 'budgex'); ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="chart-container">
                                    <canvas id="category-breakdown-chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Trends Analysis -->
                        <div class="report-card full-width">
                            <div class="card-header">
                                <h4><?php _e('ניתוח מגמות', 'budgex'); ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="trends-container">
                                    <div class="trend-item">
                                        <span class="trend-label"><?php _e('מגמת הוצאות', 'budgex'); ?></span>
                                        <span class="trend-value <?php echo $spending_trend >= 0 ? 'positive' : 'negative'; ?>">
                                            <?php echo $spending_trend >= 0 ? '+' : ''; ?><?php echo number_format($spending_trend, 1); ?>%
                                        </span>
                                    </div>
                                    <!-- Add more trend analysis here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    
    <!-- Quick Add Outcome Modal -->
    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
        <div id="quick-add-outcome-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php _e('הוסף הוצאה מהירה', 'budgex'); ?></h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="quick-add-outcome-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="quick_outcome_amount"><?php _e('סכום', 'budgex'); ?></label>
                                <input type="number" step="0.01" id="quick_outcome_amount" name="amount" required>
                            </div>
                            <div class="form-group">
                                <label for="quick_outcome_category"><?php _e('קטגוריה', 'budgex'); ?></label>
                                <input type="text" id="quick_outcome_category" name="category" list="categories" required>
                                <datalist id="categories">
                                    <?php 
                                    $categories = array_unique(array_column($outcomes, 'category'));
                                    foreach ($categories as $category): 
                                    ?>
                                        <option value="<?php echo esc_attr($category); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="quick_outcome_date"><?php _e('תאריך', 'budgex'); ?></label>
                                <input type="date" id="quick_outcome_date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quick_outcome_description"><?php _e('תיאור', 'budgex'); ?></label>
                            <textarea id="quick_outcome_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="action-button primary">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף הוצאה', 'budgex'); ?>
                            </button>
                            <button type="button" class="action-button secondary modal-close">
                                <?php _e('ביטול', 'budgex'); ?>
                            </button>
                        </div>
                        <input type="hidden" name="budget_id" value="<?php echo esc_attr($budget->id); ?>">
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p><?php _e('טוען...', 'budgex'); ?></p>
        </div>
    </div>
</div>

<!-- JavaScript Data -->
<script type="text/javascript">
    window.budgexEnhancedData = {
        budgetId: <?php echo $budget->id; ?>,
        userRole: '<?php echo esc_js($user_role); ?>',
        currency: '<?php echo esc_js($budget->currency); ?>',
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('budgex_public_nonce'); ?>',
        strings: {
            confirmDelete: '<?php _e('האם אתה בטוח שברצונך למחוק?', 'budgex'); ?>',
            loading: '<?php _e('טוען...', 'budgex'); ?>',
            error: '<?php _e('שגיאה', 'budgex'); ?>',
            success: '<?php _e('הצלחה', 'budgex'); ?>',
            addOutcome: '<?php _e('הוסף הוצאה', 'budgex'); ?>',
            editOutcome: '<?php _e('ערוך הוצאה', 'budgex'); ?>',
            deleteOutcome: '<?php _e('מחק הוצאה', 'budgex'); ?>',
            inviteUser: '<?php _e('הזמן משתמש', 'budgex'); ?>',
            manageUsers: '<?php _e('נהל משתמשים', 'budgex'); ?>'
        },
        data: {
            monthlyBreakdown: <?php echo json_encode($monthly_breakdown); ?>,
            topCategories: <?php echo json_encode($top_categories); ?>,
            outcomes: <?php echo json_encode($outcomes); ?>,
            futureExpenses: <?php echo json_encode($future_expenses); ?>,
            recurringExpenses: <?php echo json_encode($recurring_expenses); ?>,
            sharedUsers: <?php echo json_encode($shared_users); ?>
        }
    };
    
    // Initialize enhanced budget when document is ready
    jQuery(document).ready(function($) {
        console.log('Complete Public Enhanced Budget Page Loaded');
        
        // Initialize enhanced budget features
        if (typeof window.initializeEnhancedBudgetPage === 'function') {
            window.initializeEnhancedBudgetPage();
        } else {
            // Fallback initialization if enhanced JS not loaded
            initializeBasicFunctionality();
        }
        
        // Initialize charts
        initializeCharts();
        
        // Initialize tab functionality
        initializeTabNavigation();
        
        // Initialize modals
        initializeModals();
        
        // Initialize form handlers
        initializeFormHandlers();
    });
    
    // Basic functionality fallback
    function initializeBasicFunctionality() {
        // Tab switching
        $('.tab-button').on('click', function() {
            var tabId = $(this).data('tab');
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').removeClass('active');
            $('#tab-' + tabId).addClass('active');
        });
        
        // Modal controls
        $('.modal-close').on('click', function() {
            $(this).closest('.modal-overlay').hide();
        });
        
        $('#quick-add-outcome').on('click', function() {
            $('#quick-add-outcome-modal').show();
        });
        
        // Basic form submission
        $('#quick-add-outcome-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            formData += '&action=budgex_add_outcome&nonce=' + window.budgexEnhancedData.nonce;
            
            $.post(window.budgexEnhancedData.ajaxUrl, formData, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('שגיאה בהוספת ההוצאה');
                }
            });
        });
    }
    
    // Chart initialization
    function initializeCharts() {
        if (typeof Chart !== 'undefined') {
            // Monthly breakdown chart
            var ctx = document.getElementById('monthly-breakdown-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode(array_column($monthly_breakdown, 'month')); ?>,
                        datasets: [{
                            label: 'הוצאות חודשיות',
                            data: <?php echo json_encode(array_column($monthly_breakdown, 'amount')); ?>,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }
    }
    
    // Tab navigation
    function initializeTabNavigation() {
        $('.tab-button').on('click', function() {
            var tabId = $(this).data('tab');
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').removeClass('active');
            $('#tab-' + tabId).addClass('active');
        });
    }
    
    // Modal functionality
    function initializeModals() {
        $('.modal-close').on('click', function() {
            $(this).closest('.modal-overlay').hide();
        });
        
        $('#quick-add-outcome, #quick-add-expense').on('click', function() {
            $('#quick-add-outcome-modal').show();
        });
    }
    
    // Form handlers
    function initializeFormHandlers() {
        // Quick add outcome form
        $('#quick-add-outcome-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'budgex_add_outcome',
                nonce: window.budgexEnhancedData.nonce,
                budget_id: window.budgexEnhancedData.budgetId,
                amount: $('#quick_outcome_amount').val(),
                category: $('#quick_outcome_category').val(),
                description: $('#quick_outcome_description').val(),
                date: $('#quick_outcome_date').val()
            };
            
            $('#loading-overlay').show();
            
            $.post(window.budgexEnhancedData.ajaxUrl, formData, function(response) {
                $('#loading-overlay').hide();
                
                if (response.success) {
                    $('#quick-add-outcome-modal').hide();
                    location.reload(); // Refresh to show new outcome
                } else {
                    alert(response.data || 'שגיאה בהוספת ההוצאה');
                }
            }).fail(function() {
                $('#loading-overlay').hide();
                alert('שגיאה בשרת');
            });
        });
        
        // Budget adjustment form
        $('#adjust-budget-form').on('submit', function(e) {
            e.preventDefault();
            
            var adjustment = $('#budget-adjustment').val();
            var type = $('#adjustment-type').val();
            
            var formData = {
                action: 'budgex_adjust_budget',
                nonce: window.budgexEnhancedData.nonce,
                budget_id: window.budgexEnhancedData.budgetId,
                adjustment: adjustment,
                type: type
            };
            
            $('#loading-overlay').show();
            
            $.post(window.budgexEnhancedData.ajaxUrl, formData, function(response) {
                $('#loading-overlay').hide();
                
                if (response.success) {
                    location.reload(); // Refresh to show updated budget
                } else {
                    alert(response.data || 'שגיאה בעדכון התקציב');
                }
            }).fail(function() {
                $('#loading-overlay').hide();
                alert('שגיאה בשרת');
            });
        });
    }
</script>
